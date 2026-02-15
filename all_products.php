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
        WHERE p_id = p.p_id 
        LIMIT 1
    ) AS first_image
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
.card{border:none;border-radius:14px;overflow:hidden;transition:.3s;}
.card:hover{transform:translateY(-6px);box-shadow:0 12px 30px rgba(0,0,0,.12);}
.card img{height:280px;object-fit:cover;}
.price{color:#ff7a00;font-weight:600;}
.badge-brand{background:#000;}
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

    <!-- SEARCH -->
    <div class="col-lg-3">
      <input type="text" name="search"
        value="<?= htmlspecialchars($search); ?>"
        class="form-control"
        placeholder="ค้นหาสินค้า...">
    </div>

    <!-- BRAND -->
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

    <!-- CATEGORY -->
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

    <!-- GENDER -->
    <div class="col-lg-2">
      <select name="gender" class="form-select">
        <option value="">ทุกเพศ</option>
        <option value="male" <?= ($gender=="male")?'selected':''; ?>>ชาย</option>
        <option value="female" <?= ($gender=="female")?'selected':''; ?>>หญิง</option>
        <option value="unisex" <?= ($gender=="unisex")?'selected':''; ?>>ยูนิเซ็กส์</option>
      </select>
    </div>

    <!-- SORT -->
    <div class="col-lg-2">
      <select name="sort" class="form-select">
        <option value="">เรียงราคา</option>
        <option value="low" <?= ($sort=="low")?'selected':''; ?>>ราคาน้อย → มาก</option>
        <option value="high" <?= ($sort=="high")?'selected':''; ?>>ราคามาก → น้อย</option>
      </select>
    </div>

    <div class="col-lg-1">
      <button type="submit" class="btn btn-warning w-100">
        ค้นหา
      </button>
    </div>

  </div>
</div>
</form>

<!-- PRODUCT GRID -->
<div class="row g-4">

<?php if(mysqli_num_rows($rs)>0): ?>
<?php while($p=mysqli_fetch_assoc($rs)): ?>

<div class="col-md-4">
  <div class="card h-100">

    <a href="product_detail.php?id=<?= $p['p_id']; ?>"
       class="text-decoration-none text-dark">

      <?php
      $image = !empty($p['first_image'])
          ? $p['first_image']
          : 'images/no-image.png'; // fallback ถ้าไม่มีรูป
      ?>

      <img src="<?= htmlspecialchars($image); ?>" class="w-100">


      <div class="card-body">
        <span class="badge badge-brand mb-2">
          <?= htmlspecialchars($p['brand_name']); ?>
        </span>

        <h6><?= htmlspecialchars($p['p_name']); ?></h6>

        <small class="text-muted">
          <?= htmlspecialchars($p['c_name']); ?> |
          <?= ucfirst($p['p_type']); ?>
        </small>

        <p class="price mt-2">
          ฿<?= number_format($p['p_price'],0); ?>
        </p>
      </div>
    </a>

    <div class="d-flex justify-content-end gap-2 p-3 pt-0">
      <a href="?add_to_cart=<?= $p['p_id']; ?>" 
         class="btn btn-warning px-4">
         เพิ่มลงตะกร้า
      </a>
      <a href="?add_to_fav=<?= $p['p_id']; ?>" 
         class="btn btn-outline-danger px-3">
         <i class="bi bi-heart"></i>
      </a>
    </div>

  </div>
</div>

<?php endwhile; ?>
<?php else: ?>
<div class="col-12 text-center">
  <h5 class="text-muted">ไม่พบสินค้า</h5>
</div>
<?php endif; ?>

</div>
</div>

</body>
</html>
