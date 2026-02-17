<?php
session_start();
include_once("connectdb.php");

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
   ดึงข้อมูลออเดอร์ + ที่อยู่
========================== */
$order_sql = "
SELECT o.*, a.name, a.phone, a.address_line
FROM orders o
LEFT JOIN addresses a ON o.address_id = a.address_id
WHERE o.o_id='$oid'
AND o.u_id='$uid'
LIMIT 1
";

$order_rs = mysqli_query($conn,$order_sql);

if(mysqli_num_rows($order_rs) == 0){
    die("<div class='container mt-5'>
            <div class='alert alert-danger text-center'>
            ไม่พบคำสั่งซื้อนี้
            </div>
          </div>");
}

$order = mysqli_fetch_assoc($order_rs);

/* ==========================
   ดึงสินค้าในออเดอร์
========================== */
$detail_sql = "
SELECT p.p_name, p.p_price, p.p_img, od.q_ty
FROM order_details od
JOIN products p ON od.p_id = p.p_id
WHERE od.o_id='$oid'
";

$detail_rs = mysqli_query($conn,$detail_sql);
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

<!-- ==========================
     ที่อยู่จัดส่ง
========================== -->
<h6 class="mb-3">📦 ที่อยู่จัดส่ง</h6>

<?php if($order['name']): ?>
<div class="mb-4">
<strong><?= htmlspecialchars($order['name']) ?></strong><br>
<?= htmlspecialchars($order['phone']) ?><br>
<?= htmlspecialchars($order['address_line']) ?>
</div>
<?php else: ?>
<div class="alert alert-warning">
ไม่มีข้อมูลที่อยู่ในออเดอร์นี้
</div>
<?php endif; ?>

<hr>

<!-- ==========================
     รายการสินค้า
========================== -->
<h6 class="mb-3">🛒 รายการสินค้า</h6>

<?php 
$total = 0;

if(mysqli_num_rows($detail_rs) == 0){
    echo "<div class='alert alert-danger'>ไม่พบสินค้าในออเดอร์นี้</div>";
}else{
    while($item = mysqli_fetch_assoc($detail_rs)){
        $subtotal = $item['p_price'] * $item['q_ty'];
        $total += $subtotal;
?>

<div class="row align-items-center mb-3">

<div class="col-md-2">
<img src="<?= $item['p_img'] ?>" class="img-fluid rounded">
</div>

<div class="col-md-6">
<strong><?= htmlspecialchars($item['p_name']) ?></strong><br>
<small class="text-muted">
ราคา <?= number_format($item['p_price'],2) ?> บาท × <?= $item['q_ty'] ?>
</small>
</div>

<div class="col-md-4 text-end fw-bold">
<?= number_format($subtotal,2) ?> บาท
</div>

</div>

<?php } } ?>

<hr>

<!-- ==========================
     สรุปยอด
========================== -->
<div class="text-end">
<h5>ยอดรวมสินค้า: <?= number_format($total,2) ?> บาท</h5>
<h4 class="text-warning">
ยอดชำระทั้งหมด: <?= number_format($order['total_price'],2) ?> บาท
</h4>
</div>

</div>
</div>
