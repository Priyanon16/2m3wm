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
   ยกเลิกออเดอร์
========================== */
if(isset($_GET['cancel'])){

    $oid = intval($_GET['cancel']);

    // เช็คว่าเป็นของ user นี้ และยังยกเลิกได้
    $check = mysqli_query($conn,"
        SELECT status 
        FROM orders 
        WHERE o_id='$oid' 
        AND u_id='$uid'
        LIMIT 1
    ");

    if(mysqli_num_rows($check) > 0){

        $data = mysqli_fetch_assoc($check);

        // อนุญาตให้ยกเลิกเฉพาะสถานะนี้
        if($data['status'] == 'รอชำระเงิน' || 
           $data['status'] == 'ที่ต้องจัดส่ง'){

            // 1️⃣ คืน stock
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

            // 2️⃣ เปลี่ยนสถานะ
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
.status-cancel{ background:#757575; color:#fff; }

.nav-tabs .nav-link.active{
    background:#ff7a00 !important;
    color:#fff !important;
}
/* =========================
   ORDER STATUS TABS (NEW)
========================= */

.order-tabs-wrapper{
    overflow-x:auto;
    padding-bottom:10px;
    border-bottom:1px solid #eee;
}

.order-tabs{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
}

.order-tab{
    padding:10px 22px;
    border-radius:50px;
    text-decoration:none;
    font-weight:500;
    color:#555;
    background:#fff;
    border:1px solid #e0e0e0;
    transition:0.3s;
    white-space:nowrap;
}

.order-tab:hover{
    background:#fff3e6;
    color:#ff7a00;
    border-color:#ff7a00;
    transform:translateY(-2px);
}

.order-tab.active{
    background:#ff7a00;
    color:#fff;
    border-color:#ff7a00;
    box-shadow:0 5px 15px rgba(255,122,0,0.3);
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
            "คืนสินค้า",
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
</div>



<?php if(mysqli_num_rows($rs) > 0): ?>
<?php while($order = mysqli_fetch_assoc($rs)): ?>

<div class="card mb-4 p-4">

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

$badgeClass = "bg-secondary";

if($status=="รอชำระเงิน") $badgeClass="bg-danger";
elseif($status=="ที่ต้องจัดส่ง") $badgeClass="bg-warning";
elseif($status=="รอรับ") $badgeClass="bg-primary";
elseif($status=="จัดส่งสำเร็จ") $badgeClass="bg-success";
elseif($status=="ยกเลิก") $badgeClass="bg-dark";
?>

<span class="badge <?= $badgeClass ?>">
<?= $status ?>
</span>

<div class="mt-2 fw-bold text-warning">
฿<?= number_format($order['total_price'],2) ?>
</div>

<a href="orderdetail.php?id=<?= $order['o_id'] ?>"
   class="btn btn-sm btn-outline-dark mt-2">
   ดูรายละเอียด
</a>

</div>

</div>

</div>

<?php endwhile; ?>


<hr>

<div class="text-end">
<strong>
ยอดรวม: <?= number_format($order['total_price'],2) ?> บาท
</strong>
</div>

<?php if($order['status']=="รอชำระเงิน" || 
          $order['status']=="ที่ต้องจัดส่ง"): ?>

<a href="?cancel=<?= $order['o_id'] ?>" 
   class="btn btn-outline-danger btn-sm mt-3"
   onclick="return confirm('ยืนยันการยกเลิกคำสั่งซื้อ?')">
   ยกเลิกคำสั่งซื้อ
</a>

<?php endif; ?>


</div>

<?php endwhile; ?>

<?php else: ?>

<div class="alert alert-light text-center">
ไม่พบรายการในหมวดนี้
</div>

<?php endif; ?>

</div>
