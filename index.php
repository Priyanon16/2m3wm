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
$rs  = mysqli_query($conn,$sql);

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
  height:40px;           /* ขนาดโลโก้ */
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
$rs  = mysqli_query($conn,$sql);

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
เชื่อมสินค้าหน้า index ให้เชื่อมกับหน้า admin_product.php 
<?php
// admin_product.php
session_start();

// 1. เชื่อมต่อฐานข้อมูล
include_once("check_login.php"); 
include_once("connectdb.php");

if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }
mysqli_set_charset($conn, "utf8");

// 2. ส่วนคำสั่งลบสินค้า
if(isset($_GET['delete_id'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    // ดึง path รูปมาเช็คก่อนลบ
    $sql_img = "SELECT p_img FROM products WHERE p_id = '$id'";
    $res_img = mysqli_query($conn, $sql_img);
    $row_img = mysqli_fetch_assoc($res_img);
    
    // ลบข้อมูลใน DB
    if(mysqli_query($conn, "DELETE FROM products WHERE p_id = '$id'")){
        
        // --- ส่วนแก้ไข: ลบไฟล์รูปภาพทั้งหมด ---
        if(!empty($row_img['p_img'])){
            $files = explode(',', $row_img['p_img']); 
            foreach ($files as $file) {
                $file = trim($file);
                if(!empty($file) && file_exists($file)){
                    unlink($file); 
                }
            }
        }

        echo "<script>alert('ลบสินค้าเรียบร้อย'); window.location='admin_product.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// 3. ดึงข้อมูล
$sql = "SELECT p.*, c.c_name FROM products p LEFT JOIN category c ON p.c_id = c.c_id ORDER BY p.p_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสินค้า - 2M3WM Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --theme-orange: #ff5722;
            --theme-orange-hover: #e64a19;
            --theme-dark: #1a1a1a;
            --theme-bg: linear-gradient(135deg, #f8f9fa, #eef1f4);
        }

        body {
            font-family: 'Kanit', sans-serif;
            background: var(--theme-bg);
            min-height: 100vh;
        }

        header {
            background: linear-gradient(90deg, #111, var(--theme-dark));
            padding: 1rem 0;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            margin-bottom: 2rem;
        }
        
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 2px;
            color: #fff !important;
        }

        .btn-theme {
            background: var(--theme-orange);
            color: white !important;
            border: none;
            border-radius: 50px;
            padding: 8px 22px;
            transition: .3s;
        }

        .btn-theme:hover {
            background: var(--theme-orange-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 87, 34, 0.3);
        }

        .content-card {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-top: 5px solid var(--theme-orange);
            margin-bottom: 2rem;
        }

        .card-title-custom {
            color: var(--theme-dark);
            font-weight: 700;
        }

        .table img.img-thumb {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .table thead th {
            background-color: #fff;
            color: #555;
            font-weight: 600;
            border-bottom: 2px solid #eee;
        }

        .price-tag {
            color: var(--theme-orange);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .badge-size {
            background: #f1f1f1;
            color: #333;
            border: 1px solid #ddd;
            margin: 2px;
        }

        /* ตกแต่งตัวเลขจำนวนสินค้า */
        .stock-count {
            font-weight: 600;
            font-size: 1.1rem;
        }
        .out-of-stock {
            color: #dc3545;
            text-decoration: line-through;
        }
    </style>
</head>
<body>

    <header>
        <div class="container d-flex align-items-center justify-content-between">
            <a class="navbar-brand" href="index_admin.php">
                <i class="bi bi-shield-check me-2"></i>2M3WM ADMIN
            </a>
            <div class="d-flex align-items-center gap-4">
                <a href="logout.php" class="btn btn-theme text-decoration-none">
                    <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="index_admin.php" class="text-secondary text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
            </a>
            <a href="admin_add.php" class="btn btn-theme">
                <i class="bi bi-plus-lg me-1"></i> เพิ่มสินค้าใหม่
            </a>
        </div>

        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <h3 class="card-title-custom">
                    <i class="bi bi-box-seam me-2 text-warning"></i>รายการสินค้าในระบบ
                </h3>
                <span class="badge bg-secondary rounded-pill">ทั้งหมด <?= mysqli_num_rows($result); ?> รายการ</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">ID</th>
                            <th width="10%">รูปภาพ</th>
                            <th width="20%">ชื่อสินค้า</th>
                            <th width="20%">หมวดหมู่/สเปค</th>
                            <th class="text-center" width="10%">สต็อก (คู่)</th>
                            <th class="text-end" width="15%">ราคา</th>
                            <th class="text-center" width="20%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)){ 
                                $img_src = "";
                                if(!empty($row['p_img'])){
                                    $img_arr = explode(',', $row['p_img']); 
                                    $img_src = $img_arr[0]; 
                                }
                        ?>
                        <tr>
                            <td class="text-center text-muted fw-bold">#<?= $row['p_id']; ?></td>
                            
                            <td>
                                <?php if(!empty($img_src) && file_exists($img_src)): ?> <img src="<?= $img_src; ?>" class="img-thumb">
                                <?php elseif(!empty($img_src)): ?> <img src="<?= $img_src; ?>" class="img-thumb">
                                <?php else: ?>
                                    <div class="img-thumb bg-light d-flex align-items-center justify-content-center text-muted small border rounded-3">No Pic</div>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <h6 class="fw-bold mb-1 text-dark"><?= $row['p_name']; ?></h6>
                                <small class="text-muted text-truncate d-block" style="max-width: 180px;">
                                    <?= $row['p_detail']; ?>
                                </small>
                            </td>

                            <td>
                                <div class="mb-1">
                                    <span class="badge bg-dark fw-light"><?= $row['c_name'] ?? 'ไม่มีหมวด'; ?></span>
                                    <?php 
                                        $badge_cls = ($row['p_type'] == 'male') ? 'bg-primary' : (($row['p_type'] == 'female') ? 'bg-danger' : 'bg-success');
                                        $type_txt = ($row['p_type'] == 'male') ? 'Men' : (($row['p_type'] == 'female') ? 'Women' : 'Unisex');
                                    ?>
                                    <span class="badge <?= $badge_cls; ?> bg-opacity-75"><?= $type_txt; ?></span>
                                </div>
                                <div class="d-flex flex-wrap" style="max-width: 180px;">
                                    <?php 
                                    if(!empty($row['p_size'])) {
                                        $sizes = explode(',', $row['p_size']);
                                        foreach($sizes as $s) { echo '<span class="badge badge-size rounded-pill">'.$s.'</span>'; }
                                    }
                                    ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <?php if($row['p_qty'] > 0): ?>
                                    <span class="stock-count text-dark"><?= number_format($row['p_qty']); ?></span>
                                <?php else: ?>
                                    <span class="stock-count out-of-stock">0</span>
                                    <div class="text-danger" style="font-size: 0.7rem; font-weight: bold;">OUT OF STOCK</div>
                                <?php endif; ?>
                            </td>

                            <td class="text-end">
                                <span class="price-tag">฿<?= number_format($row['p_price']); ?></span>
                            </td>
                            
                            <td class="text-center">
                                <div class="btn-group shadow-sm" role="group">
                                    <a href="admin_edit.php?id=<?= $row['p_id']; ?>" class="btn btn-outline-secondary btn-sm" title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?delete_id=<?= $row['p_id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('ยืนยันที่จะลบสินค้านี้?');" title="ลบ">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                        ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <h5 class="fw-light text-secondary">ยังไม่มีสินค้าในระบบ</h5>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 