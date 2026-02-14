<?php
    include_once("check_login.php"); 
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2M3WM Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: #f8f9fa;
        }

        /* HEADER - ใช้สไตล์เดียวกับหน้าหลัก */
        header {
            background: #111;
            padding: 1.5rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 2px;
            color: #fff !important;
        }
        .btn-logout {
            background: #ff5722;
            color: white !important;
            border-radius: 50px;
            padding: 8px 20px;
            transition: 0.3s;
            border: none;
        }
        .btn-logout:hover {
            background: #e64a19;
            transform: scale(1.05);
        }

        /* WELCOME SECTION */
        .welcome-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            padding: 40px;
            margin-top: -30px; /* ให้เกยขึ้นไปบนสีพื้นหลังถ้ามี */
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-bottom: 5px solid #ff5722;
        }
        .admin-name {
            color: #ff5722;
            font-weight: 600;
        }

        /* MENU CARDS */
        .menu-card {
            border: none;
            border-radius: 20px;
            background: #fff;
            transition: all 0.3s ease;
            text-align: center;
            padding: 40px 20px;
            height: 100%;
            display: block;
            text-decoration: none;
            color: #333;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(255, 87, 34, 0.15);
            color: #ff5722;
        }
        .card-icon {
            font-size: 3.5rem;
            margin-bottom: 20px;
            display: block;
            transition: 0.3s;
        }
        .menu-card:hover .card-icon {
            transform: scale(1.1);
            color: #ff5722;
        }
        .card-title {
            font-weight: 600;
            margin-bottom: 10px;
        }
        .card-desc {
            color: #888;
            font-size: 0.9rem;
        }

        footer {
            color: #aaa;
            margin-top: 80px;
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
            <span class="text-white-50 d-none d-md-block">สถานะ: ผู้ดูแลระบบ</span>
            <a href="logout.php" class="btn-logout text-decoration-none">
                <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
            </a>
        </div>
    </div>
</header>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="welcome-card mb-5 text-center">
                <h1 class="h2 mb-2">ยินดีต้อนรับกลับมา, <span class="admin-name"><?php echo htmlspecialchars($_SESSION['aname']); ?></span> 👋</h1>
                <p class="mb-0 text-muted">เลือกเมนูที่ต้องการจัดการเพื่ออัปเดตข้อมูลหน้าร้าน Sneaker ของคุณ</p>
            </div>
        </div>
    </div>

    
    <div class="row g-4 justify-content-center">
        <div class="col-md-4 col-sm-6">
            <a href="admin_product.php" class="menu-card">
                <i class="bi bi-box-seam card-icon" style="color: #ff5722;"></i>
                <h4 class="card-title">จัดการสินค้า</h4>
                <p class="card-desc">เพิ่มรายการสินค้าใหม่ แก้ไขราคา หรือจำนวนสต็อก</p>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="orderlist.php" class="menu-card">
                <i class="bi bi-receipt card-icon" style="color: #111;"></i>
                <h4 class="card-title">จัดการออเดอร์</h4>
                <p class="card-desc">ตรวจสอบรายการสั่งซื้อและการแจ้งชำระเงินของลูกค้า</p>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="customers_data.php" class="menu-card">
                <i class="bi bi-people card-icon" style="color: #ff5722;"></i>
                <h4 class="card-title">จัดการลูกค้า</h4>
                <p class="card-desc">ดูรายชื่อสมาชิก และประวัติการเข้าใช้งาน</p>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="category_products.php" class="menu-card">
                <i class="bi bi-tags card-icon" style="color: #111;"></i>
                <h4 class="card-title">จัดการหมวดหมู่</h4>
                <p class="card-desc">แยกประเภทสินค้า เช่น รองเท้าวิ่ง, รองเท้าแฟชั่น</p>
            </a>
        </div>
    </div>

    <footer class="text-center pb-5">
        <p class="small">&copy; 2026 2M3WM SNEAKER HUB - ADMIN PANEL</p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>