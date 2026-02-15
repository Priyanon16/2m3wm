<?php
session_start();
include_once("connectdb.php");

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
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #f8f8f8, #ffffff);
}

/* Header */
.header {
    background: #000;
    color: #ff6a00;
    padding: 18px 40px;
    font-size: 22px;
    font-weight: bold;
    letter-spacing: 1px;
}

/* Container */
.container {
    width: 90%;
    max-width: 1000px;
    margin: 50px auto;
}

/* Cart Card */
.cart-box {
    background: #fff;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    border-top: 5px solid #ff6a00;
}

/* Cart Item */
.cart-item {
    display: flex;
    align-items: center;
    padding: 25px 0;
    border-bottom: 1px solid #eee;
    transition: 0.3s;
}

.cart-item:hover {
    background: #fff3eb;
    transform: scale(1.01);
}

.cart-item img {
    width: 130px;
    height: 130px;
    object-fit: cover;
    border-radius: 15px;
    margin-right: 25px;
    border: 3px solid #ff6a00;
}

/* Item Info */
.item-info {
    flex: 1;
}

.item-info h4 {
    margin: 0 0 10px;
    color: #000;
    font-size: 18px;
}

.item-info p {
    margin: 5px 0;
    color: #555;
}

/* Price */
.price {
    color: #ff6a00;
    font-weight: bold;
    font-size: 20px;
}

/* Summary */
.summary {
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid #ff6a00;
    text-align: right;
}

.summary p {
    margin: 8px 0;
    font-size: 16px;
}

.summary h3 {
    color: #000;
    font-size: 22px;
    margin: 15px 0;
}

/* Checkout Button */
.checkout-btn {
    display: inline-block;
    background: linear-gradient(45deg, #ff6a00, #ff8c42);
    color: #000;
    padding: 14px 35px;
    border: none;
    border-radius: 10px;
    font-size: 17px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 5px 15px rgba(255,106,0,0.3);
}

.checkout-btn:hover {
    background: #000;
    color: #ff6a00;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
}
</style>
</head>

<body>

<div class="header">
    🛒 2M3WM SNEAKER
</div>

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
        <div class="item-info">
            <h4><?= htmlspecialchars($item['p_name']); ?></h4>
            <p>จำนวน: <?= $qty; ?></p>
            <p class="price">
                ฿<?= number_format($subtotal,0); ?>
            </p>
            <a href="?remove=<?= $item['p_id']; ?>" style="color:red;">
                ลบสินค้า
            </a>
        </div>
    </div>

    <?php endforeach; ?>

    <div class="summary">
        <p>ยอดรวมสินค้า: ฿<?= number_format($total,0); ?></p>
        <p>ค่าจัดส่ง: ฿50</p>
        <h3>ยอดสุทธิ: ฿<?= number_format($total+50,0); ?></h3>

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
