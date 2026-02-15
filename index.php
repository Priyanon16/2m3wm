<?php
session_start();

include_once("connectdb.php");
// ฟังก์ชันจัดการ Cart และ Favorite ที่คุณทำไว้ในไฟล์รวมหรือ functions.php
include_once("functions.php"); 
include_once("bootstrap.php");

/* =========================
   1. ส่วนประมวลผล Logic (GET)
   ตรวจสอบการกดปุ่มจากหน้าเว็บ
========================= */
if(isset($_GET['add_to_cart'])){
    addToCart((int)$_GET['add_to_cart']);
}

if(isset($_GET['add_to_fav'])){
    addToFavorite((int)$_GET['add_to_fav']);
}

/* =========================
   2. การดึงข้อมูลสินค้าจากฐานข้อมูล
   เชื่อมตาราง category เพื่อดึงชื่อแบรนด์/หมวดหมู่
========================= */
$sql = "SELECT p.*, c.c_name 
        FROM products p 
        LEFT JOIN category c ON p.c_id = c.c_id 
        ORDER BY p.p_id DESC";
$rs = mysqli_query($conn, $sql);

include("header.php");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>2M3WM Sneaker - หน้าแรก</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f8f9fa; }
        
        /* สไตล์การ์ดสินค้า */
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
        .product-img { height: 350px; object-fit: cover; }
        .product-body { padding: 20px; }
        
        /* องค์ประกอบในการ์ด */
        .brand-tag { 
            display: inline-block; 
            background: #000; 
            color: #fff; 
            font-size: 12px; 
            padding: 4px 12px; 
            border-radius: 20px; 
            margin-bottom: 10px; 
        }
        .product-title { font-weight: 600; font-size: 18px; margin-bottom: 5px; color: #333; }
        .product-price { color: #ff7a00; font-weight: 700; font-size: 20px; }
        
        /* ปุ่มกด */
        .btn-cart { background: #ffc107; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; flex-grow: 1; text-decoration: none; color: #000; text-align: center; }
        .btn-fav { border: 1px solid #ff6b6b; background: #fff; width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ff6b6b; transition: .2s; text-decoration: none; }
        .btn-fav:hover { background: #ff6b6b; color: #fff; }
        .product-actions { display: flex; gap: 10px; padding: 0 20px 20px 20px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <?php if(mysqli_num_rows($rs) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($rs)): 
                // จัดการรูปภาพ: ดึงรูปแรกมาแสดงผลกรณีมีหลายรูป
                $img_arr = explode(',', $row['p_img']);
                $first_img = trim($img_arr[0]);
            ?>
            <div class="col">
                <div class="product-card shadow-sm">
                    <a href="product_detail.php?id=<?= $row['p_id']; ?>" class="text-decoration-none">
                        <img src="<?= htmlspecialchars($first_img); ?>" class="w-100 product-img" alt="<?= $row['p_name']; ?>">
                        
                        <div class="product-body">
                            <span class="brand-tag"><?= htmlspecialchars($row['c_name'] ?? 'General'); ?></span>
                            
                            <div class="product-title"><?= htmlspecialchars($row['p_name']); ?></div>
                            
                            <div class="text-muted small mb-2">
                                Category: <?= ($row['p_type'] == 'male') ? 'Men' : 'Women'; ?>
                            </div>
                            
                            <div class="product-price">
                                ฿<?= number_format($row['p_price'], 0); ?>
                            </div>
                        </div>
                    </a>

                    <div class="product-actions">
                        <?php if($row['p_qty'] > 0): ?>
                            <a href="?add_to_cart=<?= $row['p_id']; ?>" class="btn btn-cart">เพิ่มลงตะกร้า</a>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100 disabled" style="border-radius:12px;">สินค้าหมด</button>
                        <?php endif; ?>
                        
                        <a href="?add_to_fav=<?= $row['p_id']; ?>" class="btn-fav" title="ถูกใจ">
                            <i class="bi bi-heart"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">ยังไม่มีสินค้าในระบบ</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
แสดงข้อมูลสินค้าเหลือด้วย