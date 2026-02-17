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
   ดึงข้อมูลออเดอร์ + ที่อยู่
========================== */
$order_sql = "
SELECT o.*, 
       a.fullname, 
       a.phone, 
       a.address,
       a.district,
       a.province,
       a.postal_code
FROM orders o
LEFT JOIN addresses a ON o.address_id = a.address_id
WHERE o.o_id='$oid'
AND o.u_id='$uid'
LIMIT 1
";

$order_rs = mysqli_query($conn,$order_sql);

if(mysqli_num_rows($order_rs) == 0){
    die("<div style='padding:50px;text-align:center'>
            <h3>ไม่พบคำสั่งซื้อนี้</h3>
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

/* ==========================
   เตรียมสถานะ
========================== */
$status = $order['status'];
$badge_class = "bg-secondary";

if($status == "รอชำระเงิน") $badge_class = "bg-danger";
elseif($status == "ที่ต้องจัดส่ง") $badge_class = "bg-warning text-dark";
elseif($status == "รอรับ") $badge_class = "bg-primary";
elseif($status == "จัดส่งสำเร็จ") $badge_class = "bg-success";
elseif($status == "ยกเลิก") $badge_class = "bg-dark";
?>

<?php include("header.php"); ?>

<style>
body{
    background:#f5f5f5;
    font-family:'Kanit',sans-serif;
}
.card{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}
.timeline {
    display:flex;
    justify-content:space-between;
    margin:30px 0;
}
.timeline-step {
    text-align:center;
    flex:1;
    position:relative;
}
.timeline-step::after {
    content:'';
    position:absolute;
    top:15px;
    right:-50%;
    width:100%;
    height:3px;
    background:#ddd;
    z-index:-1;
}
.timeline-step:last-child::after{
    display:none;
}
.timeline-circle{
    width:30px;
    height:30px;
    border-radius:50%;
    margin:0 auto 5px;
    background:#ddd;
    line-height:30px;
    color:#fff;
}
.active .timeline-circle{
    background:#28a745;
}
.active{
    color:#28a745;
    font-weight:600;
}
</style>

<div class="container mt-5 mb-5">

<a href="myorders.php" class="btn btn-light mb-4">
← กลับไปหน้ารายการสั่งซื้อ
</a>

<div class="card p-4">

<h5>เลขที่ออเดอร์ #<?= $order['o_id'] ?></h5>
<small class="text-muted">
สั่งซื้อเมื่อ <?= date("d/m/Y H:i", strtotime($order['o_date'])) ?>
</small>

<div class="mt-3">
<span class="badge <?= $badge_class ?> px-3 py-2">
สถานะ: <?= $status ?>
</span>

<?php if(!empty($order['payment_method'])): ?>
<div class="mt-2 text-muted">
วิธีชำระเงิน:
<?= ($order['payment_method']=="cod") ? "เก็บเงินปลายทาง" : "โอนเงินผ่านธนาคาร" ?>
</div>
<?php endif; ?>
</div>

<?php if($status != "ยกเลิก"): ?>
<?php
$steps = ["รอชำระเงิน","ที่ต้องจัดส่ง","รอรับ","จัดส่งสำเร็จ"];
$current_index = array_search($status, $steps);
?>
<div class="timeline">
<?php foreach($steps as $index => $step): ?>
<div class="timeline-step <?= ($current_index !== false && $index <= $current_index) ? 'active' : '' ?>">
    <div class="timeline-circle"><?= $index+1 ?></div>
    <div><?= $step ?></div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php if($status == "ยกเลิก"): ?>
<div class="alert alert-dark mt-3">
ออเดอร์นี้ถูกยกเลิกแล้ว
<?php if(!empty($order['cancelled_at'])): ?>
<br>ยกเลิกเมื่อ <?= date("d/m/Y H:i", strtotime($order['cancelled_at'])) ?>
<?php endif; ?>
</div>
<?php endif; ?>

<hr>

<h6>📦 ที่อยู่จัดส่ง</h6>

<?php if(!empty($order['fullname'])): ?>
<p>
<strong><?= htmlspecialchars($order['fullname']) ?></strong><br>
<?= htmlspecialchars($order['phone']) ?><br>
<?= htmlspecialchars($order['address']) ?> 
ต.<?= htmlspecialchars($order['district']) ?>
จ.<?= htmlspecialchars($order['province']) ?>
<?= htmlspecialchars($order['postal_code']) ?>
</p>
<?php else: ?>
<div class="alert alert-warning">
ไม่มีข้อมูลที่อยู่ในออเดอร์นี้
</div>
<?php endif; ?>

<hr>

<h6>🛒 รายการสินค้า</h6>

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
<?= number_format($item['p_price'],2) ?> × <?= $item['q_ty'] ?>
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
