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
   ดึงข้อมูลออเดอร์
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
WHERE o.o_id = '$oid'
AND o.u_id = '$uid'
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
   ดึงรายการสินค้า
========================== */
$detail_sql = "
SELECT p.p_name,
       (SELECT img_path FROM product_images WHERE p_id = p.p_id LIMIT 1) AS p_img,
       od.q_ty,
       od.price
FROM order_details od
JOIN products p ON od.p_id = p.p_id
WHERE od.o_id = '$oid'
";

$detail_rs = mysqli_query($conn,$detail_sql);
?>

<?php include("header.php"); ?>

<style>
.step-wrapper{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin:30px 0;
}
.step{
    text-align:center;
    flex:1;
    position:relative;
}
.step-circle{
    width:35px;
    height:35px;
    border-radius:50%;
    background:#ddd;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 8px;
    font-weight:bold;
}
.step.active .step-circle{
    background:#28a745;
}
.step-label{
    font-size:14px;
}
.step-line{
    position:absolute;
    top:17px;
    left:-50%;
    width:100%;
    height:3px;
    background:#ddd;
    z-index:-1;
}
.step.active .step-line{
    background:#28a745;
}
</style>

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

<h6 class="mb-3">📍 ที่อยู่จัดส่ง</h6>

<?php if(!empty($order['fullname'])): ?>
<div class="p-3 mb-4 bg-light rounded border">
    <strong><?= htmlspecialchars($order['fullname']) ?></strong><br>
    โทร: <?= htmlspecialchars($order['phone']) ?><br>
    <?= htmlspecialchars($order['address']) ?>
    ต.<?= htmlspecialchars($order['district']) ?>
    จ.<?= htmlspecialchars($order['province']) ?>
    <?= htmlspecialchars($order['postal_code']) ?>
</div>
<?php else: ?>
<div class="alert alert-warning">
    ไม่มีข้อมูลที่อยู่ในออเดอร์นี้
</div>
<?php endif; ?>

<hr>


<?php
$status = trim($order['status']);
$step = 1;

if($status == "รอชำระเงิน") {
    $step = 1;
}
elseif($status == "ที่ต้องจัดส่ง") {
    $step = 2;
}
elseif($status == "รอรับ") {
    $step = 3;
}
elseif($status == "จัดส่งสำเร็จ") {
    $step = 4;
}
elseif($status == "ยกเลิก") {
    $step = 0;
}


?>

<h6 class="mb-3">📦 สถานะการสั่งซื้อ</h6>

<?php if($status == "ยกเลิก"): ?>

<div class="alert alert-danger text-center">
ออเดอร์ถูกยกเลิก
</div>

<?php else: ?>

<div class="step-wrapper">

<div class="step <?= $step>=1?'active':'' ?>">
    <div class="step-circle">1</div>
    <div class="step-label">รอชำระเงิน</div>
</div>

<div class="step <?= $step>=2?'active':'' ?>">
    <div class="step-line"></div>
    <div class="step-circle">2</div>
    <div class="step-label">ที่ต้องจัดส่ง</div>
</div>

<div class="step <?= $step>=3?'active':'' ?>">
    <div class="step-line"></div>
    <div class="step-circle">3</div>
    <div class="step-label">รอรับ</div>
</div>

<div class="step <?= $step>=4?'active':'' ?>">
    <div class="step-line"></div>
    <div class="step-circle">4</div>
    <div class="step-label">จัดส่งสำเร็จ</div>
</div>



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
ราคา <?= number_format($item['price'],2) ?> × <?= $item['q_ty'] ?>
</small>
</div>

<div class="col-md-4 text-end fw-bold">
<?= number_format($subtotal,2) ?> บาท
</div>

</div>

<?php
    }
}
?>

<hr>

<div class="text-end">
<h5>ยอดรวมสินค้า: <?= number_format($total,2) ?> บาท</h5>
<h4 class="text-warning">
ยอดชำระทั้งหมด: <?= number_format($order['total_price'],2) ?> บาท
</h4>
</div>

</div>
</div>
