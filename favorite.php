<?php
session_start();
include_once("connectdb.php");

/* ==========================
   ต้องล็อกอินก่อน
========================== */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

/* ==========================
   เพิ่มรายการโปรด
========================== */
if(isset($_GET['action']) && $_GET['action']=="add"){

    $pid = intval($_GET['id']);

    $check = mysqli_query($conn,"
        SELECT * FROM favorites
        WHERE user_id='$uid'
        AND product_id='$pid'
    ");

    if(mysqli_num_rows($check)==0){
        mysqli_query($conn,"
            INSERT INTO favorites (user_id,product_id)
            VALUES ('$uid','$pid')
        ");
    }

    header("Location: favorite.php");
    exit;
}

/* ==========================
   ลบรายการโปรด
========================== */
if(isset($_GET['action']) && $_GET['action']=="remove"){

    $pid = intval($_GET['id']);

    mysqli_query($conn,"
        DELETE FROM favorites
        WHERE user_id='$uid'
        AND product_id='$pid'
    ");

    header("Location: favorite.php");
    exit;
}

/* ==========================
   ดึงข้อมูลสินค้า
========================== */
$sql = "
SELECT p.*
FROM favorites f
JOIN products p ON f.product_id = p.p_id
WHERE f.user_id='$uid'
ORDER BY f.fav_id DESC
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
    transition:.3s;
}
.card:hover{
    transform:translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,.1);
}
.price{
    color:#ff7a00;
    font-weight:600;
}
</style>

<div class="container mt-5 mb-5">

<h3 class="mb-4">
<i class="bi bi-heart-fill text-danger"></i>
รายการโปรดของฉัน
</h3>

<div class="row">

<?php if(mysqli_num_rows($rs)>0): ?>

<?php while($p = mysqli_fetch_assoc($rs)): ?>

<div class="col-md-4 mb-4">
<div class="card h-100">

<img src="<?= $p['p_img'] ?>" class="card-img-top">

<div class="card-body">

<h6><?= htmlspecialchars($p['p_name']) ?></h6>
<p class="price"><?= number_format($p['p_price'],2) ?> บาท</p>

<div class="d-flex justify-content-between">

<a href="product_detail.php?id=<?= $p['p_id'] ?>" 
class="btn btn-sm btn-outline-dark">
ดูสินค้า
</a>

<a href="favorite.php?action=remove&id=<?= $p['p_id'] ?>" 
class="btn btn-sm btn-danger"
onclick="return confirm('ลบออกจากรายการโปรด?')">
<i class="bi bi-trash"></i>
</a>

</div>

</div>
</div>
</div>

<?php endwhile; ?>

<?php else: ?>

<div class="col-12">
<div class="alert alert-light text-center">
ยังไม่มีสินค้าในรายการโปรด
</div>
</div>

<?php endif; ?>

</div>
</div>
