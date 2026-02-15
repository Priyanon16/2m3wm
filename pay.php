<?php
include 'connectdb.php';

$user_id = 1; // ภายหลังเปลี่ยนเป็น $_SESSION['user_id']

// ================== กดสั่งสินค้า ==================
if(isset($_POST['confirm_order'])){

    $payment_method = $_POST['payment_method'];
    $total_price = $_POST['total_price'];

    // เพิ่ม order
    $conn->query("INSERT INTO orders (user_id,total_price,payment_method,status)
                  VALUES ('$user_id','$total_price','$payment_method','รอดำเนินการ')");
    $order_id = $conn->insert_id;

    // ย้ายสินค้าไป order_items
    $cart = $conn->query("SELECT * FROM cart WHERE user_id='$user_id'");
    while($row = $cart->fetch_assoc()){
        $conn->query("INSERT INTO order_items (order_id,product_id,quantity)
                      VALUES ('$order_id','{$row['product_id']}','{$row['quantity']}')");
    }

    // ลบตะกร้า
    $conn->query("DELETE FROM cart WHERE user_id='$user_id'");

    echo "<script>alert('สั่งซื้อสำเร็จ');window.location='checkout.php';</script>";
}


// ================== ดึงที่อยู่ ==================
$address = $conn->query("SELECT * FROM address 
                         WHERE user_id='$user_id'
                         ORDER BY address_id DESC LIMIT 1")->fetch_assoc();


// ================== ดึงสินค้าในตะกร้า ==================
$cart_sql = "SELECT cart.*, products.product_name, products.price, products.image 
             FROM cart 
             JOIN products ON cart.product_id = products.product_id
             WHERE cart.user_id='$user_id'";

$cart_result = $conn->query($cart_sql);

$total = 0;
$shipping = 75;
$discount = 80;
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f5f5f5;}
.header{background:#ee4d2d;color:white;padding:15px;font-size:22px;text-align:center;}
.box{background:white;padding:20px;margin-top:15px;border-radius:5px;}
.product-img{width:70px;}
.total-price{font-size:26px;font-weight:bold;color:#ee4d2d;}
.btn-order{background:#ee4d2d;color:white;font-size:18px;padding:10px 40px;}
.btn-order:hover{background:#d73211;color:white;}
</style>
</head>

<body>

<div class="header">ชำระเงิน</div>
<div class="container">

<form method="POST">

<!-- ===== ที่อยู่ ===== -->
<div class="box">
    <div class="d-flex justify-content-between">
        <h5>📍 ที่อยู่ในการจัดส่ง</h5>
        <a href="#" class="text-primary">เปลี่ยน</a>
    </div>

    <?php if($address): ?>
        <strong><?= $address['fullname'] ?> (+66) <?= $address['phone'] ?></strong><br>
        <?= $address['address'] ?> 
        <?= $address['district'] ?> 
        <?= $address['province'] ?> 
        <?= $address['postal_code'] ?>
    <?php else: ?>
        <span class="text-danger">ยังไม่มีที่อยู่</span>
    <?php endif; ?>
</div>


<!-- ===== สินค้า ===== -->
<div class="box">
    <div class="row fw-bold border-bottom pb-2">
        <div class="col-md-5">สินค้า</div>
        <div class="col-md-2">ราคาต่อหน่วย</div>
        <div class="col-md-2">จำนวน</div>
        <div class="col-md-3 text-end">รวม</div>
    </div>

<?php while($row = $cart_result->fetch_assoc()):
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
?>
    <div class="row align-items-center mt-3">
        <div class="col-md-5">
            <img src="uploads/<?= $row['image'] ?>" class="product-img me-2">
            <?= $row['product_name'] ?>
        </div>
        <div class="col-md-2">฿<?= number_format($row['price'],2) ?></div>
        <div class="col-md-2"><?= $row['quantity'] ?></div>
        <div class="col-md-3 text-end">฿<?= number_format($subtotal,2) ?></div>
    </div>
<?php endwhile; ?>
</div>


<!-- ===== วิธีชำระเงิน ===== -->
<div class="box">
    <h5>💳 วิธีการชำระเงิน</h5>

    <input type="radio" name="payment_method" value="QR พร้อมเพย์" checked> QR พร้อมเพย์<br>
    <input type="radio" name="payment_method" value="เก็บเงินปลายทาง"> เก็บเงินปลายทาง<br>
    <input type="radio" name="payment_method" value="บัตรเครดิต"> บัตรเครดิต/เดบิต
</div>

<?php
$grand_total = $total + $shipping - $discount;
?>

<!-- ===== สรุปยอด ===== -->
<div class="box">
    <div class="d-flex justify-content-between">
        <div>รวมสินค้า</div>
        <div>฿<?= number_format($total,2) ?></div>
    </div>

    <div class="d-flex justify-content-between">
        <div>ค่าจัดส่ง</div>
        <div>฿<?= number_format($shipping,2) ?></div>
    </div>

    <div class="d-flex justify-content-between text-danger">
        <div>ส่วนลด</div>
        <div>-฿<?= number_format($discount,2) ?></div>
    </div>

    <hr>

    <div class="d-flex justify-content-between align-items-center">
        <div class="total-price">
            ยอดชำระทั้งหมด ฿<?= number_format($grand_total,2) ?>
        </div>

        <div>
            <input type="hidden" name="total_price" value="<?= $grand_total ?>">
            <button type="submit" name="confirm_order" class="btn btn-order">
                สั่งสินค้า
            </button>
        </div>
    </div>
</div>

</form>
</div>
</body>
</html>
