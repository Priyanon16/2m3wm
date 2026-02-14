<?php
session_start();
include "data.php";
include "header.php";

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

if(!isset($_SESSION['favorite'])){
    $_SESSION['favorite'] = [];
}

if(isset($_GET['add_to_cart'])){
    $id = $_GET['add_to_cart'];
    $_SESSION['cart'][] = $id;
    header("Location: index.php");
    exit;
}

if(isset($_GET['add_to_fav'])){
    $id = $_GET['add_to_fav'];
    $_SESSION['favorite'][] = $id;
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>2M3WM Sneaker</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Kanit -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
  font-family:'Kanit',sans-serif;
  background:#f8f9fa;
}

/* HEADER */
header{
  background:#111;
}
.header-icons a{
  color:#fff;
  font-size:1.2rem;
  transition:.2s;
}
.header-icons a:hover{
  color:#ffc107;
}

#bannerSlider{
  max-height: 480px;
  overflow: hidden;
  margin-bottom: 40px;
}
.carousel-item img{
  height: 480px;
  object-fit: cover;
}



/* CARD */
.card{
  border:none;
  transition:.3s;
}
.card:hover{
  transform:translateY(-6px);
  box-shadow:0 10px 25px rgba(0,0,0,.15);
}
.card-img-top{
  height:400px;
  object-fit:cover;
}
.price{
  color:#ff7a00;
  font-weight:600;
}

/* SEARCH BAR */
.search-box{
  position: relative;
  width: 260px;
}

.search-box input{
  border-radius: 50px;
  padding-left: 42px;
  background:#222;
  border:none;
  color:#fff;
}

.search-box input::placeholder{
  color:#aaa;
}

.search-box i{
  position:absolute;
  top:50%;
  left:15px;
  transform:translateY(-50%);
  color:#aaa;
}

/* HEADER SPACING */
header .container{
  gap:20px;
}

/* CARD BODY */
.card-body{
  text-align:left;
}

.album{
  margin-top: 20px;
}

/* HERO TEXT ON SLIDER */
.hero-caption{
  position:absolute;
  top:50%;
  left:50%;
  transform:translate(-50%,-50%);
  color:#fff;
  text-align:center;
  background:rgba(0,0,0,.45);
  padding:30px 40px;
  border-radius:16px;
}

.hero-caption h1{
  font-weight:600;
}

.hero-caption p{
  color:#ddd;
}

/* BRAND SECTION */
.brand-section{
  background:#fff;
}

.brand-logos img{
  height:40px;           /* ขนาดโลโก้ */
  filter: grayscale(100%);
  opacity:0.8;
  transition:0.3s;
}

.brand-logos img:hover{
  filter: grayscale(0%);
  opacity:1;
  transform:scale(1.05);
}


</style>
</head>

<body>

<!-- HEADER -->
<header class="p-3">
  <div class="container d-flex align-items-center justify-content-between">
    <h4 class="text-white mb-0">2M3WM</h4>

    <ul class="nav col-12 col-lg-auto ms-lg-auto mb-2 justify-content-center mb-md-0 gap-3">
          <li><a href="#" class="nav-link px-2 text-white">สินค้าใหม่</a></li>
          <li><a href="#" class="nav-link px-2 text-white">ชาย</a></li>
          <li><a href="#" class="nav-link px-2 text-white">หญิง</a></li>
        </ul>

        <div class="search-box ms-lg-3">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput"
                class="form-control"
                placeholder="ค้นหาสินค้า...">
        </div>


    <div class="d-flex gap-3 header-icons">
      <a href="login.php"><i class="bi bi-person"></i></a>
      <a href="#"><i class="bi bi-heart"></i></a>
      <a href="#" class="position-relative">
        <i class="bi bi-bag"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">2</span>
      </a>
    </div>
  </div>
</header>

<!-- SLIDER -->
<div id="bannerSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">

  <div class="carousel-inner">

    <div class="carousel-item active position-relative">
      <img src="images/1.jpg" class="w-100">

      <div class="hero-caption">
        <h1>2M3WM Sneaker</h1>
        <p>รองเท้าของแท้ สำหรับทุกสไตล์</p>
        <div class="d-flex justify-content-center gap-3 mt-3">
          <a href="#" class="btn btn-warning">สินค้าใหม่</a>
          <a href="all_products.php" class="btn btn-outline-light">ดูทั้งหมด</a>
        </div>
      </div>
    </div>

    <div class="carousel-item">
      <img src="images/2.jpg" class="w-100">
    </div>

    <div class="carousel-item">
      <img src="images/1.jpg" class="w-100">
    </div>

  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#bannerSlider" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>

  <button class="carousel-control-next" type="button" data-bs-target="#bannerSlider" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>

</div>


<!-- BRAND SECTION -->
<section class="brand-section py-5">
  <div class="container text-center">
    <h5 class="mb-4 fw-semibold">แบรนด์แฟชั่นที่คุณชื่นชอบ</h5>

    <div class="d-flex justify-content-center align-items-center flex-wrap gap-5 brand-logos">
      <img src="images/brands/newbalance.jpg" alt="New Balance">
      <img src="images/brands/on.jpg" alt="On">
      <img src="images/brands/nike.jpg" alt="Nike">
      <img src="images/brands/puma.jpg" alt="Puma">
      <img src="images/brands/adidas.jpg" alt="Adidas">
      <img src="images/brands/jordan.jpg alt="Jordan">
    </div>

    <div class="mt-4">
      <a href="#" class="text-dark text-decoration-underline">ดูเพิ่มเติม</a>
    </div>
  </div>
</section>



<!-- ALBUM -->
<div class="album pb-5">
  <div class="container">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4" id="productList">

      <?php foreach($products as $p){ ?>
      <div class="col product-item">
        <a href="product_detail.php?id=<?= $p['id']; ?>" class="text-decoration-none text-dark">
            <div class="card h-100">
            <img src="<?= $p['img']; ?>" class="card-img-top">
            <div class="card-body">
                <h6 class="product-name"><?= $p['name']; ?></h6>
                <small class="text-muted product-type"><?= $p['type']; ?></small>
                <p class="price mt-2">฿<?= number_format($p['price']); ?></p>
            </div>
            </div>
        </a>
        </div>
        <?php } ?>


    </div>
  </div>
</div>



<!-- SEARCH SCRIPT -->
<script>
document.getElementById("searchInput").addEventListener("keyup", function(){
  let keyword = this.value.toLowerCase();
  let items = document.querySelectorAll(".product-item");

  items.forEach(item => {
    let name = item.querySelector(".product-name").innerText.toLowerCase();
    let type = item.querySelector(".product-type").innerText.toLowerCase();

    item.style.display = (name.includes(keyword) || type.includes(keyword)) ? "" : "none";
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
