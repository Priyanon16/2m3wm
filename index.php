<?php
session_start();

include_once("connectdb.php");
include_once("functions.php");
include_once("bootstrap.php");

/* =========================================================
   [แก้ไข] ส่วนเพิ่มตะกร้า : เปลี่ยนเป็นบันทึกลง Database โดยตรง
   เพื่อให้เชื่อมโยงกับหน้า cart.php
========================================================= */
if(isset($_GET['add_to_cart'])){
    
    // 1. ตรวจสอบว่า Login หรือยัง
    if(!isset($_SESSION['user_id'])){
        echo "<script>alert('กรุณาล็อกอินก่อนซื้อสินค้า'); window.location='login.php';</script>";
        exit;
    }

    $pid = intval($_GET['add_to_cart']);
    $uid = intval($_SESSION['user_id']);

    // 2. ดึงข้อมูลสินค้าเพื่อเช็คสต็อก และ หาไซส์แรก (Default Size)
    // เพราะหน้า Index ไม่มีตัวเลือกไซส์ เราจะหยิบไซส์แรกสุดมาใส่ให้อัตโนมัติ
    $q_prod = mysqli_query($conn, "SELECT p_qty, p_size FROM products WHERE p_id = $pid");
    $prod   = mysqli_fetch_assoc($q_prod);

    if($prod['p_qty'] > 0){
        
        // แปลง string ไซส์ (เช่น "38,39,40") ให้เป็น array แล้วเอาตัวแรก
        $size_arr = explode(',', $prod['p_size']); 
        $default_size = isset($size_arr[0]) ? trim($size_arr[0]) : ''; 

        // 3. เช็คว่ามีสินค้านี้ (และไซส์นี้) ในตะกร้าหรือยัง
        $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$uid AND product_id=$pid AND size='$default_size'");

        if(mysqli_num_rows($check_cart) > 0){
            // มีแล้ว -> อัปเดตจำนวน +1 (แต่ต้องไม่เกินสต็อก)
            $cart_item = mysqli_fetch_assoc($check_cart);
            if($cart_item['quantity'] < $prod['p_qty']){
                mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id=$uid AND product_id=$pid AND size='$default_size'");
            }
        } else {
            // ยังไม่มี -> Insert ใหม่
            mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity, size) VALUES ($uid, $pid, 1, '$default_size')");
        }

        // แจ้งเตือนและไปหน้าตะกร้าทันที
        echo "<script>alert('เพิ่มสินค้าลงตะกร้าเรียบร้อย'); window.location='cart.php';</script>";
        
    } else {
        echo "<script>alert('สินค้าหมด'); window.location='index.php';</script>";
    }
    exit;
}

/* =========================
   [คงเดิม] ส่วน Favorite
========================= */
if(isset($_GET['add_to_fav'])){
    addToFavorite((int)$_GET['add_to_fav']);
}

/* =========================
   2. ดึงสินค้า + รูปหลัก
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
       ) AS main_img
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
                    <a href="#" class="btn btn-outline-light">
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
<?php if($row['p_qty'] > 0): ?>
<small class="text-success">
คงเหลือ <?= number_format($row['p_qty']); ?> คู่
</small>
<?php else: ?>
<small class="text-danger fw-bold">
สินค้าหมด
</small>
<?php endif; ?>
</div>

<div class="product-price">
฿<?= number_format($row['p_price'], 0); ?>
</div>

</div>
</a>

<div class="product-actions">

<?php if($row['p_qty'] > 0): ?>
<a href="?add_to_cart=<?= $row['p_id']; ?>" 
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

</body>
</html>