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
    background: linear-gradient(135deg,#f8f9fa,#eef1f4);
}

/* ===== HEADER ===== */
header {
    background: linear-gradient(90deg,#111,#1a1a1a);
    padding: 1.5rem 0;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
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
    padding: 8px 22px;
    transition: .3s;
}

.btn-logout:hover {
    background: #e64a19;
    transform: translateY(-2px);
}

/* ===== WELCOME CARD ===== */
.welcome-card {
    background: #fff;
    border-radius: 20px;
    padding: 50px;
    margin-top: 40px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    border-bottom: 6px solid #ff5722;
    position: relative;
}

.admin-name {
    color: #ff5722;
    font-weight: 700;
}

/* ===== STAT BOX ===== */
.stat-card {
    background:#fff;
    border-radius:20px;
    padding:25px;
    text-align:center;
    box-shadow:0 8px 25px rgba(0,0,0,0.05);
    transition:.3s;
}

.stat-card:hover {
    transform:translateY(-6px);
}

.stat-number {
    font-size:28px;
    font-weight:700;
    color:#ff5722;
}

/* ===== MENU CARD ===== */
.menu-card {
    border:none;
    border-radius:25px;
    background:#fff;
    padding:45px 25px;
    text-align:center;
    transition:.3s;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
    text-decoration:none;
    color:#333;
}

.menu-card:hover {
    transform:translateY(-12px);
    box-shadow:0 20px 45px rgba(255,87,34,.2);
    color:#ff5722;
}

.card-icon {
    font-size:3.8rem;
    margin-bottom:20px;
    color:#ff5722;
    transition:.3s;
}

.menu-card:hover .card-icon {
    transform:scale(1.1);
}

.card-title {
    font-weight:600;
}

.card-desc {
    color:#888;
    font-size:0.9rem;
}
.container {
    max-width: 1200px;
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
            <div class="welcome-card text-center mb-5">
                <div class="mb-3">
                    <i class="bi bi-person-circle" style="font-size:70px;color:#ff5722;"></i>
                </div>

                <h1 class="h2 fw-bold">
                    ยินดีต้อนรับกลับมา
                    <span class="admin-name">
                        <?= htmlspecialchars($_SESSION['uname']); ?>
                    </span> 👋
                </h1>

                <p class="text-muted">
                    ผู้ดูแลระบบร้าน 2M3WM Sneaker
                </p>
            </div>

        </div>
    </div>
    <div class="row g-4 mb-5 text-center">

    <div class="col-md-4">
        <div class="col-lg-3 col-md-6">
            <i class="bi bi-receipt fs-1 text-warning"></i>
            <div class="stat-number">125</div>
            <div class="text-muted">ออเดอร์ทั้งหมด</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="col-lg-3 col-md-6">
            <i class="bi bi-box-seam fs-1 text-success"></i>
            <div class="stat-number">48</div>
            <div class="text-muted">สินค้าทั้งหมด</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <i class="bi bi-people fs-1 text-primary"></i>
            <div class="stat-number">320</div>
            <div class="text-muted">สมาชิกทั้งหมด</div>
        </div>
    </div>

</div>

    
    <div class="row g-4">
        <div class="col-lg-3 col-md-6">
            <a href="admin_product.php" class="menu-card">
                <i class="bi bi-box-seam card-icon" style="color: #ff5722;"></i>
                <h4 class="card-title">จัดการสินค้า</h4>
                <p class="card-desc">เพิ่มรายการสินค้าใหม่ แก้ไขราคา หรือจำนวนสต็อก</p>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="a_orderlist.php" class="menu-card">
                <i class="bi bi-receipt card-icon" style="color: #ff5722;"></i>
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
                <i class="bi bi-tags card-icon" style="color: #ff5722;"></i>
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