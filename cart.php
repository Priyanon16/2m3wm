<?php
session_start();
include_once("connectdb.php");
include_once("header.php");
include_once("bootstrap.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$uid = intval($_SESSION['user_id']);

/* ลบสินค้า */
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

/* ดึงข้อมูลตะกร้า */
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

<style>
body {
    background: linear-gradient(135deg, #f8f8f8, #ffffff);
    font-family:'Kanit',sans-serif;
}

.container {
    max-width: 1000px;
    margin: 50px auto;
}

.cart-box {
    background: #fff;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    border-top: 5px solid #ff6a00;
}

.cart-item {
    display: flex;
    align-items: center;
    padding: 25px 0;
    border-bottom: 1px solid #eee;
}

.cart-item img {
    width: 130px;
    height: 130px;
    object-fit: cover;
    border-radius: 15px;
    margin-right: 25px;
    border: 3px solid #ff6a00;
}

.price {
    color: #ff6a00;
    font-weight: bold;
    font-size: 18px;
}

.summary {
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid #ff6a00;
    text-align: right;
}

.checkout-btn {
    background: linear-gradient(45deg, #ff6a00, #ff8c42);
    color: #000;
    padding: 14px 35px;
    border: none;
    border-radius: 10px;
    font-weight: bold;
}
</style>

<div class="container">
<div class="cart-box">

<?php if(mysqli_num_rows($rs)==0): ?>
    <h3>ยังไม่มีสินค้าในตะกร้า</h3>
<?php else: ?>

<?php 
$total = 0;
while($item = mysqli_fetch_assoc($rs)): 
    $qty = $item['quantity'];
    $subtotal = $item['p_price'] * $qty;
    $total += $subtotal;
?>

<div class="cart-item">
    <img src="<?= $item['p_img']; ?>">
    <div style="flex:1;">
        <h5><?= htmlspecialchars($item['p_name']); ?></h5>
        <p>จำนวน: <?= $qty; ?></p>
        <p class="price">฿<?= number_format($subtotal,0); ?></p>
        <a href="?remove=<?= $item['p_id']; ?>" style="color:red;">
            ลบสินค้า
        </a>
    </div>
</div>

<?php endwhile; ?>


<div class="summary">
    <p>ยอดรวมสินค้า: ฿<?= number_format($total,0); ?></p>
    <p>ค่าจัดส่ง: ฿50</p>
    <h4>ยอดสุทธิ: ฿<?= number_format($total+50,0); ?></h4>

    <form action="pay.php" method="post">
        <button type="submit" class="pay-btn">
            ดำเนินการสั่งซื้อ
        </button>
    </form>
</div>

<?php endif; ?>

</div>
</div>



</body>
</html>
