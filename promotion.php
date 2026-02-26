<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

/* =========================
   ดึงสินค้าโปรโมชั่น
========================= */
$sql = "
SELECT p.*,
       b.brand_name,
       (
           SELECT img_path 
           FROM product_images 
           WHERE p_id = p.p_id 
           LIMIT 1
       ) AS main_img
FROM products p
LEFT JOIN brand b ON p.brand_id = b.brand_id
WHERE p.is_promo = 1
AND p.discount_percent > 0
ORDER BY p.p_id DESC
";

$rs = mysqli_query($conn,$sql);

include("header.php");
?>

<style>
.promo-badge{
    position:absolute;
    top:15px;
    left:15px;
    background:#dc3545;
    color:#fff;
    padding:6px 12px;
    font-weight:700;
    border-radius:20px;
    font-size:14px;
}

.old-price{
    text-decoration:line-through;
    color:#999;
    font-size:14px;
}

.new-price{
    color:#ff7a00;
    font-weight:700;
    font-size:18px;
}
</style>

<div class="container py-5">

<h2 class="fw-bold mb-4">🔥 โปรโมชั่นพิเศษ</h2>

<div class="row">

<?php if(mysqli_num_rows($rs)>0): ?>
<?php while($p = mysqli_fetch_assoc($rs)): ?>

<?php
$img = $p['main_img'] ?: 'images/no-image.png';
$discount = intval($p['discount_percent']);

$old_price = $p['p_price'];
$new_price = $old_price - ($old_price * $discount / 100);
?>

<div class="col-md-3 mb-4">

<div class="card h-100 shadow-sm position-relative">

<div class="promo-badge">
ลด <?= $discount ?>%
</div>

<a href="product_detail.php?id=<?= $p['p_id']; ?>"
   class="text-decoration-none text-dark">

<img src="<?= htmlspecialchars($img); ?>"
     class="card-img-top"
     style="height:250px;object-fit:cover;">

<div class="card-body">

<h6 class="fw-bold">
<?= htmlspecialchars($p['p_name']); ?>
</h6>

<div class="old-price">
฿<?= number_format($old_price,0); ?>
</div>

<div class="new-price">
฿<?= number_format($new_price,0); ?>
</div>

</div>
</a>

</div>
</div>

<?php endwhile; ?>
<?php else: ?>

<div class="col-12 text-center">
<h5 class="text-muted">ยังไม่มีสินค้าโปรโมชั่น</h5>
</div>

<?php endif; ?>

</div>
</div>