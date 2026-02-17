<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = intval($_SESSION['user_id']);

/* ==========================
   FILTER STATUS
========================== */
$filter = $_GET['status'] ?? 'ทั้งหมด';

$where = "WHERE o.u_id = '$uid'";

if($filter != 'ทั้งหมด'){
    $filter_safe = mysqli_real_escape_string($conn,$filter);
    $where .= " AND o.status = '$filter_safe'";
}

/* ==========================
   ดึงออเดอร์ + จำนวนสินค้า + รูปตัวอย่าง
========================== */
$sql = "
SELECT 
    o.*,
    COUNT(od.d_id) AS item_count,
    (
        SELECT p.p_img
        FROM order_details od2
        JOIN products p ON od2.p_id = p.p_id
        WHERE od2.o_id = o.o_id
        LIMIT 1
    ) AS preview_img
FROM orders o
LEFT JOIN order_details od ON o.o_id = od.o_id
$where
GROUP BY o.o_id
ORDER BY o.o_id DESC
";

$rs = mysqli_query($conn,$sql);
?>

<?php include("header.php"); ?>

<style>
body{
    background:#f4f6f9;
    font-family:'Kanit',sans-serif;
}
.order-card{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    transition:0.3s;
}
.order-card:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
}
.status-badge{
    padding:6px 12px;
    border-radius:50px;
    font-size:13px;
    color:#fff;
}
.status-pay{ background:#ff5252; }
.status-ship{ background:#ff9800; }
.status-wait{ background:#2196f3; }
.status-done{ background:#4caf50; }
.status-cancel{ background:#757575; }
.preview-img{
    width:80px;
    height:80px;
    object-fit:cover;
    border-radius:10px;
}
.order-tabs{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}
.order-tab{
    padding:8px 18px;
    border-radius:50px;
    background:#fff;
    border:1px solid #ddd;
    text-decoration:none;
    color:#555;
}
.order-tab.active{
    background:#ff7a00;
    color:#fff;
    border-color:#ff7a00;
}
</style>

<div class="container mt-5 mb-5">

<h3 class="mb-4">
<i class="bi bi-clock-history text-warning"></i>
รายการสั่งซื้อของฉัน
</h3>

<!-- FILTER TABS -->
<div class="order-tabs mb-4">
<?php
$statuses = [
    "ทั้งหมด",
    "รอชำระเงิน",
    "ที่ต้องจัดส่ง",
    "รอรับ",
    "จัดส่งสำเร็จ",
    "ยกเลิก"
];
foreach($statuses as $st):
?>
<a class="order-tab <?= ($filter==$st)?'active':'' ?>"
   href="?status=<?= urlencode($st) ?>">
   <?= $st ?>
</a>
<?php endforeach; ?>
</div>

<?php if(mysqli_num_rows($rs) > 0): ?>
<?php while($order = mysqli_fetch_assoc($rs)): ?>

<div class="card order-card mb-4 p-4">

<div class="d-flex justify-content-between align-items-center">

<div class="d-flex align-items-center gap-3">

<img src="<?= $order['preview_img'] ?? 'images/no-image.png' ?>" class="preview-img">

<div>
<strong>เลขที่ออเดอร์ #<?= $order['o_id'] ?></strong><br>
<small class="text-muted">
<?= date("d/m/Y H:i", strtotime($order['o_date'])) ?>
</small><br>
<small class="text-muted">
<?= $order['item_count'] ?> รายการ
</small>
</div>

</div>

<div class="text-end">

<?php
$status = $order['status'];

if($status=="รอชำระเงิน"){
    echo '<span class="status-badge status-pay">รอชำระเงิน</span>';
}elseif($status=="ที่ต้องจัดส่ง"){
    echo '<span class="status-badge status-ship">ที่ต้องจัดส่ง</span>';
}elseif($status=="รอรับ"){
    echo '<span class="status-badge status-wait">รอรับ</span>';
}elseif($status=="จัดส่งสำเร็จ"){
    echo '<span class="status-badge status-done">จัดส่งสำเร็จ</span>';
}elseif($status=="ยกเลิก"){
    echo '<span class="status-badge status-cancel">ยกเลิก</span>';
}
?>

<div class="mt-2">
<strong>฿<?= number_format($order['total_price'],2) ?></strong>
</div>

<a href="orderdetail.php?id=<?= $order['o_id'] ?>" 
   class="btn btn-sm btn-outline-dark mt-2">
   ดูรายละเอียด
</a>

</div>

</div>

</div>

<?php endwhile; ?>
<?php else: ?>

<div class="alert alert-light text-center">
ไม่พบรายการในหมวดนี้
</div>

<?php endif; ?>

</div>
