<?php
session_start();

include_once("connectdb.php");
include_once("functions.php");
include_once("bootstrap.php");

/* =========================
   เพิ่มตะกร้า / โปรด
========================= */
if(isset($_GET['add_to_cart'])){
    addToCart($_GET['add_to_cart']);
}

if(isset($_GET['add_to_fav'])){
    addToFavorite($_GET['add_to_fav']);
}

/* =========================
   รับค่าฟิลเตอร์
========================= */
$search   = $_GET['search'] ?? '';
$brand    = $_GET['brand'] ?? '';
$category = $_GET['category'] ?? '';
$gender   = $_GET['gender'] ?? '';
$sort     = $_GET['sort'] ?? '';

// [เพิ่ม] ตัวแปรเช็คว่ามีการกรองข้อมูลหรือไม่
$has_filter = !empty($search) || !empty($brand) || !empty($category) || !empty($gender) || !empty($sort);

$where = " WHERE 1=1 ";

/* ค้นหา */
if(!empty($search)){
    $safeSearch = mysqli_real_escape_string($conn,$search);
    $where .= " AND p.p_name LIKE '%$safeSearch%' ";
}

/* แบรนด์ */
if(!empty($brand)){
    $where .= " AND p.brand_id = ".intval($brand)." ";
}

/* หมวดหมู่ */
if(!empty($category)){
    $where .= " AND p.c_id = ".intval($category)." ";
}

/* เพศ */
if(!empty($gender)){
    $safeGender = mysqli_real_escape_string($conn,$gender);
    $where .= " AND p.p_type = '$safeGender' ";
}

/* เรียงราคา */
$order = " ORDER BY p.p_id DESC ";

if($sort == "low"){
    $order = " ORDER BY p.p_price ASC ";
}
if($sort == "high"){
    $order = " ORDER BY p.p_price DESC ";
}

/* =========================
   Query สินค้า
========================= */
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
FROM products p
LEFT JOIN brand b ON p.brand_id = b.brand_id
LEFT JOIN category c ON p.c_id = c.c_id
$where
$order
";

$rs = mysqli_query($conn,$sql);

/* =========================
   ดึงแบรนด์จริง
========================= */
$brandSQL = "SELECT * FROM brand ORDER BY brand_name ASC";
$brandRS  = mysqli_query($conn,$brandSQL);

/* =========================
   ดึงหมวดหมู่จริง
========================= */
$catSQL = "SELECT * FROM category ORDER BY c_name ASC";
$catRS  = mysqli_query($conn,$catSQL);

include("header.php");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สินค้าทั้งหมด | 2M3WM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{font-family:'Kanit',sans-serif;background:#f4f6f9;}
.page-header{background:#111;color:#fff;padding:40px;text-align:center;}
.filter-box{background:#fff;padding:20px;border-radius:12px;box-shadow:0 5px 15px rgba(0,0,0,.05);}
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
.btn-reset {
    background: #6c757d;
    color: #fff;
    border: none;
}
.btn-reset:hover {
    background: #5a6268;
    color: #fff;
}
</style>
</head>
<body>

<div class="page-header">
  <h2 class="fw-semibold">สินค้าทั้งหมด</h2>
  <p class="mb-0 text-light">เลือกสินค้าที่ใช่สำหรับคุณ</p>
</div>

<div class="container py-5">

<form method="GET">
<div class="filter-box mb-5">
  <div class="row g-3 align-items-center">

    <div class="col-lg-2">
      <input type="text" name="search"
        value="<?= htmlspecialchars($search); ?>"
        class="form-control"
        placeholder="ค้นหาสินค้า...">
    </div>

    <div class="col-lg-2">
      <select name="brand" class="form-select">
        <option value="">ทุกแบรนด์</option>
        <?php while($b=mysqli_fetch_assoc($brandRS)): ?>
          <option value="<?= $b['brand_id']; ?>"
          <?= ($brand==$b['brand_id'])?'selected':''; ?>>
            <?= $b['brand_name']; ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="col-lg-2">
      <select name="category" class="form-select">
        <option value="">ทุกหมวดหมู่</option>
        <?php while($c=mysqli_fetch_assoc($catRS)): ?>
          <option value="<?= $c['c_id']; ?>"
          <?= ($category==$c['c_id'])?'selected':''; ?>>
            <?= $c['c_name']; ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="col-lg-2">
      <select name="gender" class="form-select">
        <option value="">ทุกเพศ</option>
        <option value="male" <?= ($gender=="male")?'selected':''; ?>>ชาย</option>
        <option value="female" <?= ($gender=="female")?'selected':''; ?>>หญิง</option>
        <option value="unisex" <?= ($gender=="unisex")?'selected':''; ?>>ยูนิเซ็กส์</option>
      </select>
    </div>

    <div class="col-lg-2">
      <select name="sort" class="form-select">
        <option value="">เรียงราคา</option>
        <option value="low" <?= ($sort=="low")?'selected':''; ?>>ราคาน้อย → มาก</option>
        <option value="high" <?= ($sort=="high")?'selected':''; ?>>ราคามาก → น้อย</option>
      </select>
    </div>

    <div class="col-lg-2">
      <div class="d-flex gap-2">
          <button type="submit" class="btn btn-warning flex-fill">
            ค้นหา
          </button>
          
          <?php if($has_filter): ?>
          <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn btn-reset flex-fill" title="ล้างค่าค้นหา">
            <i class="bi bi-arrow-counterclockwise"></i>
          </a>
          <?php endif; ?>
      </div>
    </div>

  </div>
</div>
</form>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">

<?php if(mysqli_num_rows($rs) > 0): ?>
<?php while($p = mysqli_fetch_assoc($rs)): ?>

<?php
$img = !empty($p['main_img']) 
       ? $p['main_img'] 
       : 'images/no-image.png';
?>

<div class="col">
<div class="product-card">

<a href="product_detail.php?id=<?= $p['p_id']; ?>" 
   class="text-decoration-none text-dark">

<img src="<?= htmlspecialchars($img); ?>" 
     class="w-100 product-img">

<div class="product-body">

<span class="brand-tag">
<?= htmlspecialchars($p['brand_name'] ?? 'General'); ?>
</span>

<div class="product-title">
<?= htmlspecialchars($p['p_name']); ?>
</div>

<div class="text-muted small mb-1">
<?= ucfirst($p['p_type']); ?>
</div>

<div class="mb-2">
<?php if($p['p_qty'] > 0): ?>
<small class="text-success">
คงเหลือ <?= number_format($p['p_qty']); ?> คู่
</small>
<?php else: ?>
<small class="text-danger fw-bold">
สินค้าหมด
</small>
<?php endif; ?>
</div>

<div class="product-price">
฿<?= number_format($p['p_price'], 0); ?>
</div>

</div>
</a>

<div class="product-actions">

<?php if($p['p_qty'] > 0): ?>
<a href="?add_to_cart=<?= $p['p_id']; ?>" 
   class="btn btn-cart">
เพิ่มลงตะกร้า
</a>
<?php else: ?>
<button class="btn btn-secondary w-100 disabled">
สินค้าหมด
</button>
<?php endif; ?>

<a href="?add_to_fav=<?= $p['p_id']; ?>" 
   class="btn btn-fav">
<i class="bi bi-heart"></i>
</a>

</div>

</div>
</div>

<?php endwhile; ?>
<?php else: ?>

<div class="col-12 text-center py-5">
    <div class="text-muted mb-3">
        <i class="bi bi-search" style="font-size: 3rem;"></i>
    </div>
    <h4 class="text-muted">ไม่พบสินค้าที่คุณค้นหา</h4>
    <p class="text-secondary">ลองเปลี่ยนคำค้นหา หรือ รีเซ็ตตัวกรอง</p>
    <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn btn-outline-dark mt-2">ดูสินค้าทั้งหมด</a>
</div>

<?php endif; ?>

</div>

</div>

</body>
</html>