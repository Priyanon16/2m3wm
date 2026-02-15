<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

/* ==========================
   รับค่า filter status
========================== */
$filter = $_GET['status'] ?? 'ทั้งหมด';

$where = "WHERE u_id = '$uid'";

if($filter != 'ทั้งหมด'){
    $filter_safe = mysqli_real_escape_string($conn,$filter);
    $where .= " AND status = '$filter_safe'";
}

/* ==========================
   ดึงออเดอร์
========================== */
$sql = "
SELECT *
FROM orders
$where
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
.status-pay{ background:#ff5252; color:#fff; }
.status-ship{ background:#ff9800; color:#fff; }
.status-wait{ background:#2196f3; color:#fff; }
.status-done{ background:#4caf50; color:#fff; }
.status-return{ background:#9c27b0; color:#fff; }
.nav-tabs .nav-link.active{
    background:#ff7a00 !important;
    color:#fff !important;
}
</style>

<div class="container mt-5 mb-5">

<h3 class="mb-4">
<i class="bi bi-clock-history text-warning"></i>
รายการสั่งซื้อของฉัน
</h3>

<!-- ==========================
     TAB STATUS
========================== -->
<!-- STATUS MENU -->
<div class="order-tabs-wrapper mb-4">
    <div class="order-tabs">
        <?php
        $statuses = [
            "ทั้งหมด",
            "รอชำระเงิน",
            "ที่ต้องจัดส่ง",
            "รอรับ",
            "จัดส่งสำเร็จ",
            "คืนสินค้า"
        ];

        foreach($statuses as $st):
        ?>
            <a class="order-tab <?= ($filter==$st)?'active':'' ?>"
               href="?status=<?= urlencode($st) ?>">
                <?= $st ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>



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

if($status=="รอชำระเงิน"){
    echo '<span class="status-badge status-pay">รอชำระเงิน</span>';
}elseif($status=="ที่ต้องจัดส่ง"){
    echo '<span class="status-badge status-ship">ที่ต้องจัดส่ง</span>';
}elseif($status=="รอรับ"){
    echo '<span class="status-badge status-wait">รอรับ</span>';
}elseif($status=="จัดส่งสำเร็จ"){
    echo '<span class="status-badge status-done">จัดส่งสำเร็จ</span>';
}elseif($status=="คืนสินค้า"){
    echo '<span class="status-badge status-return">คืนสินค้า</span>';
}else{
    echo '<span class="badge bg-secondary">'.$status.'</span>';
}
?>
</div>

</div>

<hr>

<?php
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
ไม่พบรายการในหมวดนี้
</div>

<?php endif; ?>

</div>
