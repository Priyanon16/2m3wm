<?php
    include_once("check_login.php"); // ไฟล์นี้ต้องมี session_start() อยู่ข้างใน
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ปรียานนท์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #fff5f8;
            font-family: 'Kanit', sans-serif;
        }
        .navbar {
            background-color: #f06292 !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-link, .navbar-brand {
            color: white !important;
        }
        .welcome-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 5px 15px rgba(240, 98, 146, 0.1);
            border-left: 10px solid #f8bbd0;
        }
        .menu-card {
            border: none;
            border-radius: 20px;
            transition: all 0.3s ease;
            text-decoration: none;
            overflow: hidden;
            height: 100%;
        }
        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(240, 98, 146, 0.2);
        }
        .card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #f06292;
        }
        .btn-logout {
            background-color: #ff80ab;
            border: none;
            border-radius: 10px;
            color: white;
            padding: 8px 20px;
        }
        .btn-logout:hover {
            background-color: #c2185b;
            color: white;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fa-solid fa-heart-pulse me-2"></i>Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="#"class="nav-link">ออเดอร์</a></li>
                <li class="nav-item">
                <li class="nav-item"><a href="#"class="nav-link">สินค้า</a></li>
                <li class="nav-item">
                <li class="nav-item"><a href="#"class="nav-link">จัดการลูกค้า</a></li>
                <li class="nav-item">
                    <a class="nav-link btn-logout" href="logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i> ออกจากระบบ</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="welcome-section mb-5">
        <h1 class="h3 mb-1">ยินดีต้อนรับคุณ, <span class="text-pink" style="color:#e91e63;"><?php echo htmlspecialchars($_SESSION['aname']); ?></span> ✨</h1>
        <p class="text-muted mb-0">จัดการระบบหลังบ้านของคุณได้จากเมนูด้านล่างนี้</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="products.php" class="text-decoration-none">
                <div class="card menu-card text-center p-4">
                    <div class="card-body">
                        <i class="fa-solid fa-boxes-stacked card-icon"></i>
                        <h4 class="text-dark">จัดการสินค้า</h4>
                        <p class="text-muted">เพิ่ม ลบ แก้ไข รายการสินค้า</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="orders.php" class="text-decoration-none">
                <div class="card menu-card text-center p-4">
                    <div class="card-body">
                        <i class="fa-solid fa-cart-shopping card-icon"></i>
                        <h4 class="text-dark">จัดการออเดอร์</h4>
                        <p class="text-muted">ตรวจสอบสถานะการสั่งซื้อ</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="customers.php" class="text-decoration-none">
                <div class="card menu-card text-center p-4">
                    <div class="card-body">
                        <i class="fa-solid fa-users card-icon"></i>
                        <h4 class="text-dark">จัดการลูกค้า</h4>
                        <p class="text-muted">ดูรายชื่อและข้อมูลสมาชิก</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <footer class="text-center mt-5 pt-5 pb-4 text-muted">
        <small>&copy; 2026 Preeyanon Krutnit. All rights reserved.</small>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>