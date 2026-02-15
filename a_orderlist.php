<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$title = "จัดการออเดอร์";

require_once __DIR__ . '/connectdb.php';
include __DIR__ . '/bootstrap.php';

/* =======================
   รับค่า Filter
======================= */
$status   = $_GET['status'] ?? '';
$date     = $_GET['date'] ?? '';
$keyword  = $_GET['keyword'] ?? '';

/* =======================
   SQL
======================= */
$sql = "
SELECT 
    o.id,
    o.created_at,
    o.total_price,
    o.payment_method,
    o.status,
    u.name,
    COALESCE(SUM(oi.quantity),0) as total_qty
FROM orders o
LEFT JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON oi.order_id = o.id
WHERE 1
";

/* Filter */
if (!empty($status)) {
    $status = mysqli_real_escape_string($conn,$status);
    $sql .= " AND o.status = '$status' ";
}

if (!empty($date)) {
    $date = mysqli_real_escape_string($conn,$date);
    $sql .= " AND DATE(o.created_at) = '$date' ";
}

if (!empty($keyword)) {
    $kw = mysqli_real_escape_string($conn,$keyword);
    $sql .= " AND (o.id LIKE '%$kw%' OR u.name LIKE '%$kw%') ";
}

$sql .= " GROUP BY o.id ORDER BY o.created_at DESC ";

$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
?>

<style>
body{
  margin:0;
  background:#f4f6f9;
}

.wrapper{
  display:flex;
  min-height:100vh;
}

main{
  flex:1;
  padding:40px;
}

.page-header{
  background:#fff;
  padding:20px 25px;
  border-radius:14px;
  border-left:6px solid #ff7a00;
  box-shadow:0 5px 15px rgba(0,0,0,0.08);
  margin-bottom:25px;
}

.page-header h4{
  color:#ff7a00;
  font-weight:600;
}

.card-custom{
  background:#fff;
  border-radius:14px;
  border:1px solid #eee;
  box-shadow:0 5px 15px rgba(0,0,0,0.06);
}

.btn-orange{
  background:#ff7a00;
  color:#fff;
  border:none;
  border-radius:10px;
}

.btn-orange:hover{
  background:#ff9433;
}

.table thead{
  background:#212529;
  color:#fff;
}

.table tbody tr:hover{
  background:#fff3e6;
}

.badge-warning-custom{
  background:#ff7a00;
}

.badge-success-custom{
  background:#28a745;
}
</style>

<div class="wrapper">

<?php include __DIR__ . '/sidebar.php'; ?>

<main>

<div class="page-header d-flex justify-content-between align-items-center">
  <h4 class="mb-0">
    <i class="bi bi-receipt-cutoff me-2"></i> จัดการออเดอร์
  </h4>
  <span class="text-secondary">Order Management</span>
</div>

<!-- Filter -->
<div class="card card-custom p-4 mb-4">
<form method="GET">
<div class="row g-3">

<div class="col-md-3">
<label class="form-label">สถานะ</label>
<select name="status" class="form-select">
<option value="">ทั้งหมด</option>
<option value="รอแพ็ค">รอแพ็ค</option>
<option value="รอจัดส่ง">รอจัดส่ง</option>
<option value="จัดส่งแล้ว">จัดส่งแล้ว</option>
</select>
</div>

<div class="col-md-3">
<label class="form-label">วันที่สร้างออเดอร์</label>
<input type="date" name="date" class="form-control">
</div>

<div class="col-md-4">
<label class="form-label">ค้นหา</label>
<input type="text" name="keyword" class="form-control" placeholder="Order ID / ชื่อลูกค้า">
</div>

<div class="col-md-2 d-flex align-items-end">
<button class="btn btn-orange w-100">
<i class="bi bi-search me-1"></i> ค้นหา
</button>
</div>

</div>
</form>
</div>

<!-- Table -->
<div class="card card-custom">
<div class="table-responsive">
<table class="table align-middle mb-0 text-center">
<thead>
<tr>
<th>Order ID</th>
<th>วันที่</th>
<th>ลูกค้า</th>
<th>จำนวน</th>
<th>ยอดรวม</th>
<th>ชำระเงิน</th>
<th>สถานะ</th>
<th>จัดการ</th>
</tr>
</thead>
<tbody>

<?php if($result && mysqli_num_rows($result) > 0): ?>
<?php while($order = mysqli_fetch_assoc($result)): ?>
<tr>
<td>#<?= $order['id'] ?></td>
<td><?= date("Y-m-d", strtotime($order['created_at'])) ?></td>
<td><?= htmlspecialchars($order['name']) ?></td>
<td><?= $order['total_qty'] ?></td>
<td class="text-warning fw-semibold">
<?= number_format($order['total_price'],2) ?> บาท
</td>
<td><?= htmlspecialchars($order['payment_method']) ?></td>
<td>
<?php
$badge = "bg-secondary";
if($order['status']=="รอแพ็ค") $badge="badge-warning-custom";
if($order['status']=="รอจัดส่ง") $badge="bg-primary";
if($order['status']=="จัดส่งแล้ว") $badge="badge-success-custom";
?>
<span class="badge <?= $badge ?>">
<?= $order['status'] ?>
</span>
</td>
<td>
<a href="a_order_detail.php?id=<?= $order['id'] ?>" 
class="btn btn-sm btn-outline-dark">
ดูรายละเอียด
</a>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="8">ไม่พบข้อมูล</td>
</tr>
<?php endif; ?>

</tbody>
</table>
</div>
</div>

</main>
</div>
