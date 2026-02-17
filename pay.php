<?php
session_start();
include 'connectdb.php';
include 'bootstrap.php'; // เอาออกชั่วคราวถ้าไม่ได้ใช้ หรือใส่ไว้ถ้าจำเป็นต้องโหลด CSS

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
    
    // ค่าส่ง
    $shipping_cost = 60; 
    $final_total = $total_price + $shipping_cost;

    // 1.2 สร้าง Order
    $stmt_order = $conn->prepare("INSERT INTO orders (u_id, total_price, status, o_date) VALUES (?, ?, 'รอชำระเงิน', NOW())");
    $stmt_order->bind_param("id", $user_id, $final_total);
    
    if(!$stmt_order->execute()){
        die("Error creating order: " . $conn->error);
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

    // 1.5 ไปหน้าสำเร็จ
    header("Location: orderdetail.php?id=".$order_id."&success=1");
    exit();
}

/* =========================================
   ส่วนที่ 2: ดึงข้อมูลเพื่อแสดงผลหน้าเว็บ
========================================= */
// ตรวจสอบ Query ว่าถูกต้องหรือไม่
$sql_view = "
    SELECT c.quantity, p.p_name, p.p_price,
           (SELECT img_path FROM product_images WHERE p_id = p.p_id LIMIT 1) AS p_img
    FROM cart c
    JOIN products p ON c.product_id = p.p_id
    WHERE c.user_id = ?
";

$stmt_products = $conn->prepare($sql_view);

if(!$stmt_products){
    // ถ้า SQL ผิดพลาดจะแสดงข้อความตรงนี้
    die("SQL Error: " . $conn->error); 
}

$stmt_products->bind_param("i", $user_id);
$stmt_products->execute();
$cart_result = $stmt_products->get_result();

// ตัวแปรสำหรับคำนวณยอดรวม
$total = 0;
$shipping = 60; 
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน - 2M3WM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body{background:#f5f5f5;font-family:'Kanit',sans-serif;}
        .container{ max-width: 900px; margin: 0 auto; padding: 20px;}
        .card{ background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 20px;}
        .summary-item{ display: flex; justify-content: space-between; margin-bottom: 10px; }
        .total-price{ font-size: 1.2rem; font-weight: bold; color: #ff7a00; }
        .btn-confirm{ 
            background: #ff7a00; color: #fff; width: 100%; padding: 12px; 
            border: none; border-radius: 5px; font-size: 1.1rem; cursor: pointer;
        }
        .btn-confirm:hover{ background: #e66e00; }
        .payment-option{ margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .item-row { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>

    <?php include("header.php"); ?>

    <div class="container py-5">
        <h2 class="mb-4 text-center">ยืนยันการสั่งซื้อ</h2>

        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <h4 class="mb-3">รายการสินค้า</h4>
                    
                    <?php if($cart_result->num_rows > 0): ?>
                        <?php 
                        // วนลูปแสดงข้อมูลสินค้า
                        while($row = $cart_result->fetch_assoc()): 
                            $subtotal = $row['p_price'] * $row['quantity'];
                            $total += $subtotal; // บวกยอดรวมที่นี่
                            
                            // เช็ครูปภาพ ถ้าไม่มีให้ใช้รูป placeholder
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

                    <button type="submit" name="confirm_order" class="btn-confirm" <?= ($total == 0) ? 'disabled' : '' ?>>
                        ยืนยันการสั่งซื้อ
                    </button>
                    
                    <a href="cart.php" class="d-block text-center mt-3 text-muted" style="text-decoration:none;">
                        &lt; ย้อนกลับไปแก้ไขตะกร้า
                    </a>

                </form>
            </div>
        </div>
    </div>

</body>
</html>