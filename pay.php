<?php
include 'connectdb.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* =========================
   เมื่อกดชำระสินค้า
========================= */
if(isset($_POST['confirm_order'])){

    $payment_method = $_POST['payment_method'];
    $total_price    = $_POST['total_price'];

    $stmt = $conn->prepare("INSERT INTO orders 
        (user_id,total_price,payment_method,status,order_date) 
        VALUES (?,?,?,'รอชำระเงิน',NOW())");
    $stmt->bind_param("ids",$user_id,$total_price,$payment_method);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    $cart = $conn->query("SELECT * FROM cart WHERE user_id='$user_id'");
    while($row = $cart->fetch_assoc()){

        $stmt2 = $conn->prepare("INSERT INTO order_items 
            (order_id,product_id,quantity) VALUES (?,?,?)");
        $stmt2->bind_param("iii",$order_id,$row['product_id'],$row['quantity']);
        $stmt2->execute();
    }

    $conn->query("DELETE FROM cart WHERE user_id='$user_id'");
    header("Location: orderhistory.php?success=1");
    exit();
}

/* =========================
   ดึงข้อมูลสินค้าในตะกร้า
========================= */
$cart_sql = "SELECT cart.*, 
                    products.product_name, 
                    products.price,
                    products.image,
                    products.size,
                    products.color,
                    products.shop_name
             FROM cart
             JOIN products ON cart.product_id = products.product_id
             WHERE cart.user_id='$user_id'";

$cart_result = $conn->query($cart_sql);

$total = 0;
$shipping = 75;
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
.product-img{width:80px;height:80px;object-fit:cover;}
.total{font-size:26px;font-weight:bold;color:#ee4d2d;}
.btn-order{background:#ee4d2d;color:white;font-size:18px;padding:10px 40px;}
.btn-order:hover{background:#d73211;color:white;}
.shop-name{font-weight:bold;color:#333;}
.option-text{color:#777;font-size:14px;}
</style>
</head>

<body>
<div class="header">ตรวจสอบคำสั่งซื้อ</div>
<div class="container">

<form method="POST">

<div class="box">
<h5>🛒 สินค้าที่สั่งซื้อ</h5>

<?php while($row = $cart_result->fetch_assoc()):
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
?>

<div class="border-bottom pb-3 mb-3">

    <!-- ร้านค้า -->
    <div class="shop-name mb-2">
        🏬 <?= htmlspecialchars($row['shop_name']) ?>
    </div>

    <div class="row align-items-center">

        <!-- รูปสินค้า -->
        <div class="col-md-2">
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="product-img">
        </div>

        <!-- รายละเอียด -->
        <div class="col-md-4">
            <div><?= htmlspecialchars($row['product_name']) ?></div>
            <div class="option-text">
                ตัวเลือก: สี <?= htmlspecialchars($row['color']) ?> 
                | ไซซ์ <?= htmlspecialchars($row['size']) ?>
            </div>

            <div class="mt-2">
                <textarea name="note[]" class="form-control form-control-sm"
                placeholder="หมายเหตุถึงร้านค้า (ถ้ามี)"></textarea>
            </div>
        </div>

        <!-- ราคา -->
        <div class="col-md-2">
            ฿<?= number_format($row['price'],2) ?>
        </div>

        <!-- จำนวน -->
        <div class="col-md-2">
            x <?= $row['quantity'] ?>
        </div>

        <!-- รวม -->
        <div class="col-md-2 text-end fw-bold">
            ฿<?= number_format($subtotal,2) ?>
        </div>

    </div>

</div>

<?php endwhile; ?>
</div>
<div class="box">
<h5>💳 วิธีชำระเงิน</h5>

<div class="form-check">
  <input class="form-check-input" type="radio" name="payment_method" value="QR พร้อมเพย์" checked>
  <label class="form-check-label">QR พร้อมเพย์</label>
</div>

<div class="form-check">
  <input class="form-check-input" type="radio" name="payment_method" value="เก็บเงินปลายทาง">
  <label class="form-check-label">เก็บเงินปลายทาง</label>
</div>

<div class="form-check">
  <input class="form-check-input" type="radio" name="payment_method" value="บัตรเครดิต">
  <label class="form-check-label">บัตรเครดิต / เดบิต</label>
</div>

</div>

<?php
$grand_total = $total + $shipping;
?>

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
