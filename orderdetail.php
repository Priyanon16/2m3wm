<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = intval($_SESSION['user_id']);

if(!isset($_GET['id'])){
    header("Location: myorders.php");
    exit;
}

$oid = intval($_GET['id']);

/* ==========================
   ดึงข้อมูลออเดอร์จริง
========================== */
$stmt = $conn->prepare("
    SELECT *
    FROM orders
    WHERE o_id = ? AND u_id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $oid, $uid);
$stmt->execute();
$order_rs = $stmt->get_result();

if($order_rs->num_rows == 0){
    die("<div class='container mt-5'>
            <div class='alert alert-danger text-center'>
            ไม่พบคำสั่งซื้อนี้
            </div>
          </div>");
}

$order = $order_rs->fetch_assoc();

/* ==========================
   ดึงที่อยู่ล่าสุดของ user (กรณีไม่มี address_id)
========================== */
$stmt_addr = $conn->prepare("
    SELECT *
    FROM addresses
    WHERE user_id = ?
    ORDER BY address_id DESC
    LIMIT 1
");
$stmt_addr->bind_param("i", $uid);
$stmt_addr->execute();
$addr_rs = $stmt_addr->get_result();
$address = $addr_rs->fetch_assoc();

/* ==========================
   ดึงสินค้าในออเดอร์
========================== */
$stmt_detail = $conn->prepare("
    SELECT od.q_ty, od.price, p.p_name,
           (SELECT img_path FROM product_images WHERE p_id = p.p_id LIMIT 1) AS p_img
    FROM order_details od
    JOIN products p ON od.p_id = p.p_id
    WHERE od.o_id = ?
");
$stmt_detail->bind_param("i", $oid);
$stmt_detail->execute();
$detail_rs = $stmt_detail->get_result();
?>

<?php include("header.php"); ?>

<div class="container mt-5 mb-5">

<a href="myorders.php" class="btn btn-light mb-4">
← กลับไปหน้ารายการสั่งซื้อ
</a>

<div class="card p-4 shadow-sm">

<h5>เลขที่ออเดอร์ #<?= $order['o_id'] ?></h5>
<small class="text-muted">
สั่งซื้อเมื่อ <?= date("d/m/Y H:i", strtotime($order['o_date'])) ?>
</small>

<hr>

<h6>📦 ที่อยู่จัดส่ง</h6>

<?php if($address): ?>
<div class="mb-4">
<strong><?= htmlspecialchars($address['fullname']) ?></strong><br>
<?= htmlspecialchars($address['phone']) ?><br>
<?= htmlspecialchars($address['address']) ?> 
ต.<?= htmlspecialchars($address['district']) ?> 
จ.<?= htmlspecialchars($address['province']) ?> 
<?= htmlspecialchars($address['postal_code']) ?>
</div>
<?php else: ?>
<div class="alert alert-warning">
ยังไม่มีที่อยู่จัดส่ง
</div>
<?php endif; ?>

<hr>

<h6>🛒 รายการสินค้า</h6>

<?php 
$total = 0;

if($detail_rs->num_rows == 0){
    echo "<div class='alert alert-danger'>ไม่พบสินค้าในออเดอร์นี้</div>";
}else{
    while($item = $detail_rs->fetch_assoc()){
        $subtotal = $item['price'] * $item['q_ty'];
        $total += $subtotal;
?>

<div class="row align-items-center mb-3">

<div class="col-md-2">
<img src="<?= $item['p_img'] ?: 'https://placehold.co/100x100' ?>" 
     class="img-fluid rounded">
</div>

<div class="col-md-6">
<strong><?= htmlspecialchars($item['p_name']) ?></strong><br>
<small class="text-muted">
ราคา <?= number_format($item['price'],2) ?> บาท × <?= $item['q_ty'] ?>
</small>
</div>

<div class="col-md-4 text-end fw-bold">
<?= number_format($subtotal,2) ?> บาท
</div>

</div>

<?php } } ?>

<hr>

<div class="text-end">
<h5>ยอดรวมสินค้า: <?= number_format($total,2) ?> บาท</h5>
<h4 class="text-warning">
ยอดชำระทั้งหมด: <?= number_format($order['total_price'],2) ?> บาท
</h4>
</div>

</div>
</div>
