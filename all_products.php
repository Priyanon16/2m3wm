<?php
session_start();

include_once("connectdb.php");
include_once("functions.php");
include_once("bootstrap.php");

/* =========================
   กดเพิ่มตะกร้า / โปรด
========================= */
if(isset($_GET['add_to_cart'])){
    addToCart($_GET['add_to_cart']);
}

if(isset($_GET['add_to_fav'])){
    addToFavorite($_GET['add_to_fav']);
}

/* =========================
   ดึงสินค้าจากฐานข้อมูลจริง
========================= */
$sql = "SELECT * FROM products ORDER BY p_id DESC";
$rs  = mysqli_query($conn,$sql);

include("header.php");
?>



<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สินค้าทั้งหมด | 2M3WM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body{
  font-family:'Kanit',sans-serif;
  background:#f4f6f9;
}

/* HEADER */
.page-header{
  background:#111;
  color:#fff;
  padding:40px 0;
  text-align:center;
}

/* FILTER BAR */
.filter-box{
  background:#fff;
  padding:20px;
  border-radius:12px;
  box-shadow:0 5px 15px rgba(0,0,0,.05);
}

/* CARD */
.card{
  border:none;
  border-radius:14px;
  overflow:hidden;
  transition:.3s;
}
.card:hover{
  transform:translateY(-6px);
  box-shadow:0 12px 30px rgba(0,0,0,.12);
}
.card img{
  height:280px;
  object-fit:cover;
}
.price{
  color:#ff7a00;
  font-weight:600;
}
.badge-brand{
  background:#000;
}

/* TOP TABS */
.top-tabs .nav-link{
  position: relative;
  transition: .3s;
}

.top-tabs .nav-link:hover{
  color: #ff7a00;
}

.active-tab{
  color: #ff7a00 !important;
}

.active-tab::after{
  content: "";
  position: absolute;
  bottom: -6px;
  left: 0;
  width: 100%;
  height: 3px;
  background: #ff7a00;
  border-radius: 5px;
}

</style>
</head>
<body>

<!-- PAGE HEADER -->
<div class="page-header">
  <h2 class="fw-semibold">สินค้าทั้งหมด</h2>
  <p class="mb-0 text-light">เลือกสินค้าที่ใช่สำหรับคุณ</p>
</div>

<div class="container py-5">

  <!-- FILTER SECTION -->
 <div class="filter-box mb-5 p-4">

  <div class="row g-3 align-items-center">

    <!-- SEARCH -->
    <div class="col-lg-4">
      <div class="input-group filter-input">
        <span class="input-group-text bg-white border-0">
          <i class="bi bi-search"></i>
        </span>
        <input type="text" id="searchInput" class="form-control border-0"
          placeholder="ค้นหาสินค้า...">
      </div>
    </div>

    <!-- BRAND -->
    <div class="col-lg-3">
      <div class="input-group filter-input">
        <span class="input-group-text bg-white border-0">
          <i class="bi bi-tag"></i>
        </span>
        <select id="brandFilter" class="form-select border-0">
          <option value="">ทุกแบรนด์</option>
          <option value="Nike">Nike</option>
          <option value="Adidas">Adidas</option>
          <option value="Puma">Puma</option>
        </select>
      </div>
    </div>

    <!-- CATEGORY -->
    <div class="col-lg-3">
      <div class="input-group filter-input">
        <span class="input-group-text bg-white border-0">
          <i class="bi bi-grid"></i>
        </span>
        <select id="categoryFilter" class="form-select border-0">
          <option value="">ทุกหมวดหมู่</option>
          <option value="new">สินค้าใหม่</option>
          <option value="recommended">สินค้าแนะนำ</option>
          <option value="female">หญิง</option>
          <option value="male">ชาย</option>
        </select>
      </div>
    </div>

    <!-- SORT -->
    <div class="col-lg-2">
      <div class="input-group filter-input">
        <span class="input-group-text bg-white border-0">
          <i class="bi bi-arrow-down-up"></i>
        </span>
        <select id="sortPrice" class="form-select border-0">
          <option value="">เรียงตามราคา</option>
          <option value="low">ราคาน้อย → มาก</option>
          <option value="high">ราคามาก → น้อย</option>
        </select>
      </div>
    </div>
  </div>
</div>

  <!-- PRODUCT GRID -->
  <div class="row g-4" id="productList">

<?php while($p = mysqli_fetch_assoc($rs)): ?>

<div class="col-md-4 product-item"
  data-name="<?= strtolower($p['p_name']); ?>"
  data-brand="<?= $p['p_type']; ?>"
  data-price="<?= $p['p_price']; ?>"
  data-category="all">

  <div class="card h-100">

    <a href="product_detail.php?id=<?= $p['p_id']; ?>" 
       class="text-decoration-none text-dark">

      <img src="<?= htmlspecialchars($p['p_img']); ?>" class="w-100">

      <div class="card-body">
        <span class="badge badge-brand mb-2">
          <?= htmlspecialchars($p['p_type']); ?>
        </span>

        <h6><?= htmlspecialchars($p['p_name']); ?></h6>

        <p class="price mt-2">
          ฿<?= number_format($p['p_price'],0); ?>
        </p>
      </div>
    </a>

    <!-- ปุ่ม -->
    <div class="d-flex gap-3 p-3 pt-0">

      <a href="?add_to_cart=<?= $p['p_id']; ?>" 
         class="btn btn-warning w-100">
         เพิ่มลงตะกร้า
      </a>

      <a href="?add_to_fav=<?= $p['p_id']; ?>" 
         class="btn btn-outline-danger">
         <i class="bi bi-heart"></i>
      </a>

    </div>

  </div>
</div>

<?php endwhile; ?>

</div>


</div>


</body>
</html>
