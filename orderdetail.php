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
SELECT *
FROM orders
WHERE o_id = '$oid'
AND u_id = '$uid'
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
SELECT p.p_name, p.p_price, 
       (SELECT img_path FROM product_images WHERE p_id = p.p_id LIMIT 1) AS p_img,
       od.q_ty, od.price
FROM order_details od
JOIN products p ON od.p_id = p.p_id
WHERE od.o_id = '$oid'
";

$detail_rs = mysqli_query($conn,$detail_sql);
?>

<?php include("header.php"); ?>

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

<h6>📌 สถานะออเดอร์</h6>

<?php
$status = $order['status'];
$badge = "bg-secondary";

if($status=="รอชำระเงิน") $badge="bg-danger";
elseif($status=="ที่ต้องจัดส่ง") $badge="bg-warning text-dark";
elseif($status=="รอรับ") $badge="bg-primary";
elseif($status=="จัดส่งสำเร็จ") $badge="bg-success";
elseif($status=="ยกเลิก") $badge="bg-dark";
?>

<span class="badge <?= $badge ?> p-2 mb-3">
<?= $status ?>
</span>

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
