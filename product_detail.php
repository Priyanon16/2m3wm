<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

$id = intval($_GET['id'] ?? 0);

if($id <= 0){
    echo "ไม่พบสินค้า";
    exit;
}

/* =========================
   ดึงข้อมูลสินค้า + แบรนด์ + หมวดหมู่
========================= */
$sql = "
SELECT 
    p.*, 
    b.brand_name, 
    c.c_name
FROM products p
LEFT JOIN brand b ON p.brand_id = b.brand_id
LEFT JOIN category c ON p.c_id = c.c_id
WHERE p.p_id = $id
LIMIT 1
";

$rs  = mysqli_query($conn,$sql);
$product = mysqli_fetch_assoc($rs);

if(!$product){
    echo "ไม่พบสินค้า";
    exit;
}

/* =========================
   ดึงรูปทั้งหมดของสินค้า
========================= */
$img_rs = mysqli_query($conn,"
SELECT img_path 
FROM product_images 
WHERE p_id = $id
");

$images = [];
while($img = mysqli_fetch_assoc($img_rs)){
    $images[] = $img['img_path'];
}

include("header.php");
?>

<div class="container mt-5 mb-5">
<div class="row">

<!-- =========================
     รูปสินค้า (หลายรูป)
========================= -->
<div class="col-md-6">

<?php if(count($images) > 0): ?>

<div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">

        <?php foreach($images as $key => $img): ?>
        <div class="carousel-item <?= $key == 0 ? 'active' : '' ?>">
            <img src="<?= htmlspecialchars($img); ?>" 
                 class="d-block w-100 rounded shadow"
                 style="height:450px;object-fit:cover;">
        </div>
        <?php endforeach; ?>

    </div>

    <?php if(count($images) > 1): ?>
    <button class="carousel-control-prev" type="button" 
            data-bs-target="#productCarousel" 
            data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" 
            data-bs-target="#productCarousel" 
            data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
    <?php endif; ?>
</div>

<?php else: ?>
<img src="images/no-image.png" 
     class="img-fluid rounded shadow">
<?php endif; ?>

</div>


<!-- =========================
     รายละเอียดสินค้า
========================= -->
<div class="col-md-6">

<h3 class="fw-bold">
<?= htmlspecialchars($product['p_name']) ?>
</h3>

<p class="text-muted">
แบรนด์: <?= htmlspecialchars($product['brand_name'] ?? '-') ?>
<br>
หมวดหมู่: <?= htmlspecialchars($product['c_name'] ?? '-') ?>
<br>
ประเภท: <?= htmlspecialchars($product['p_type']) ?>
</p>

<h4 class="text-warning mb-3">
฿<?= number_format($product['p_price'],2) ?>
</h4>

<p>
ไซซ์: <?= htmlspecialchars($product['p_size']) ?>
</p>

<hr>

<p>
<?= nl2br(htmlspecialchars($product['p_detail'])) ?>
</p>

<div class="mt-4 d-flex gap-3">
    <a href="cart.php?add=<?= $product['p_id'] ?>" 
       class="btn btn-warning">
       เพิ่มลงตะกร้า
    </a>

    <a href="favorite.php?add=<?= $product['p_id'] ?>" 
       class="btn btn-outline-danger">
       เพิ่มรายการโปรด
    </a>
</div>

</div>
</div>
</div>
