<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

if(!isset($_GET['id'])){
    header("Location: myorders.php");
    exit;
}

$oid = intval($_GET['id']);

/* ==========================
   ดึงข้อมูลออเดอร์
========================== */
$order_sql = "
SELECT *
FROM orders
WHERE o_id = '$oid'
AND u_id = '$uid'
LIMIT 1
";

$order_rs = mysqli_query($conn,$order_sql);

if(mysqli_num_rows($order_rs) == 0){
    echo "<div class='container mt-5'>
            <div class='alert alert-danger text-center'>
            ไม่พบคำสั่งซื้อนี้
            </div>
          </div>";
    exit;
}

$order = mysqli_fetch_assoc($order_rs);


/* ==========================
   ยกเลิกออเดอร์
========================== */
if(isset($_GET['cancel'])){

    if($order['status']=="รอชำระเงิน" || 
       $order['status']=="ที่ต้องจัดส่ง"){

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

        mysqli_query($conn,"
            UPDATE orders 
            SET status='ยกเลิก',
                cancelled_at=NOW(),
                cancel_reason='ลูกค้ายกเลิก'
            WHERE o_id='$oid'
        ");

        header("Location: orderdetail.php?id=$oid");
        exit;
    }
}


/* ==========================
   ดึงรายการสินค้า
========================== */
$detail_sql = "
SELECT p.p_name, p.p_price, p.p_img, od.q_ty
FROM order_details od
JOIN products p ON od.p_id = p.p_id
WHERE od.o_id = '$oid'
";

$detail_rs = mysqli_query($conn,$detail_sql);

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
    padding:6px 14px;
    border-radius:50px;
    font-size:14px;
}
.status-pay{ background:#ff5252; color:#fff; }
.status-ship{ background:#ff9800; color:#fff; }
.status-wait{ background:#2196f3; color:#fff; }
.status-done{ background:#4caf50; color:#fff; }
.status-return{ background:#9c27b0; color:#fff; }
.status-cancel{ background:#757575; color:#fff; }
</style>

<div class="container mt-5 mb-5">

<a href="myorders.php" class="btn btn-light mb-4">
← กลับไปหน้ารายการสั่งซื้อ
</a>

<div class="card p-4">

<div class="d-flex justify-content-between align-items-center mb-3">

<div>
<h5>เลขที่ออเดอร์ #<?= $order['o_id'] ?></h5>
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
}elseif($status=="ยกเลิก"){
    echo '<span class="status-badge status-cancel">ยกเลิก</span>';
}else{
    echo '<span class="badge bg-secondary">'.$status.'</span>';
}
?>
</div>

</div>

<hr>

<?php while($item = mysqli_fetch_assoc($detail_rs)): 
$subtotal = $item['p_price'] * $item['q_ty'];
?>

<div class="row align-items-center mb-3">

<div class="col-md-2">
<img src="<?= $item['p_img'] ?>" class="img-fluid rounded">
</div>

<div class="col-md-6">
<strong><?= htmlspecialchars($item['p_name']) ?></strong><br>
<small class="text-muted">
จำนวน <?= $item['q_ty'] ?> ชิ้น
</small>
</div>

<div class="col-md-4 text-end">
<?= number_format($subtotal,2) ?> บาท
</div>

</div>

<?php endwhile; ?>

<hr>

<div class="text-end">
<h5>ยอดรวม: <?= number_format($order['total_price'],2) ?> บาท</h5>
</div>

<?php if($order['status']=="รอชำระเงิน" || 
          $order['status']=="ที่ต้องจัดส่ง"): ?>

<a href="?id=<?= $oid ?>&cancel=1" 
   class="btn btn-outline-danger mt-3"
   onclick="return confirm('ยืนยันการยกเลิกคำสั่งซื้อ?')">
   ยกเลิกคำสั่งซื้อ
</a>

<?php endif; ?>

</div>

</div>
