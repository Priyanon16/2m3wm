<?php
session_start();
include_once("check_login.php"); // แนะนำให้เพิ่มไฟล์นี้เพื่อความปลอดภัย
include_once("connectdb.php");

/* ===========================================
   ดึงข้อมูลสถิติ (Real-time Statistics)
=========================================== */

// 1. นับจำนวนออเดอร์ทั้งหมด
$sql_orders = "SELECT COUNT(*) as count FROM orders";
$rs_orders = mysqli_query($conn, $sql_orders);
$row_orders = mysqli_fetch_assoc($rs_orders);
$count_orders = $row_orders['count'];

// 2. นับจำนวนสินค้าทั้งหมด
$sql_products = "SELECT COUNT(*) as count FROM products";
$rs_products = mysqli_query($conn, $sql_products);
$row_products = mysqli_fetch_assoc($rs_products);
$count_products = $row_products['count'];

// 3. นับจำนวนสมาชิกทั้งหมด
$sql_users = "SELECT COUNT(*) as count FROM users";
$rs_users = mysqli_query($conn, $sql_users);
$row_users = mysqli_fetch_assoc($rs_users);
$count_users = $row_users['count'];

// 4. (Optional) ยอดขายรวม (ถ้ามีตาราง orders ที่มีราคารวม)
// $sql_sales = "SELECT SUM(total_price) as total FROM orders WHERE status = 'completed'"; 
// ...
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2M3WM Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: #f4f6f9;
            color: #333;
        }

        /* Layout Structure (เหมือน admin_product.php) */
        .layout {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #222, #333);
            color: #fff;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .welcome-banner::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 87, 34, 0.2);
            border-radius: 50%;
        }
        .admin-highlight {
            color: #ff5722;
            font-weight: 700;
        }

        /* Stat Cards */
        .stat-card {
            background: #fff;
            border: none;
            border-radius: 15px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        .bg-icon-orange { background: rgba(255, 87, 34, 0.1); color: #ff5722; }
        .bg-icon-blue { background: rgba(13, 110, 253, 0.1); color: #0d6efd; }
        .bg-icon-green { background: rgba(25, 135, 84, 0.1); color: #198754; }
        
        .stat-info h3 { margin: 0; font-weight: 700; font-size: 2rem; }
        .stat-info p { margin: 0; color: #888; font-size: 0.9rem; }

        /* Menu Grid Cards */
        .menu-grid-card {
            background: #fff;
            border: none;
            border-radius: 15px;
            text-align: center;
            padding: 30px 20px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-decoration: none;
            color: #333;
            display: block;
            height: 100%;
        }
        .menu-grid-card:hover {
            background: #ff5722;
            color: #fff;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(255, 87, 34, 0.3);
        }
        .menu-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #ff5722;
            transition: 0.3s;
        }
        .menu-grid-card:hover .menu-icon {
            color: #fff;
            transform: scale(1.1);
        }
        .menu-title { font-weight: 600; font-size: 1.1rem; margin-bottom: 5px; }
        .menu-desc { font-size: 0.85rem; color: #888; transition: 0.3s; }
        .menu-grid-card:hover .menu-desc { color: rgba(255,255,255,0.8); }

    </style>
</head>
<body>

<div class="layout">

    <?php include("sidebar.php"); ?>

    <div class="main-content">
        
        <div class="welcome-banner d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">สวัสดี, <span class="admin-highlight"><?= isset($_SESSION['uname']) ? htmlspecialchars($_SESSION['uname']) : 'Admin'; ?></span></h2>
                <p class="mb-0 text-white-50">ยินดีต้อนรับสู่ระบบจัดการร้าน 2M3WM Sneaker </p>
            </div>
            <div class="d-none d-md-block">
                <i class="bi bi-speedometer2" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?= number_format($count_orders); ?></h3>
                        <p>ออเดอร์ทั้งหมด</p>
                    </div>
                    <div class="stat-icon bg-icon-orange">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?= number_format($count_products); ?></h3>
                        <p>สินค้าในสต็อก</p>
                    </div>
                    <div class="stat-icon bg-icon-blue">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?= number_format($count_users); ?></h3>
                        <p>สมาชิกทั้งหมด</p>
                    </div>
                    <div class="stat-icon bg-icon-green">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3"><i class="bi bi-grid-fill text-warning me-2"></i>เมนูด่วน</h5>
        <div class="row g-4">
            
            <div class="col-6 col-md-4 col-xl-3">
                <a href="admin_product.php" class="menu-grid-card">
                    <i class="bi bi-box-seam menu-icon"></i>
                    <div class="menu-title">จัดการสินค้า</div>
                    <div class="menu-desc">เพิ่ม/ลบ/แก้ไข สต็อก</div>
                </a>
            </div>

            <div class="col-6 col-md-4 col-xl-3">
                <a href="a_orderlist.php" class="menu-grid-card">
                    <i class="bi bi-receipt-cutoff menu-icon"></i>
                    <div class="menu-title">รายการสั่งซื้อ</div>
                    <div class="menu-desc">เช็คสถานะออเดอร์</div>
                </a>
            </div>

            <div class="col-6 col-md-4 col-xl-3">
                <a href="admin_brand.php" class="menu-grid-card">
                    <i class="bi bi-tags menu-icon"></i>
                    <div class="menu-title">จัดการแบรนด์</div>
                    <div class="menu-desc">เพิ่มแบรนด์สินค้า</div>
                </a>
            </div>

            <div class="col-6 col-md-4 col-xl-3">
                <a href="category_products.php" class="menu-grid-card">
                    <i class="bi bi-layers menu-icon"></i>
                    <div class="menu-title">หมวดหมู่</div>
                    <div class="menu-desc">ประเภทสินค้า</div>
                </a>
            </div>

            <div class="col-6 col-md-4 col-xl-3">
                <a href="customer_data.php" class="menu-grid-card">
                    <i class="bi bi-person-lines-fill menu-icon"></i>
                    <div class="menu-title">ข้อมูลลูกค้า</div>
                    <div class="menu-desc">ดูรายชื่อสมาชิก</div>
                </a>
            </div>

             <div class="col-6 col-md-4 col-xl-3">
                <a href="logout.php" class="menu-grid-card" onclick="return confirm('ต้องการออกจากระบบใช่หรือไม่?')">
                    <i class="bi bi-box-arrow-right menu-icon"></i>
                    <div class="menu-title">ออกจากระบบ</div>
                    <div class="menu-desc">Logout</div>
                </a>
            </div>

        </div>

    </div> </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>