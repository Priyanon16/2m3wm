<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$uid = intval($_SESSION['user_id']);

/* ==========================
   ลบสินค้า
========================== */
if(isset($_GET['remove'])){
    $pid = intval($_GET['remove']);
    mysqli_query($conn,"
        DELETE FROM cart 
        WHERE user_id='$uid' 
        AND product_id='$pid'
    ");
    header("Location: cart.php");
    exit;
}

/* ==========================
   ดึงข้อมูลตะกร้า
========================== */
$sql = "
SELECT c.quantity, p.*
FROM cart c
JOIN products p ON c.product_id = p.p_id
WHERE c.user_id='$uid'
";

$rs = mysqli_query($conn,$sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ตะกร้าสินค้า - 2M3WM</title>

<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
    font-family:'Kanit',sans-serif;
}

/* PAGE HEADER */
.page-header{
    background:#111;
    padding:40px 0;
    text-align:center;
    color:#fff;
}

.page-header h2{
    font-weight:600;
}

/* CART BOX */
.cart-box{
    background:#fff;
    padding:35px;
    border-radius:15px;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
}

.cart-item{
    display:flex;
    align-items:center;
    padding:20px 0;
    border-bottom:1px solid #eee;
}

.cart-item img{
    width:120px;
    height:120px;
    object-fit:cover;
    border-radius:10px;
    margin-right:20px;
}

.price{
    color:#ff7a00;
    font-weight:600;
    font-size:18px;
}

.summary{
    margin-top:30px;
    padding-top:20px;
    border-top:2px solid #ff7a00;
    text-align:right;
}

.checkout-btn{
    background:#ff7a00;
    border:none;
    padding:12px 30px;
    border-radius:10px;
    font-weight:600;
    color:#fff;
    transition:.3s;
}

.checkout-btn:hover{
    background:#e66e00;
}
</style>
</head>

<body>

<?php include_once("header.php"); ?>

<!-- PAGE HEADER -->
<div class="page-header">
    <h2>ตะกร้าสินค้า</h2>
    <p class="mb-0 text-light">ตรวจสอบสินค้าก่อนทำการสั่งซื้อ</p>
</div>

<div class="container mt-5 mb-5">
<div class="cart-box">

<?php if(mysqli_num_rows($rs)==0): ?>

    <div class="alert alert-light text-center">
        ยังไม่มีสินค้าในตะกร้า
    </div>

<?php else: ?>

<?php 
$total = 0;
while($item = mysqli_fetch_assoc($rs)): 
    $qty = $item['quantity'];
    $subtotal = $item['p_price'] * $qty;
    $total += $subtotal;
?>

<div class="cart-item">
    <img src="<?= htmlspecialchars($item['p_img']); ?>">

    <div style="flex:1;">
        <h5><?= htmlspecialchars($item['p_name']); ?></h5>
        <p>จำนวน: <?= $qty; ?></p>
        <p class="price">
            ฿<?= number_format($subtotal,0); ?>
        </p>
        <a href="?remove=<?= $item['p_id']; ?>" 
           class="text-danger"
           onclick="return confirm('ต้องการลบสินค้านี้หรือไม่?')">
           ลบสินค้า
        </a>
    </div>
</div>

<?php endwhile; ?>

<div class="summary">
    <p>ยอดรวมสินค้า: ฿<?= number_format($total,0); ?></p>
    <p>ค่าจัดส่ง: ฿50</p>
    <h4>ยอดสุทธิ: ฿<?= number_format($total+50,0); ?></h4>

    <form action="checkout.php" method="post">
        <button type="submit" class="checkout-btn">
            ดำเนินการสั่งซื้อ
        </button>
    </form>
</div>

<?php endif; ?>

</div>
</div>

</body>
</html>
