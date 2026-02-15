<?php
include 'connectdb.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// =========================
// เมื่อกดชำระสินค้า
// =========================
if(isset($_POST['confirm_order'])){

    $payment_method = $_POST['payment_method'];
    $total_price    = $_POST['total_price'];

    // สร้างคำสั่งซื้อ
    $stmt = $conn->prepare("INSERT INTO orders 
        (user_id,total_price,payment_method,status,order_date) 
        VALUES (?,?,?,'รอชำระเงิน',NOW())");
    $stmt->bind_param("ids",$user_id,$total_price,$payment_method);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // ย้ายสินค้า cart → order_items
    $cart = $conn->query("SELECT * FROM cart WHERE user_id='$user_id'");
    while($row = $cart->fetch_assoc()){

        $stmt2 = $conn->prepare("INSERT INTO order_items 
            (order_id,product_id,quantity) VALUES (?,?,?)");
        $stmt2->bind_param("iii",$order_id,$row['product_id'],$row['quantity']);
        $stmt2->execute();
    }

    // ลบตะกร้า
    $conn->query("DELETE FROM cart WHERE user_id='$user_id'");

    header("Location: orderdetail.php?success=1");
    exit();
}

// =========================
// ดึงที่อยู่
// =========================
$address = $conn->query("SELECT * FROM address 
    WHERE user_id='$user_id' 
    ORDER BY address_id DESC LIMIT 1")->fetch_assoc();

// =========================
// ดึงสินค้าในตะกร้า
// =========================
$cart_sql = "SELECT cart.*, products.product_name, products.price 
             FROM cart
             JOIN products ON cart.product_id = products.product_id
             WHERE cart.user_id='$user_id'";
$cart_result = $conn->query($cart_sql);

$total = 0;
$shipping = 75;
$discount = 0;
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ชำระเงิน</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f5f5f5;}
.header{background:#ee4d2d;color:white;padding:15px;text-align:center;font-size:22px;}
.box{background:white;padding:20px;margin-top:15px;border-radius:6px;}
.total{font-size:26px;font-weight:bold;color:#ee4d2d;}
.btn-order{background:#ee4d2d;color:white;font-size:18px;padding:10px 40px;}
.btn-order:hover{background:#d73211;color:white;}
</style>
</head>

<body>
<div class="header">ชำระเงิน</div>
<div class="container">

<form method="POST">

<!-- ================= ที่อยู่ ================= -->
<div class="box">
    <h5>📍 ที่อยู่จัดส่ง</h5>
    <?php if($address): ?>
        <strong><?= htmlspecialchars($address['fullname']) ?></strong><br>
        โทร: <?= htmlspecialchars($address['phone']) ?><br>
        <?= htmlspecialchars($address['address']) ?> 
        <?= htmlspecialchars($address['district']) ?> 
        <?= htmlspecialchars($address['province']) ?> 
        <?= htmlspecialchars($address['postal_code']) ?>
    <?php else: ?>
        <div class="text-danger">กรุณาเพิ่มที่อยู่ก่อนสั่งซื้อ</div>
    <?php endif; ?>
</div>

<!-- ================= สินค้า ================= -->
<div class="box">
    <h5>🛒 รายการสินค้า</h5>
    <div class="row fw-bold border-bottom pb-2">
        <div class="col-md-6">สินค้า</div>
        <div class="col-md-2">ราคา</div>
        <div class="col-md-2">จำนวน</div>
        <div class="col-md-2 text-end">รวม</div>
    </div>

<?php while($row = $cart_result->fetch_assoc()):
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
?>
    <div class="row mt-3">
        <div class="col-md-6"><?= htmlspecialchars($row['product_name']) ?></div>
        <div class="col-md-2">฿<?= number_format($row['price'],2) ?></div>
        <div class="col-md-2"><?= $row['quantity'] ?></div>
        <div class="col-md-2 text-end">฿<?= number_format($subtotal,2) ?></div>
    </div>
<?php endwhile; ?>
</div>

<!-- ================= วิธีชำระเงิน ================= -->
<div class="box">
    <h5>💳 วิธีการชำระเงิน</h5>
    <input type="radio" name="payment_method" value="QR พร้อมเพย์" checked> QR พร้อมเพย์<br>
    <input type="radio" name="payment_method" value="เก็บเงินปลายทาง"> เก็บเงินปลายทาง<br>
    <input type="radio" name="payment_method" value="บัตรเครดิต/เดบิต"> บัตรเครดิต/เดบิต
</div>

<?php
$grand_total = $total + $shipping - $discount;
?>

<!-- ================= สรุปยอด ================= -->
<div class="box">
    <div class="d-flex justify-content-between">
        <div>รวมสินค้า</div>
        <div>฿<?= number_format($total,2) ?></div>
    </div>

    <div class="d-flex justify-content-between">
        <div>ค่าจัดส่ง</div>
        <div>฿<?= number_format($shipping,2) ?></div>
    </div>

    <hr>

    <div class="d-flex justify-content-between align-items-center">
        <div class="total">
            ยอดชำระทั้งหมด ฿<?= number_format($grand_total,2) ?>
        </div>
        <div>
            <input type="hidden" name="total_price" value="<?= $grand_total ?>">
            <button type="submit" name="confirm_order" class="btn btn-order">
                ชำระสินค้า
            </button>
        </div>
    </div>
</div>

</form>
</div>
</body>
</html>
