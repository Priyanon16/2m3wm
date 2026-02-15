<?php
session_start();
include "data.php";
include "functions.php";
include "header.php";

if(isset($_GET['add_to_cart'])){
    addToCart($_GET['add_to_cart']);
}

if(isset($_GET['add_to_fav'])){
    addToFavorite($_GET['add_to_fav']);
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สินค้าทั้งหมด | 2M3WM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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

    <?php foreach($products as $p){ ?>
    <div class="col-md-4 product-item"
      data-name="<?= strtolower($p['name']); ?>"
      data-brand="<?= $p['brand']; ?>"
      data-price="<?= $p['price']; ?>"
      data-category="<?= $p['category']; ?>">


      <a href="product_detail.php?id=<?= $p['id']; ?>" class="text-decoration-none text-dark">
        <div class="card h-100">
          <img src="<?= $p['img']; ?>" class="w-100">
          <div class="card-body">
            <span class="badge badge-brand mb-2"><?= $p['brand']; ?></span>
            <h6><?= $p['name']; ?></h6>
            <small class="text-muted"><?= $p['type']; ?></small>
            <p class="price mt-2">฿<?= number_format($p['price']); ?></p>

        <div class="d-flex align-items-center mt-3">
            <!-- กลุ่มปุ่มขวา -->
            <div class="d-flex gap-3 ms-auto">

                <a href="?add_to_cart=<?= $p['id']; ?>" 
                  class="btn btn-warning px-4">
                  เพิ่มลงตะกร้า
                </a>

                <a href="?add_to_fav=<?= $p['id']; ?>" 
                  class="btn btn-outline-danger px-3">
                  <i class="bi bi-heart"></i>
                </a>

            </div>

        </div>

          </div>
        </div>
      </a>

    </div>
    <?php } ?>

  </div>

</div>

<script>
const searchInput = document.getElementById("searchInput");
const brandFilter = document.getElementById("brandFilter");
const sortPrice = document.getElementById("sortPrice");
const products = document.querySelectorAll(".product-item");
const categoryFilter = document.getElementById("categoryFilter");

function filterProducts(){
  const keyword = searchInput.value.toLowerCase();
  const brand = brandFilter.value;
  const category = categoryFilter.value;

  products.forEach(item => {
    const name = item.dataset.name;
    const itemBrand = item.dataset.brand;
    const itemCategory = item.dataset.category;

    let show = true;

    if(keyword && !name.includes(keyword)) show = false;
    if(brand && itemBrand !== brand) show = false;
    if(category && itemCategory !== category) show = false;

    item.style.display = show ? "" : "none";
  });
}

function sortProducts(){
  const container = document.getElementById("productList");
  const items = Array.from(products);

  if(sortPrice.value === "low"){
    items.sort((a,b)=>a.dataset.price - b.dataset.price);
  }
  else if(sortPrice.value === "high"){
    items.sort((a,b)=>b.dataset.price - a.dataset.price);
  }

  items.forEach(item => container.appendChild(item));
}

searchInput.addEventListener("keyup", filterProducts);
brandFilter.addEventListener("change", filterProducts);
sortPrice.addEventListener("change", sortProducts);
categoryFilter.addEventListener("change", filterProducts);

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
