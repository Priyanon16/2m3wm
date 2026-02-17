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

    // เช็คว่าเป็นออเดอร์ของ user นี้จริง
    $stmt = $conn->prepare("SELECT status FROM orders WHERE o_id=? AND u_id=? LIMIT 1");
    $stmt->bind_param("ii", $oid, $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $data = $result->fetch_assoc();

        if($data['status'] == 'รอชำระเงิน' || $data['status'] == 'ที่ต้องจัดส่ง'){

            // คืน stock
            $stmt_detail = $conn->prepare("SELECT p_id, q_ty FROM order_details WHERE o_id=?");
            $stmt_detail->bind_param("i", $oid);
            $stmt_detail->execute();
            $detail_rs = $stmt_detail->get_result();

            while($item = $detail_rs->fetch_assoc()){
                $stmt_update = $conn->prepare("UPDATE products SET p_qty = p_qty + ? WHERE p_id=?");
                $stmt_update->bind_param("ii", $item['q_ty'], $item['p_id']);
                $stmt_update->execute();
            }

            // เปลี่ยนสถานะ
            $stmt_cancel = $conn->prepare("
                UPDATE orders 
                SET status='ยกเลิก',
                    cancelled_at=NOW(),
                    cancel_reason='ลูกค้ายกเลิก'
                WHERE o_id=?");
            $stmt_cancel->bind_param("i", $oid);
            $stmt_cancel->execute();
        }
    }

    header("Location: myorders.php");
    exit;
}

/* ==========================
   Filter สถานะ
========================== */

$filter = $_GET['status'] ?? 'ทั้งหมด';

if($filter == 'ทั้งหมด'){
    $stmt = $conn->prepare("SELECT * FROM orders WHERE u_id=? ORDER BY o_id DESC");
    $stmt->bind_param("i", $uid);
}else{
    $stmt = $conn->prepare("SELECT * FROM orders WHERE u_id=? AND status=? ORDER BY o_id DESC");
    $stmt->bind_param("is", $uid, $filter);
}

$stmt->execute();
$rs = $stmt->get_result();
?>

<?php include("header.php"); ?>

<div class="container mt-5 mb-5">

<h3 class="mb-4">รายการสั่งซื้อของฉัน</h3>

<!-- ปุ่มกรอง -->
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

<?php if($rs->num_rows > 0): ?>

<?php while($order = $rs->fetch_assoc()): ?>

<div class="card mb-4 p-4 shadow-sm">

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
$badge = "secondary";

if($status=="รอชำระเงิน") $badge="danger";
elseif($status=="ที่ต้องจัดส่ง") $badge="warning";
elseif($status=="รอรับ") $badge="primary";
elseif($status=="จัดส่งสำเร็จ") $badge="success";
elseif($status=="ยกเลิก") $badge="dark";
?>

<span class="badge bg-<?= $badge ?>">
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
