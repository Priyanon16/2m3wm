<?php
session_start();
include 'connectdb.php';


$user_id = $_SESSION['user_id'];

/* ===============================
   เมื่อกดชำระสินค้า
================================ */
if(isset($_POST['confirm_order'])){

    if(empty($_POST['payment_method'])){
        die("กรุณาเลือกช่องทางการชำระเงิน");
    }

    $payment_method = $_POST['payment_method'];
    $total_price    = floatval($_POST['total_price']);

    // สร้างคำสั่งซื้อ
    $stmt = $conn->prepare("INSERT INTO orders 
        (user_id,total_price,payment_method,status,order_date)
        VALUES (?,?,?,'รอชำระเงิน',NOW())");
    $stmt->bind_param("ids",$user_id,$total_price,$payment_method);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // ดึงสินค้าใน cart
    $stmt_cart = $conn->prepare("SELECT product_id, quantity 
                                 FROM cart WHERE user_id=?");
    $stmt_cart->bind_param("i",$user_id);
    $stmt_cart->execute();
    $cart = $stmt_cart->get_result();

    while($row = $cart->fetch_assoc()){
        $stmt2 = $conn->prepare("INSERT INTO order_items
            (order_id,product_id,quantity)
            VALUES (?,?,?)");
        $stmt2->bind_param("iii",$order_id,$row['product_id'],$row['quantity']);
        $stmt2->execute();
    }

    // ลบตะกร้า
    $stmt_del = $conn->prepare("DELETE FROM cart WHERE user_id=?");
    $stmt_del->bind_param("i",$user_id);
    $stmt_del->execute();

    header("Location: orderdetail.php?success=1");
    exit();
}

/* ===============================
   ดึงข้อมูลสินค้าในตะกร้า
================================ */
$stmt_products = $conn->prepare("
    SELECT cart.*, 
           products.product_name,
           products.price,
           products.image,
           products.size,
           products.color,
           products.shop_name
    FROM cart
    JOIN products ON cart.product_id = products.product_id
    WHERE cart.user_id=?
");
$stmt_products->bind_param("i",$user_id);
$stmt_products->execute();
$cart_result = $stmt_products->get_result();

$total = 0;
$shipping = 60;
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ชำระสินค้า</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f4f4;
    font-family: 'Segoe UI', sans-serif;
}
.header{
    background:#111;
    color:#fff;
    padding:18px;
    text-align:center;
    font-size:22px;
    letter-spacing:1px;
}
.box{
    background:#fff;
    padding:25px;
    margin-top:20px;
    border-radius:10px;
    box-shadow:0 3px 10px rgba(0,0,0,0.05);
}
.product-img{
    width:80px;
    height:80px;
    object-fit:cover;
    border-radius:6px;
}
.total{
    font-size:28px;
    font-weight:bold;
    color:#ff6a00;
}
.btn-order{
    background:#ff6a00;
    color:#fff;
    padding:12px 45px;
    border:none;
    border-radius:8px;
    font-size:16px;
}
.btn-order:hover{
    background:#e55d00;
}

.payment-box{
    border:1px solid #ccc;
    border-radius:10px;
    padding:15px;
    margin-bottom:12px;
    cursor:pointer;
    transition:0.2s;
}
.payment-box:hover{
    border-color:#ff6a00;
}
.payment-active{
    border:2px solid #ff6a00 !important;
    background:#fff6ef;
}
.shop-name{
    font-weight:600;
    color:#111;
}
.option-text{
    font-size:14px;
    color:#666;
}
</style>
</head>

<body>

<div class="header">CHECKOUT</div>

<div class="container">

<form method="POST">

<!-- ================= สินค้า ================= -->
<div class="box">
<h5 class="mb-4">รายการสินค้า</h5>

<?php if($cart_result->num_rows == 0): ?>
<div class="text-danger">ไม่มีสินค้าในตะกร้า</div>
<?php else: ?>

<?php while($row = $cart_result->fetch_assoc()):
$subtotal = $row['price'] * $row['quantity'];
$total += $subtotal;
?>

<div class="border-bottom pb-3 mb-3">

<div class="shop-name mb-2">
<?= htmlspecialchars($row['shop_name']) ?>
</div>

<div class="row align-items-center">

<div class="col-md-2">
<img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="product-img">
</div>

<div class="col-md-4">
<div><?= htmlspecialchars($row['product_name']) ?></div>
<div class="option-text">
สี: <?= htmlspecialchars($row['color']) ?> | 
ไซซ์: <?= htmlspecialchars($row['size']) ?>
</div>
</div>

<div class="col-md-2">
฿<?= number_format($row['price'],2) ?>
</div>

<div class="col-md-2">
x <?= $row['quantity'] ?>
</div>

<div class="col-md-2 text-end fw-bold">
฿<?= number_format($subtotal,2) ?>
</div>

</div>
</div>

<?php endwhile; ?>
<?php endif; ?>
</div>


<!-- ================= ช่องทางการชำระเงิน ================= -->
<div class="box">
<h5 class="mb-4">ช่องทางการชำระเงิน</h5>

<label class="payment-box payment-active">
<input type="radio" name="payment_method" value="QR พร้อมเพย์" checked>
QR พร้อมเพย์
</label>

<label class="payment-box">
<input type="radio" name="payment_method" value="บัตรเครดิต/เดบิต">
บัตรเครดิต / เดบิต
</label>

<label class="payment-box">
<input type="radio" name="payment_method" value="โอนผ่านธนาคาร">
โอนผ่านธนาคาร
</label>

<label class="payment-box">
<input type="radio" name="payment_method" value="เก็บเงินปลายทาง">
เก็บเงินปลายทาง (COD)
</label>

</div>


<?php
$grand_total = $total + $shipping;
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
฿<?= number_format($grand_total,2) ?>
</div>

<div>
<input type="hidden" name="total_price" value="<?= $grand_total ?>">
<button type="submit" name="confirm_order" class="btn-order">
ชำระสินค้า
</button>
</div>
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
