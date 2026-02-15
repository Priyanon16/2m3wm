<?php
session_start();
include_once("connectdb.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

/* ==========================
   ดึงรายการออเดอร์ของ user
========================== */

$sql = "
SELECT *
FROM orders
WHERE u_id = '$uid'
ORDER BY o_id DESC
";

$rs = mysqli_query($conn,$sql);
?>

<?php include("header.php"); ?>

<style>
body{
    background:#f4f6f9;
    font-family:'Kanit',sans-serif;
}
.card{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}
.status-badge{
    padding:6px 12px;
    border-radius:50px;
    font-size:14px;
}
.status-pack{ background:#ff9800; color:#fff; }
.status-ship{ background:#2196f3; color:#fff; }
.status-done{ background:#4caf50; color:#fff; }
</style>

<div class="container mt-5 mb-5">

<h3 class="mb-4">
<i class="bi bi-clock-history text-warning"></i>
ประวัติการสั่งซื้อ
</h3>

<?php if(mysqli_num_rows($rs) > 0): ?>

<?php while($order = mysqli_fetch_assoc($rs)): ?>

<div class="card mb-4 p-4">

<div class="d-flex justify-content-between align-items-center mb-3">

<div>
<strong>เลขที่ออเดอร์ #<?= $order['o_id'] ?></strong><br>
<small class="text-muted">
<?= date("d/m/Y H:i", strtotime($order['o_date'])) ?>
</small>
</div>

<div>
<?php
$status = $order['status'];

if($status=="รอแพ็ค"){
    echo '<span class="status-badge status-pack">รอแพ็ค</span>';
}elseif($status=="รอจัดส่ง"){
    echo '<span class="status-badge status-ship">รอจัดส่ง</span>';
}elseif($status=="จัดส่งแล้ว"){
    echo '<span class="status-badge status-done">จัดส่งแล้ว</span>';
}else{
    echo '<span class="badge bg-secondary">'.$status.'</span>';
}
?>
</div>

</div>

<hr>

<?php
/* ดึงสินค้าในออเดอร์นี้ */
$oid = $order['o_id'];

$detail_sql = "
SELECT p.p_name, p.p_price, p.p_img, od.q_ty
FROM order_details od
JOIN products p ON od.p_id = p.p_id
WHERE od.o_id = '$oid'
";

$detail_rs = mysqli_query($conn,$detail_sql);

while($item = mysqli_fetch_assoc($detail_rs)):
?>

<div class="row align-items-center mb-3">

<div class="col-md-2">
<img src="<?= $item['p_img'] ?>" 
class="img-fluid rounded">
</div>

<div class="col-md-6">
<?= htmlspecialchars($item['p_name']) ?><br>
<small class="text-muted">
จำนวน <?= $item['q_ty'] ?> ชิ้น
</small>
</div>

<div class="col-md-4 text-end">
<?= number_format($item['p_price'],2) ?> บาท
</div>

</div>

<?php endwhile; ?>

<hr>

<div class="text-end">
<strong>
ยอดรวม: <?= number_format($order['total_price'],2) ?> บาท
</strong>
</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="alert alert-light text-center">
ยังไม่มีประวัติการสั่งซื้อ
</div>

<?php endif; ?>

</div>
