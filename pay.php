<?php
session_start();
include 'connectdb.php';

// แสดง Error เพื่อช่วยในการตรวจสอบ
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ตรวจสอบการล็อกอิน
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

/* ===============================
   เมื่อกดชำระสินค้า
================================ */
if(isset($_POST['confirm_order'])){

    if(empty($_POST['payment_method'])){
        die("<script>alert('กรุณาเลือกช่องทางการชำระเงิน'); window.history.back();</script>");
    }

    $payment_method = $_POST['payment_method'];
    $total_price    = floatval($_POST['total_price']);

    // 1. สร้างคำสั่งซื้อในตาราง orders
    $stmt = $conn->prepare("INSERT INTO orders 
        (user_id, total_price, payment_method, status, order_date)
        VALUES (?, ?, ?, 'รอชำระเงิน', NOW())");
    $stmt->bind_param("ids", $user_id, $total_price, $payment_method);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // 2. ดึงสินค้าจากตะกร้าในฐานข้อมูล
    $stmt_cart = $conn->prepare("SELECT product_id, quantity FROM cart WHERE user_id = ?");
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $cart = $stmt_cart->get_result();

    // 3. ย้ายรายการสินค้าไปตาราง order_items
    while($row = $cart->fetch_assoc()){
        $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt2->bind_param("iii", $order_id, $row['product_id'], $row['quantity']);
        $stmt2->execute();
    }

    // 4. ลบสินค้าออกจากตะกร้าเมื่อสั่งซื้อสำเร็จ
    $stmt_del = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt_del->bind_param("i", $user_id);
    $stmt_del->execute();

    header("Location: orderdetail.php?id=$order_id&success=1");
    exit();
}

/* ===============================
   ดึงข้อมูลสินค้าในตะกร้า (ปรับชื่อคอลัมน์ให้ตรงกับตาราง products จริง)
================================ */
$stmt_products = $conn->prepare("
    SELECT c.*, 
           p.p_name, 
           p.p_price,
           (
                SELECT img_path 
                FROM product_images 
                WHERE p_id = p.p_id 
                LIMIT 1
           ) AS p_img
    FROM cart c
    JOIN products p ON c.product_id = p.p_id
    WHERE c.user_id = ?
");


// แก้ไขจาก $u_id เป็น $user_id
if ($stmt_products) {
    $stmt_products->bind_param("i", $user_id);
    $stmt_products->execute();
    $cart_result = $stmt_products->get_result();
} else {
    die("SQL Error: " . $conn->error);
}

$total = 0;
$shipping = 60;
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ชำระสินค้า - 2M3WM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{ background:#f4f4f4; font-family: 'Segoe UI', sans-serif; }
        .header{ background:#111; color:#fff; padding:18px; text-align:center; font-size:22px; }
        .box{ background:#fff; padding:25px; margin-top:20px; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.05); }
        .product-img{ width:80px; height:80px; object-fit:cover; border-radius:6px; }
        .total{ font-size:28px; font-weight:bold; color:#ff6a00; }
        .btn-order{ background:#ff6a00; color:#fff; padding:12px 45px; border:none; border-radius:8px; font-weight:bold; }
        .btn-order:hover{ background:#e55d00; }
        .payment-box{ border:1px solid #ccc; border-radius:10px; padding:15px; margin-bottom:12px; cursor:pointer; }
        .payment-active{ border:2px solid #ff6a00 !important; background:#fff6ef; }
    </style>
</head>
<body>

<div class="header">CHECKOUT</div>

<div class="container mb-5">
    <form method="POST">
        <div class="box">
            <h5 class="mb-4">รายการสินค้า</h5>
            <?php if($cart_result->num_rows == 0): ?>
                <div class="text-danger text-center">ไม่มีสินค้าในตะกร้า</div>
            <?php else: ?>
                <?php while($row = $cart_result->fetch_assoc()): 
                    $subtotal = $row['p_price'] * $row['quantity']; 
                    $total += $subtotal;
                ?>
                <div class="border-bottom pb-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <img src="<?= htmlspecialchars($row['p_img']) ?>" class="product-img">
                        </div>
                        <div class="col-md-6">
                            <div class="fw-bold"><?= htmlspecialchars($row['p_name']) ?></div>
                            <small class="text-muted">จำนวน: <?= $row['quantity'] ?></small>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="fw-bold text-orange">฿<?= number_format($subtotal, 2) ?></span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <div class="box">
            <h5 class="mb-4">ช่องทางการชำระเงิน</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="payment-box payment-active d-block">
                        <input type="radio" name="payment_method" value="QR พร้อมเพย์" checked> QR พร้อมเพย์
                    </label>
                </div>
                <div class="col-md-6">
                    <label class="payment-box d-block">
                        <input type="radio" name="payment_method" value="เก็บเงินปลายทาง"> เก็บเงินปลายทาง (COD)
                    </label>
                </div>
            </div>
        </div>

        <?php $grand_total = $total + $shipping; ?>
        <div class="box">
            <div class="d-flex justify-content-between mb-2">
                <span>รวมสินค้า</span>
                <span>฿<?= number_format($total, 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>ค่าจัดส่ง</span>
                <span>฿<?= number_format($shipping, 2) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <div class="total">฿<?= number_format($grand_total, 2) ?></div>
                <input type="hidden" name="total_price" value="<?= $grand_total ?>">
                <button type="submit" name="confirm_order" class="btn-order shadow-sm">ชำระสินค้า</button>
            </div>
        </div>
    </form>
</div>

<script>
    const paymentOptions = document.querySelectorAll('.payment-box');
    paymentOptions.forEach(box => {
        box.addEventListener('click', function(){
            paymentOptions.forEach(b => b.classList.remove('payment-active'));
            this.classList.add('payment-active');
            this.querySelector('input').checked = true;
        });
    });
</script>

</body>
</html>