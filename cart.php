<?php
session_start();
include_once("connectdb.php");
include_once("header.php");   // ✅ เรียก header แยก
include_once("bootstrap.php");

$cart = $_SESSION['cart'] ?? [];

/* ลบสินค้า */
if(isset($_GET['remove'])){
    $remove_id = intval($_GET['remove']);
    unset($_SESSION['cart'][$remove_id]);
    header("Location: cart.php");
    exit;
}

/* ถ้าตะกร้าว่าง */
if(empty($cart)){
    $cart_items = [];
} else {
    $ids = implode(",", array_keys($cart));
    $sql = "SELECT * FROM products WHERE p_id IN ($ids)";
    $rs  = mysqli_query($conn,$sql);
    $cart_items = [];
    while($row = mysqli_fetch_assoc($rs)){
        $cart_items[] = $row;
    }
}
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

<?php if(empty($cart_items)): ?>
    <h3>ยังไม่มีสินค้าในตะกร้า</h3>
<?php else: ?>

<?php 
$total = 0;
foreach($cart_items as $item): 
    $qty = $cart[$item['p_id']];
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

<?php endforeach; ?>

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
