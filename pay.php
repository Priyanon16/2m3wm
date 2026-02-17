<?php
session_start();
include 'connectdb.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

/* ===============================
   เมื่อกดปุ่ม "ยืนยันการชำระเงิน" (Process Order)
================================ */
if(isset($_POST['confirm_order'])){

    if(empty($_POST['payment_method'])){
        echo "<script>alert('กรุณาเลือกช่องทางการชำระเงิน');window.history.back();</script>";
        exit();
    }

    // 1. ดึงสินค้าในตะกร้ามาตรวจสอบอีกครั้งเพื่อความชัวร์
    $stmt_cart = $conn->prepare("
        SELECT c.product_id, c.quantity, p.p_price
        FROM cart c
        JOIN products p ON c.product_id = p.p_id
        WHERE c.user_id = ?
    ");
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $cart = $stmt_cart->get_result();

    if($cart->num_rows == 0){
        die("ไม่มีสินค้าในตะกร้า");
    }

    $total_price = 0;
    $items = [];

    while($row = $cart->fetch_assoc()){
        $subtotal = $row['p_price'] * $row['quantity'];
        $total_price += $subtotal;
        $items[] = $row;
    }
    
    // บวกค่าส่ง (ถ้ามี)
    $shipping_cost = 60; 
    $final_total = $total_price + $shipping_cost;

    // 2. สร้าง Order หลัก
    $stmt_order = $conn->prepare("
        INSERT INTO orders (u_id, total_price, status, o_date)
        VALUES (?, ?, 'รอชำระเงิน', NOW())
    ");
    $stmt_order->bind_param("id", $user_id, $final_total);
    $stmt_order->execute();
    $order_id = $stmt_order->insert_id;

    // 3. บันทึก Order Details
    $stmt_detail = $conn->prepare("
        INSERT INTO order_details (o_id, p_id, q_ty, price)
        VALUES (?, ?, ?, ?)
    ");

    foreach($items as $item){
        $stmt_detail->bind_param("iiid", 
            $order_id, 
            $item['product_id'], 
            $item['quantity'], 
            $item['p_price']
        );
        $stmt_detail->execute();
    }

    // 4. ล้างตะกร้า
    $stmt_clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt_clear->bind_param("i", $user_id);
    $stmt_clear->execute();

    // 5. ส่งไปหน้าแสดงรายละเอียดออเดอร์ (ต้องมีไฟล์ orderdetail.php รองรับ)
    header("Location: orderdetail.php?id=".$order_id."&success=1");
    exit();
}

/* ===============================
   ดึงข้อมูลสินค้ามาแสดงผล (View Page)
================================ */
$stmt_products = $conn->prepare("
    SELECT c.*, p.p_name, p.p_price,
           (SELECT img_path FROM product_images WHERE p_id = p.p_id LIMIT 1) AS p_img
    FROM cart c
    JOIN products p ON c.product_id = p.p_id
    WHERE c.user_id = ?
");
$stmt_products->bind_param("i", $user_id);
$stmt_products->execute();
$cart_result = $stmt_products->get_result();

$total = 0;
$shipping = 60; // ค่าส่ง
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน - 2M3WM</title>
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
    </style>
</head>
<body>

    <?php include("header.php"); ?>

    <div class="container py-5">
        <h2 class="mb-4 text-center">ยืนยันการสั่งซื้อ</h2>

        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <h4>รายการสินค้า</h4>
                    <hr>
                    <?php if($cart_result->num_rows > 0): ?>
                        <?php while($row = $cart_result->fetch_assoc()): 
                            $subtotal = $row['p_price'] * $row['quantity'];
                            $total += $subtotal;
                        ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div style="display:flex; gap:10px; align-items:center;">
                                <img src="<?= !empty($row['p_img']) ? $row['p_img'] : 'images/no-image.png' ?>" 
                                     style="width:50px; height:50px; object-fit:cover; border-radius:5px;">
                                <div>
                                    <div><?= htmlspecialchars($row['p_name']) ?></div>
                                    <small class="text-muted">x <?= $row['quantity'] ?></small>
                                </div>
                            </div>
                            <div>฿<?= number_format($subtotal, 0) ?></div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>ไม่มีสินค้าในตะกร้า</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-5">
                <form action="pay.php" method="POST">
                    
                    <div class="card">
                        <h4>สรุปยอดชำระ</h4>
                        <hr>
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
                        <h4>เลือกวิธีการชำระเงิน</h4>
                        <div class="payment-option">
                            <input type="radio" name="payment_method" value="transfer" id="pay1" required checked>
                            <label for="pay1">โอนเงินผ่านธนาคาร</label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" name="payment_method" value="cod" id="pay2">
                            <label for="pay2">เก็บเงินปลายทาง</label>
                        </div>
                    </div>

                    <button type="submit" name="confirm_order" class="btn-confirm">
                        ยืนยันการสั่งซื้อ
                    </button>
                    
                    <a href="cart.php" class="d-block text-center mt-3 text-muted">ย้อนกลับไปตะกร้า</a>

                </form>
            </div>
        </div>
    </div>

</body>
</html>