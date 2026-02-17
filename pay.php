<?php
session_start();
include 'connectdb.php';

// ย้าย bootstrap และ header ไปไว้ด้านล่างสุด
// include 'bootstrap.php'; 
// include 'header.php'; 

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ตรวจสอบการ Login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

/* =========================================
   ส่วนที่ 1: ประมวลผลเมื่อกดปุ่ม "ยืนยันการสั่งซื้อ"
========================================= */
if(isset($_POST['confirm_order'])){

    if(empty($_POST['payment_method'])){
        echo "<script>alert('กรุณาเลือกช่องทางการชำระเงิน');window.history.back();</script>";
        exit();
    }

    $payment_method = $_POST['payment_method'];

    // -----------------------------------------------------------
    // [แก้ไขใหม่] 1. ตรวจสอบที่อยู่ก่อน
    // -----------------------------------------------------------
    $sql_check_addr = "SELECT address_id FROM addresses WHERE user_id = ? ORDER BY address_id DESC LIMIT 1";
    $stmt_check = $conn->prepare($sql_check_addr);
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if($res_check->num_rows == 0){
        echo "<script>alert('กรุณาเพิ่มที่อยู่จัดส่งก่อนทำการสั่งซื้อ'); window.location='address.php';</script>";
        exit();
    }

    $addr_row_check = $res_check->fetch_assoc();
    $address_id = $addr_row_check['address_id'];
    // -----------------------------------------------------------


    // 1.1 ดึงสินค้าในตะกร้า
    $sql_cart = "SELECT c.product_id, c.quantity, p.p_price 
                 FROM cart c 
                 JOIN products p ON c.product_id = p.p_id 
                 WHERE c.user_id = ?";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $cart = $stmt_cart->get_result();

    if($cart->num_rows == 0){
        echo "<script>alert('ไม่มีสินค้าในตะกร้า');window.location='index.php';</script>";
        exit();
    }

    $total_price = 0;
    $items = [];
    while($row = $cart->fetch_assoc()){
        $subtotal = $row['p_price'] * $row['quantity'];
        $total_price += $subtotal;
        $items[] = $row;
    }
    
    $shipping_cost = 60; 
    $final_total = $total_price + $shipping_cost;

    // 1.2 สร้าง Order
    $stmt_order = $conn->prepare("
        INSERT INTO orders 
        (u_id, address_id, total_price, status, o_date, payment_method) 
        VALUES (?, ?, ?, 'รอชำระเงิน', NOW(), ?)
    ");
    
    if(!$stmt_order){
        die("Prepare Error (Order): " . $conn->error);
    }

    $stmt_order->bind_param("iids", $user_id, $address_id, $final_total, $payment_method);
    
    if(!$stmt_order->execute()){
        die("Execute Error (Order): " . $stmt_order->error);
    }
    
    $order_id = $stmt_order->insert_id;

    // 1.3 บันทึก Order Details
    $stmt_detail = $conn->prepare("INSERT INTO order_details (o_id, p_id, q_ty, price) VALUES (?, ?, ?, ?)");
    foreach($items as $item){
        $stmt_detail->bind_param("iiid", 
            $order_id, 
            $item['product_id'], 
            $item['quantity'], 
            $item['p_price']
        );
        $stmt_detail->execute();
    }

    // 1.4 ล้างตะกร้า
    $stmt_clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt_clear->bind_param("i", $user_id);
    $stmt_clear->execute();

    // 1.5 เปลี่ยนหน้า
    if ($payment_method == 'cod') {
        header("Location: orderdetail.php?id=".$order_id."&success=1");
    } else {
        header("Location: qrcode.php?id=".$order_id);
    }
    exit();
}

/* =========================================
   ส่วนที่ 2: ดึงข้อมูลเพื่อแสดงผลหน้าเว็บ
========================================= */
include 'bootstrap.php'; 
include 'header.php'; 

// 2.1 ดึงที่อยู่มาแสดง
$sql_addr = "SELECT * FROM addresses WHERE user_id = ? ORDER BY address_id DESC LIMIT 1";
$stmt_addr = $conn->prepare($sql_addr);
$stmt_addr->bind_param("i", $user_id);
$stmt_addr->execute();
$res_addr = $stmt_addr->get_result();
$addr_row = $res_addr->fetch_assoc();

$show_fullname = 'ไม่ระบุชื่อ';
$show_phone = '-';
$show_address = '';
$has_address = false;

if ($addr_row) {
    $show_fullname = $addr_row['fullname'];
    $show_phone = $addr_row['phone'];
    $show_address = $addr_row['address'] . ' ' . 
                    'ต.' . $addr_row['district'] . ' ' . 
                    'จ.' . $addr_row['province'] . ' ' . 
                    $addr_row['postal_code'];
    $has_address = true;
} else {
    // Fallback: ดึงชื่อจาก users (แก้ชื่อคอลัมน์ user_id -> id)
    $sql_u = "SELECT fullname FROM users WHERE id = ?"; 
    $stmt_u = $conn->prepare($sql_u);
    
    if($stmt_u){
        $stmt_u->bind_param("i", $user_id);
        $stmt_u->execute();
        $res_u = $stmt_u->get_result();
        if($u_row = $res_u->fetch_assoc()){
            $show_fullname = $u_row['fullname'];
        }
    }
}

// 2.2 ดึงสินค้าในตะกร้า
$sql_view = "
    SELECT c.quantity, p.p_name, p.p_price,
           (SELECT img_path FROM product_images WHERE p_id = p.p_id LIMIT 1) AS p_img
    FROM cart c
    JOIN products p ON c.product_id = p.p_id
    WHERE c.user_id = ?
";
$stmt_products = $conn->prepare($sql_view);
$stmt_products->bind_param("i", $user_id);
$stmt_products->execute();
$cart_result = $stmt_products->get_result();

$total = 0;
$shipping = 60; 
?>

<style>
body{background:#f5f5f5;font-family:'Kanit',sans-serif;}
.pay-container{ max-width: 900px; margin: 0 auto; padding: 20px;}
.card{ background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 20px;}
.summary-item{ display: flex; justify-content: space-between; margin-bottom: 10px; }
.total-price{ font-size: 1.2rem; font-weight: bold; color: #ff7a00; }
.btn-confirm{ 
    background: #ff7a00; color: #fff; width: 100%; padding: 12px; 
    border: none; border-radius: 5px; font-size: 1.1rem; cursor: pointer;
}
.btn-confirm:hover{ background: #e66e00; }
.btn-confirm:disabled{ background: #ccc; cursor: not-allowed; }
.payment-option{ margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
.item-row { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
.address-box { border-left: 4px solid #ff7a00; background: #fff8f0; padding: 15px; border-radius: 5px; }
.edit-addr-btn { float: right; font-size: 0.9rem; text-decoration: none; color: #ff7a00; }
</style>

<div class="pay-container py-5">
    <h2 class="mb-4 text-center">ยืนยันการสั่งซื้อ</h2>

    <div class="row">
        <div class="col-md-7">
            
            <div class="card mb-3">
                <h4 class="mb-3">
                    ที่อยู่จัดส่ง
                    <a href="address.php" class="edit-addr-btn">จัดการที่อยู่</a>
                </h4>
                
                <?php if($has_address): ?>
                    <div class="address-box">
                        <strong><?= htmlspecialchars($show_fullname) ?></strong>
                        <span class="text-muted ms-2">(<?= htmlspecialchars($show_phone) ?>)</span>
                        <p class="mb-0 mt-2 text-secondary">
                            <?= htmlspecialchars($show_address) ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mt-3 mb-0 text-center">
                        <p class="mb-2">⚠️ คุณยังไม่มีที่อยู่จัดส่ง</p>
                        <a href="address.php" class="btn btn-sm btn-outline-primary">เพิ่มที่อยู่จัดส่งใหม่</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h4 class="mb-3">รายการสินค้า</h4>
                <?php if($cart_result->num_rows > 0): ?>
                    <?php while($row = $cart_result->fetch_assoc()): 
                        $subtotal = $row['p_price'] * $row['quantity'];
                        $total += $subtotal; 
                        $img = !empty($row['p_img']) ? $row['p_img'] : 'https://placehold.co/100x100?text=No+Image';
                    ?>
                    <div class="d-flex justify-content-between align-items-center item-row">
                        <div style="display:flex; gap:15px; align-items:center;">
                            <img src="<?= htmlspecialchars($img) ?>" 
                                 style="width:60px; height:60px; object-fit:cover; border-radius:5px;">
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($row['p_name']) ?></h6>
                                <small class="text-muted">จำนวน: <?= $row['quantity'] ?> ชิ้น</small>
                            </div>
                        </div>
                        <div class="fw-bold">฿<?= number_format($subtotal, 0) ?></div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <p>ไม่มีสินค้าในตะกร้า</p>
                        <a href="index.php" class="btn btn-sm btn-outline-secondary">ไปเลือกซื้อสินค้า</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-5">
            <form action="pay.php" method="POST">
                
                <div class="card">
                    <h4 class="mb-3">สรุปยอดชำระ</h4>
                    <div class="summary-item">
                        <span>ราคาสินค้า</span>
                        <span>฿<?= number_format($total, 0) ?></span>
                    </div>
                    <div class="summary-item">
                        <span>ค่าจัดส่ง</span>
                        <span>฿<?= number_format($shipping, 0) ?></span>
                    </div>
                    <hr>
                    <div class="summary-item">
                        <strong>ยอดสุทธิ</strong>
                        <strong class="total-price">฿<?= number_format($total + $shipping, 0) ?></strong>
                    </div>
                </div>

                <div class="card">
                    <h4 class="mb-3">เลือกวิธีการชำระเงิน</h4>
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="transfer" id="pay1" required checked>
                        <label for="pay1" class="ms-2">โอนเงินผ่านธนาคาร</label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="cod" id="pay2">
                        <label for="pay2" class="ms-2">เก็บเงินปลายทาง</label>
                    </div>
                </div>

                <button type="submit" name="confirm_order" class="btn-confirm" 
                    <?= ($total == 0) ? 'disabled' : '' ?>>
                    ยืนยันการสั่งซื้อ
                </button>
                
                <?php if(!$has_address): ?>
                    <div class="text-center text-danger mt-2 small">* กรุณาเพิ่มที่อยู่จัดส่งก่อนยืนยัน</div>
                <?php endif; ?>
                
                <a href="cart.php" class="d-block text-center mt-3 text-muted" style="text-decoration:none;">
                    &lt; ย้อนกลับไปแก้ไขตะกร้า
                </a>

            </form>
        </div>
    </div>
</div>

</body>
</html>