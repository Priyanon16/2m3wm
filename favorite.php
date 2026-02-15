<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$uid = intval($_SESSION['user_id']);

/* ==========================
   เพิ่มรายการโปรด
========================== */
if(isset($_GET['action']) && $_GET['action']=="add"){

    $pid = intval($_GET['id']);

    $check = mysqli_query($conn,"
        SELECT * FROM favorites
        WHERE user_id=$uid
        AND product_id=$pid
    ");

    if(mysqli_num_rows($check)==0){
        mysqli_query($conn,"
            INSERT INTO favorites (user_id,product_id)
            VALUES ($uid,$pid)
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
        WHERE user_id=$uid
        AND product_id=$pid
    ");

    header("Location: favorite.php");
    exit;
}

/* ==========================
   ดึงข้อมูลสินค้า + รูปหลัก
========================== */
$sql = "
SELECT 
    p.*,
    b.brand_name,
    c.c_name,
    (
        SELECT img_path 
        FROM product_images 
        WHERE product_images.p_id = p.p_id
        LIMIT 1
    ) AS main_img
FROM favorites f
JOIN products p ON f.product_id = p.p_id
LEFT JOIN brand b ON p.brand_id = b.brand_id
LEFT JOIN category c ON p.c_id = c.c_id
WHERE f.user_id = $uid
ORDER BY f.fav_id DESC
";

$rs = mysqli_query($conn,$sql);

include("header.php");
?>

<style>
body{
    background:#f4f6f9;
    font-family:'Kanit',sans-serif;
}

.page-title{
    font-weight:700;
    margin-bottom:30px;
}

.product-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    background:#fff;
    transition:.3s;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
    height:100%;
}

.product-card:hover{
    transform:translateY(-6px);
    box-shadow:0 15px 35px rgba(0,0,0,0.15);
}

.product-img{
    height:300px;
    object-fit:cover;
}

.brand-tag{
    background:#000;
    color:#fff;
    font-size:12px;
    padding:4px 10px;
    border-radius:20px;
    display:inline-block;
    margin-bottom:8px;
}

.price{
    color:#ff7a00;
    font-weight:700;
    font-size:18px;
}

.empty-box{
    background:#fff;
    padding:50px;
    border-radius:20px;
    text-align:center;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}
</style>

<div class="container py-5">

<h3 class="page-title">
<i class="bi bi-heart-fill text-danger me-2"></i>
รายการโปรดของฉัน
</h3>

<div class="row g-4">

<?php if(mysqli_num_rows($rs)>0): ?>
<?php while($p = mysqli_fetch_assoc($rs)): ?>

<?php
$img = !empty($p['main_img']) 
       ? $p['main_img'] 
       : 'images/no-image.png';
?>

<div class="col-md-4">
<div class="product-card">

<a href="product_detail.php?id=<?= $p['p_id'] ?>"
   class="text-decoration-none text-dark">

<img src="<?= htmlspecialchars($img); ?>" 
     class="w-100 product-img">

<div class="p-3">

<span class="brand-tag">
<?= htmlspecialchars($p['brand_name'] ?? 'General'); ?>
</span>

<h6 class="fw-semibold mb-1">
<?= htmlspecialchars($p['p_name']); ?>
</h6>

<small class="text-muted">
<?= htmlspecialchars($p['c_name']); ?> |
<?= ucfirst($p['p_type']); ?>
</small>

<div class="price mt-2">
฿<?= number_format($p['p_price'],0); ?>
</div>

</div>
</a>

<div class="d-flex justify-content-between p-3 pt-0">

<a href="product_detail.php?id=<?= $p['p_id'] ?>"
class="btn btn-outline-dark btn-sm">
ดูสินค้า
</a>

<a href="favorite.php?action=remove&id=<?= $p['p_id'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('ลบออกจากรายการโปรด?')">
<i class="bi bi-trash"></i>
</a>

</div>

</div>
</div>

<?php endwhile; ?>
<?php else: ?>

<div class="col-12">
<div class="empty-box">
<h5 class="mb-3 text-muted">
ยังไม่มีสินค้าในรายการโปรด
</h5>
<a href="all_products.php" class="btn btn-warning">
เลือกซื้อสินค้า
</a>
</div>
</div>

<?php endif; ?>

</div>
</div>
