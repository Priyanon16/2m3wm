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
<title>2M3WM Sneaker</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body{
  font-family:'Kanit',sans-serif;
  background:#f8f9fa;
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

.product-card{
  border:none;
  border-radius:20px;
  overflow:hidden;
  background:#f1f1f1;
  transition:.3s;
}

.product-card:hover{
  transform:translateY(-6px);
  box-shadow:0 15px 35px rgba(0,0,0,.15);
}

.product-img{
  height:380px;
  object-fit:cover;
  border-top-left-radius:20px;
  border-top-right-radius:20px;
}

.product-body{
  padding:20px;
}

.brand-tag{
  display:inline-block;
  background:#000;
  color:#fff;
  font-size:12px;
  padding:4px 10px;
  border-radius:20px;
  margin-bottom:10px;
}

.product-title{
  font-weight:600;
  font-size:18px;
  margin-bottom:5px;
}

.product-type{
  color:#888;
  font-size:14px;
}

.product-price{
  color:#ff7a00;
  font-weight:700;
  font-size:18px;
}

.btn-cart{
  background:#ffc107;
  border:none;
  padding:10px 25px;
  border-radius:12px;
  font-weight:600;
}

.btn-fav{
  border:1px solid #ff6b6b;
  background:#fff;
  width:45px;
  height:45px;
  border-radius:12px;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#ff6b6b;
}

.product-actions{
  display:flex;
  justify-content:space-between; /* ดันซ้าย-ขวา */
  align-items:center;
  gap:10px;
  margin-top:15px;
}


</style>
</head>

<body>

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
      <img src="images/brands/jordan.jpg" alt="Jordan">
    </div>

    <div class="mt-4">
      <a href="#" class="text-dark text-decoration-underline">ดูเพิ่มเติม</a>
    </div>
  </div>
</section>



<!-- ALBUM -->
<div class="album pb-5">
  <div class="container">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">

<?php
$sql = "SELECT * FROM products ORDER BY p_id DESC";
$rs  = mysqli_query($conn,$sql);

if(mysqli_num_rows($rs) > 0):
while($row = mysqli_fetch_assoc($rs)):
?>

<div class="col">
  <div class="product-card">

    <a href="product_detail.php?id=<?= $row['p_id']; ?>" 
       class="text-decoration-none text-dark">

      <img src="<?= htmlspecialchars($row['p_img']); ?>" 
           class="w-100 product-img">

      <div class="product-body">

        <span class="brand-tag">
          <?= htmlspecialchars($row['p_type']); ?>
        </span>

        <div class="product-title">
          <?= htmlspecialchars($row['p_name']); ?>
        </div>

        <div class="product-type">
          รองเท้า<?= htmlspecialchars($row['p_type']); ?>
        </div>

        <div class="product-price mt-2">
          ฿<?= number_format($row['p_price'],0); ?>
        </div>

      </div>
    </a>

   <div class="product-actions px-3 pb-3">

      <a href="?add_to_cart=<?= $row['p_id']; ?>" 
        class="btn btn-cart">
        เพิ่มลงตะกร้า
      </a>

      <a href="?add_to_fav=<?= $row['p_id']; ?>" 
        class="btn-fav">
        <i class="bi bi-heart"></i>
      </a>

    </div>



  </div>
</div>


<?php
endwhile;
else:
echo '<div class="text-center">ยังไม่มีสินค้าในระบบ</div>';
endif;
?>

    </div>
  </div>
</div>

</body>
</html>
