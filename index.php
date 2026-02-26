<?php
session_start();

include_once("connectdb.php");
include_once("functions.php");
include_once("bootstrap.php");

/* =========================================================
   [แก้ไข] ส่วนเพิ่มตะกร้า : ลบออกได้เลย หรือ Comment ไว้
   เพราะเราจะให้ไปเพิ่มที่หน้า product_detail.php แทน
========================================================= */
// if(isset($_GET['add_to_cart'])){ ... }  <-- ลบส่วนนี้ออกได้เลยครับ

/* =========================
   [คงเดิม] ส่วน Favorite
========================= */
if(isset($_GET['add_to_fav'])){
    // ส่วนนี้เก็บไว้เหมือนเดิม ถ้าอยากให้กด Fav ได้เลยจากหน้านี้
    // แต่ต้องมีฟังก์ชัน addToFavorite() รองรับนะครับ
    addToFavorite((int)$_GET['add_to_fav']);
}

/* =========================
   [คงเดิม] ดึงสินค้า + รูปหลัก
========================= */
$sql = "
SELECT p.*, 
       c.c_name,
       b.brand_name,
       (
        SELECT img_path 
        FROM product_images 
        WHERE product_images.p_id = p.p_id 
        LIMIT 1
       ) AS main_img,
       (
        SELECT SUM(p_qty_stock)
        FROM product_stock
        WHERE product_stock.p_id = p.p_id
       ) AS total_stock
FROM products p
LEFT JOIN category c ON p.c_id = c.c_id
LEFT JOIN brand b ON p.brand_id = b.brand_id
ORDER BY p.p_id DESC
";

$rs = mysqli_query($conn,$sql);

if(!$rs){
    die("SQL Error: ".mysqli_error($conn));
}

include("header.php");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>2M3WM Sneaker</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body { font-family: 'Kanit', sans-serif; background: #f8f9fa; }

.product-card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    background: #fff;
    transition: .3s;
    height: 100%;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}
.product-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}
.product-img {
    height: 380px;
    object-fit: cover;
}
.product-body {
    padding: 20px;
}
.brand-tag {
    display: inline-block;
    background: #000;
    color: #fff;
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 20px;
    margin-bottom: 10px;
}
.product-title {
    font-weight: 600;
    font-size: 18px;
}
.product-price {
    color: #ff7a00;
    font-weight: 700;
    font-size: 20px;
}
.btn-cart {
    background: #ffc107;
    border: none;
    padding: 10px 25px;
    border-radius: 12px;
    font-weight: 600;
    flex-grow: 1;
    text-align: center;
    text-decoration: none;
    color: #000;
}
.btn-fav {
    border: 1px solid #ff6b6b;
    background: #fff;
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ff6b6b;
}
.product-actions {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 15px;
}

/* =========================
   SLIDER
========================= */
.slider-img {
    height: 480px;
    object-fit: cover;
}

.slider-caption {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    text-align: center;
    background: rgba(0,0,0,0.45);
    padding: 30px 40px;
    border-radius: 20px;
    color: #fff;
}

.promo-badge{
    position:absolute;
    top:15px;
    left:15px;
    background:#dc3545;
    color:#fff;
    padding:10px 18px;        /* ใหญ่ขึ้น */
    border-radius:30px;       /* โค้งมากขึ้น */
    font-size:16px;           /* ตัวหนังสือใหญ่ */
    font-weight:700;
    box-shadow:0 4px 10px rgba(0,0,0,.25);  /* เพิ่มเงา */
    z-index:5;
}
.product-card{
    position:relative;
}
.promo-box{
    border-radius:20px;
    border:none;
    box-shadow:0 20px 40px rgba(0,0,0,.2);
    animation: popZoom .4s ease;
}

@keyframes popZoom{
    from{ transform: scale(.8); opacity:0;}
    to{ transform: scale(1); opacity:1;}
}
</style>
</head>

<body>


<div id="bannerSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">

    <div class="carousel-inner">

        <div class="carousel-item active position-relative">
            <img src="images/1.jpg" class="d-block w-100 slider-img">
            <div class="slider-caption">
                <h1>2M3WM Sneaker</h1>
                <p>รองเท้าของแท้ สำหรับทุกสไตล์</p>
                <div class="mt-3">
                    <a href="all_products.php" class="btn btn-warning me-2">
                        ดูสินค้าทั้งหมด
                    </a>
                    <a href="promotion.php" class="btn btn-outline-light">
                        โปรโมชั่น
                    </a>
                </div>
            </div>
        </div>

        <div class="carousel-item">
            <img src="images/2.jpg" class="d-block w-100 slider-img">
        </div>

        <div class="carousel-item">
            <img src="images/1.jpg" class="d-block w-100 slider-img">
        </div>

    </div>

    <button class="carousel-control-prev" type="button"
        data-bs-target="#bannerSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button"
        data-bs-target="#bannerSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>

</div>


<div class="container py-5">
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">

<?php if(mysqli_num_rows($rs) > 0): ?>
<?php while($row = mysqli_fetch_assoc($rs)): ?>

<?php
$img = !empty($row['main_img']) 
       ? $row['main_img'] 
       : 'images/no-image.png';
?>

<div class="col">
<div class="product-card">

<?php
$old = $row['p_price'];
$discount = $row['discount_percent'] ?? 0;
$is_promo = $row['is_promo'] ?? 0;
?>

<?php if($is_promo == 1 && $discount > 0): ?>
<div class="promo-badge">
    ลด <?= $discount ?>%
</div>
<?php endif; ?>

<a href="product_detail.php?id=<?= $row['p_id']; ?>" 
   class="text-decoration-none text-dark">

<img src="<?= htmlspecialchars($img); ?>" 
     class="w-100 product-img">

<div class="product-body">

<span class="brand-tag">
<?= htmlspecialchars($row['brand_name'] ?? 'General'); ?>
</span>


<div class="product-title">
<?= htmlspecialchars($row['p_name']); ?>
</div>

<div class="text-muted small mb-1">
<?= ucfirst($row['p_type']); ?>
</div>

<div class="mb-2">
<?php 
$stock = $row['total_stock'] ?? 0;
if($stock > 0): 
?>
<small class="text-success">
คงเหลือ <?= number_format($stock); ?> คู่
</small>
<?php else: ?>
<small class="text-danger fw-bold">
สินค้าหมด
</small>
<?php endif; ?>
</div>

<?php
$old = $row['p_price'];
$discount = $row['discount_percent'] ?? 0;
$is_promo = $row['is_promo'] ?? 0;

if($is_promo == 1 && $discount > 0){

    $new = $old - ($old * $discount / 100);
?>
    <div class="product-price">

        <div style="text-decoration:line-through;color:#999;font-size:14px;">
            ฿<?= number_format($old,0); ?>
        </div>

        <div style="color:#ff5722;font-weight:700;font-size:20px;">
            ฿<?= number_format($new,0); ?>
        </div>


    </div>

<?php
} else {
?>
    <div class="product-price">
        ฿<?= number_format($old,0); ?>
    </div>
<?php } ?>

</div>
</a>

<div class="product-actions">

<?php if($stock > 0): ?>
<a href="product_detail.php?id=<?= $row['p_id']; ?>" 
   class="btn btn-cart">
เพิ่มลงตะกร้า
</a>
<?php else: ?>
<button class="btn btn-secondary w-100 disabled">
สินค้าหมด
</button>
<?php endif; ?>

<a href="?add_to_fav=<?= $row['p_id']; ?>" 
   class="btn-fav">
<i class="bi bi-heart"></i>
</a>

</div>

</div>
</div>

<?php endwhile; ?>
<?php else: ?>

<div class="col-12 text-center">
<h5 class="text-muted">ยังไม่มีสินค้าในระบบ</h5>
</div>

<?php endif; ?>

</div>
</div>

<!-- PROMOTION POPUP -->
<div class="modal fade" id="promoModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content promo-box">

      <div class="modal-body text-center p-4 position-relative">

        <button type="button" class="btn-close position-absolute end-0 me-3"
                data-bs-dismiss="modal"></button>

        <h2 class="fw-bold text-danger mb-3">
            🔥 FLASH SALE 🔥
        </h2>

        <h4 class="mb-3">
            ลดสูงสุด <span class="text-danger fw-bold">50%</span>
        </h4>

        <p class="text-muted">
            เฉพาะวันนี้เท่านั้น! รีบช้อปก่อนหมด
        </p>

        <img src="images/promo.jpg"
             class="img-fluid rounded mb-3"
             style="max-height:250px;object-fit:cover;">

        <a href="promotion.php" class="btn btn-warning btn-lg w-100">
            ดูโปรโมชั่นทั้งหมด
        </a>

      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    var promoModal = new bootstrap.Modal(
        document.getElementById('promoModal')
    );

    promoModal.show();

});
</script>
</body>
</html>