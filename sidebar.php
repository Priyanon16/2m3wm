<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sidebar</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">


<style>
body {
    background-color: #f8f9fa;
    font-family: 'Kanit', sans-serif;
}

.logo-text {
    font-size: 1.6rem;      /* ใหญ่ขึ้น */
    font-weight: 600;
    letter-spacing: 0.5px;
}


/* Sidebar */
.sidebar {
    width: 280px;
    min-height: 100vh;
    background: #212529;
    color: #fff;
    transition: all 0.3s ease;

    display: flex;
    flex-direction: column;
}

/* Sidebar collapsed */
.sidebar.collapsed {
    width: 80px;
}

/* Hide text when collapsed */
.sidebar.collapsed span,
.sidebar.collapsed .logo-text,
.sidebar.collapsed .user-text {
    display: none;
}
.sidebar.collapsed .submenu-arrow {
    display: none;
}
.sidebar.collapsed .collapse {
    display: none !important;
}

.sidebar {
    overflow-x: hidden;
}

/* Nav links */
.sidebar .nav-link {
    color: #fff;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
}

.sidebar .nav-link:hover {
    background: rgba(255,255,255,0.15);
}

.sidebar .nav-link i {
    font-size: 1.2rem;
}

/* Content */
.content {
    flex: 1;
    background: #fff;
    min-height: 100vh;
}

.sidebar-toggle {
    font-size: 1.8rem;   /* ขนาดไอคอน */
    padding: 0;
}

.sidebar-toggle:focus {
    box-shadow: none;
}

.sidebar .nav-link.small {
    padding-left: 28px;
    opacity: 0.85;
}

.sidebar .nav-link.small:hover {
    opacity: 1;
    background: rgba(255,255,255,0.12);
}

.brand-accent {
    color: #ff7a00;   /* สีส้ม */
}

.sidebar-toggle i {
    color: #ff7a00;
}
.sidebar-toggle:hover i {
    color: #ff9a3c;
}


</style>
</head>

<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar p-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="logo-text">
                <span class="brand-accent">2m</span>3wm
            </span>


            <button class="btn btn-link text-white sidebar-toggle" id="toggleBtn">
                <i class="bi bi-list"></i>
            </button>
        </div>



        <hr>

        <ul class="nav nav-pills flex-column mb-auto">
            <li>
                <a href="index_admin.php" class="nav-link text-white">
                    <i class="bi bi-speedometer2"></i>
                    <span>แดชบอร์ด</span>
                </a>
            </li>
            <li>
                <a href="a_orderlist.php" class="nav-link text-white">
                    <i class="bi bi-table"></i>
                    <span>ออเดอร์</span>
                </a>
            </li>
                        <li class="nav-item">
                <a class="nav-link text-white d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                    href="#productMenu"
                    role="button"
                    aria-expanded="false">

                        <div>
                            <i class="bi bi-box me-2"></i>
                            <span>สินค้า</span>
                        </div>

                        <i class="bi bi-chevron-down submenu-arrow small"></i>
                </a>

    <!-- Submenu -->
    <div class="collapse ps-4" id="productMenu">
        <ul class="nav flex-column mt-1">
            <li class="nav-item">
                <a href="edit_product.php" class="nav-link text-white small">
                    จัดการสินค้า
                </a>
            </li>
            <li class="nav-item">
                <a href="category_products.php" class="nav-link text-white small">
                    จัดการประเภทสินค้า
                </a>
            </li>
        </ul>
    </div>
</li>

            <li>
                <a href="customer_data.php" class="nav-link text-white">
                    <i class="bi bi-people"></i>
                    <span>ลูกค้า</span>
                </a>
            </li>
        </ul>

        <hr>

      <div class="dropdown mt-auto">
    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-circle me-2 fs-4"></i>
        <strong class="user-text"><?= $_SESSION['aname'] ?? 'Admin' ?></strong>
    </a>

    <ul class="dropdown-menu dropdown-menu-dark shadow">
        <li>
            <a class="dropdown-item" href="admin_profile.php">
                <i class="bi bi-person me-2"></i>โปรไฟล์
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="admin_setting.php">
                <i class="bi bi-gear me-2"></i>การตั้งค่า
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="logout.php">
                <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
            </a>
        </li>
    </ul>
</div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('toggleBtn').addEventListener('click', function () {
    const sidebar = document.getElementById('sidebar');
    const isCollapsed = sidebar.classList.contains('collapsed');

    if (!isCollapsed) {
        // 🔒 ซ่อน submenu ทันที (ก่อน animation)
        sidebar.classList.add('collapsing-submenu');

        // ปิด collapse ทั้งหมด
        document.querySelectorAll('#sidebar .collapse.show').forEach(function (el) {
            bootstrap.Collapse.getOrCreateInstance(el).hide();
        });

        // รอ 1 เฟรม แล้วค่อยพับ sidebar
        requestAnimationFrame(() => {
            sidebar.classList.remove('collapsing-submenu');
            sidebar.classList.add('collapsed');
        });
    } else {
        sidebar.classList.remove('collapsed');
    }
});
</script>



</body>
</html>
