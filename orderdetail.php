<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$oid = intval($_GET['id'] ?? 0);

$order_sql = "
SELECT o.*, a.fullname, a.phone, a.address, a.district, a.province, a.postal_code
FROM orders o
LEFT JOIN addresses a ON o.address_id = a.address_id
WHERE o.o_id='$oid' AND o.u_id='$uid'
LIMIT 1
";

$order_rs = mysqli_query($conn,$order_sql);

if(mysqli_num_rows($order_rs)==0){
    echo "ไม่พบคำสั่งซื้อ";
    exit;
}

$order = mysqli_fetch_assoc($order_rs);
?>

<?php include("header.php"); ?>

<div class="container mt-5 mb-5">

<h3>รายละเอียดคำสั่งซื้อ #<?= $order['o_id'] ?></h3>

<div class="card p-4 mb-4">

<strong>สถานะ:</strong> <?= $order['status'] ?><br>
<strong>วันที่สั่งซื้อ:</strong> <?= date("d/m/Y H:i", strtotime($order['o_date'])) ?>

</div>

<div class="card p-4 mb-4">

<h5>ที่อยู่จัดส่ง</h5>

<?= htmlspecialchars($order['fullname']) ?><br>
<?= htmlspecialchars($order['phone']) ?><br>
<?= htmlspecialchars($order['address']) ?><br>
<?= htmlspecialchars($order['district']) ?>,
<?= htmlspecialchars($order['province']) ?>
<?= htmlspecialchars($order['postal_code']) ?>

</div>

<div class="card p-4">

<h5>รายการสินค้า</h5>

<?php
$detail_sql = "
SELECT p.p_name, p.p_price, p.p_img, od.q_ty
FROM order_details od
JOIN products p ON od.p_id = p.p_id
WHERE od.o_id = '$oid'
";

$detail_rs = mysqli_query($conn,$detail_sql);

$total = 0;

while($item = mysqli_fetch_assoc($detail_rs)):
$subtotal = $item['p_price'] * $item['q_ty'];
$total += $subtotal;
?>

<div class="row mb-3 align-items-center">

<div class="col-md-2">
<img src="<?= $item['p_img'] ?>" class="img-fluid rounded">
</div>

<div class="col-md-6">
<?= htmlspecialchars($item['p_name']) ?><br>
<small>จำนวน <?= $item['q_ty'] ?> ชิ้น</small>
</div>

<div class="col-md-4 text-end">
<?= number_format($subtotal,2) ?> บาท
</div>

</div>

<?php endwhile; ?>

<hr>

<div class="text-end">
<strong>ยอดรวมทั้งหมด: <?= number_format($total,2) ?> บาท</strong>
</div>

</div>

</div>
