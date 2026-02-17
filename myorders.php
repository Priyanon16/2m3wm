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
   ยกเลิกออเดอร์
========================== */
if(isset($_GET['cancel'])){

    $oid = intval($_GET['cancel']);

    // ตรวจสอบว่าเป็นของ user นี้
    $check = mysqli_query($conn,"
        SELECT status 
        FROM orders 
        WHERE o_id='$oid' 
        AND u_id='$uid'
        LIMIT 1
    ");

    if(mysqli_num_rows($check) > 0){

        $data = mysqli_fetch_assoc($check);

        // ยกเลิกได้เฉพาะ 2 สถานะนี้
        if($data['status'] == 'รอชำระเงิน' || 
           $data['status'] == 'ที่ต้องจัดส่ง'){

            // คืน stock
            $detail_rs = mysqli_query($conn,"
                SELECT p_id, q_ty 
                FROM order_details 
                WHERE o_id='$oid'
            ");

            while($item = mysqli_fetch_assoc($detail_rs)){
                mysqli_query($conn,"
                    UPDATE products 
                    SET p_qty = p_qty + {$item['q_ty']}
                    WHERE p_id = {$item['p_id']}
                ");
            }

            // เปลี่ยนสถานะ
            mysqli_query($conn,"
                UPDATE orders 
                SET status='ยกเลิก',
                    cancelled_at=NOW(),
                    cancel_reason='ลูกค้ายกเลิก'
                WHERE o_id='$oid'
            ");
        }
    }

    header("Location: myorders.php");
    exit;
}

/* ==========================
   Filter สถานะ
========================== */
$filter = $_GET['status'] ?? 'ทั้งหมด';

$where = "WHERE u_id='$uid'";

if($filter != 'ทั้งหมด'){
    $filter_safe = mysqli_real_escape_string($conn,$filter);
    $where .= " AND status='$filter_safe'";
}

/* ==========================
   ดึงรายการออเดอร์
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
.order-card{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}
.status-badge{
    padding:6px 14px;
    border-radius:50px;
    font-size:13px;
    font-weight:500;
}
</style>

<div class="container mt-5 mb-5">

<h3 class="mb-4">
<i class="bi bi-clock-history text-warning"></i>
รายการสั่งซื้อของฉัน
</h3>

<!-- ==========================
     แถบกรองสถานะ
========================== -->
<div class="mb-4">
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
<a href="?status=<?= urlencode($st) ?>"
   class="btn btn-sm <?= ($filter==$st)?'btn-warning':'btn-outline-secondary' ?> me-2 mb-2">
   <?= $st ?>
</a>
<?php endforeach; ?>
</div>


<?php if(mysqli_num_rows($rs) > 0): ?>

<?php while($order = mysqli_fetch_assoc($rs)): ?>

<div class="card order-card mb-4 p-4">

<div class="d-flex justify-content-between align-items-center">

<div>
<strong>เลขที่ออเดอร์ #<?= $order['o_id'] ?></strong><br>
<small class="text-muted">
<?= date("d/m/Y H:i", strtotime($order['o_date'])) ?>
</small>
</div>

<div class="text-end">

<?php
$status = $order['status'];
$badge = "bg-secondary";

if($status=="รอชำระเงิน") $badge="bg-danger";
elseif($status=="ที่ต้องจัดส่ง") $badge="bg-warning text-dark";
elseif($status=="รอรับ") $badge="bg-primary";
elseif($status=="จัดส่งสำเร็จ") $badge="bg-success";
elseif($status=="ยกเลิก") $badge="bg-dark";
?>

<span class="status-badge <?= $badge ?>">
<?= $status ?>
</span>

<div class="mt-2 fw-bold text-warning">
฿<?= number_format($order['total_price'],2) ?>
</div>

<div class="mt-3">

<a href="orderdetail.php?id=<?= $order['o_id'] ?>"
   class="btn btn-sm btn-outline-dark">
   ดูรายละเอียด
</a>

<?php if($status=="รอชำระเงิน" || $status=="ที่ต้องจัดส่ง"): ?>
<a href="?cancel=<?= $order['o_id'] ?>"
   class="btn btn-sm btn-outline-danger"
   onclick="return confirm('ยืนยันการยกเลิกคำสั่งซื้อ?')">
   ยกเลิก
</a>
<?php endif; ?>

</div>

</div>

</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="alert alert-light text-center">
ไม่พบรายการสั่งซื้อ
</div>

<?php endif; ?>

</div>
