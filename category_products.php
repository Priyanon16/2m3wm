<?php
    include_once("check_login.php"); 
    include_once("connectdb.php"); // เชื่อมต่อฐานข้อมูล
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหมวดหมู่สินค้า - 2M3WM ADMIN</title>
    
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

        /* HEADER สไตล์เดียวกับหน้า Dashboard */
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
        }

        /* MAIN CONTENT */
        .main-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-top: 20px;
        }

        .table thead {
            background-color: #111;
            color: #fff;
        }
        
        .btn-orange {
            background-color: #ff5722;
            color: white;
            border-radius: 8px;
            transition: 0.3s;
            border: none;
        }
        .btn-orange:hover {
            background-color: #e64a19;
            color: white;
        }

        .badge-category {
            background-color: #fff3e0;
            color: #ff5722;
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 500;
        }

        .btn-action {
            border-radius: 8px;
            padding: 5px 10px;
        }
    </style>
</head>
<body>

<header>
    <div class="container d-flex align-items-center justify-content-between">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-shield-check me-2"></i>2M3WM ADMIN
        </a>
        <div class="d-flex align-items-center gap-4">
            <a href="index.php" class="text-white text-decoration-none d-none d-md-block">หน้าหลัก</a>
            <a href="products.php" class="text-white text-decoration-none d-none d-md-block">สินค้า</a>
            <a href="logout.php" class="btn-logout text-decoration-none">
                <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
            </a>
        </div>
    </div>
</header>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 fw-bold"><i class="bi bi-tags-fill me-2 text-orange" style="color:#ff5722;"></i> จัดการหมวดหมู่สินค้า</h2>
        <button class="btn btn-orange px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-circle me-1"></i> เพิ่มหมวดหมู่ใหม่
        </button>
    </div>

    <div class="main-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="10%" class="ps-4">ID</th>
                        <th width="50%">ชื่อหมวดหมู่</th>
                        <th width="20%">จำนวนสินค้า</th>
                        <th width="20%" class="text-center pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // ตัวอย่างการดึงข้อมูลจาก Database
                        $sql = "SELECT * FROM category ORDER BY cat_id DESC";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_array($result)) {
                    ?>
                    <tr>
                        <td class="ps-4"><?php echo $row['cat_id']; ?></td>
                        <td><span class="badge-category"><?php echo $row['cat_name']; ?></span></td>
                        <td>
                            <?php 
                                // โค้ดสำหรับนับจำนวนสินค้าในหมวดหมู่นี้ (ถ้ามีตารางสินค้า)
                                echo "0 รายการ"; 
                            ?>
                        </td>
                        <td class="text-center pe-4">
                            <a href="edit_category.php?id=<?php echo $row['cat_id']; ?>" class="btn btn-sm btn-outline-dark btn-action me-1">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="delete_category.php?id=<?php echo $row['cat_id']; ?>" class="btn btn-sm btn-outline-danger btn-action" onclick="return confirm('ยืนยันการลบหมวดหมู่นี้?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="text-center mt-5 pb-4 text-muted">
        <small>&copy; 2026 2M3WM SNEAKER HUB. All rights reserved.</small>
    </footer>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header bg-dark text-white" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold">เพิ่มหมวดหมู่สินค้าใหม่</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="process_category.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">ชื่อหมวดหมู่</label>
                        <input type="text" name="cat_name" class="form-control" placeholder="เช่น Running, Basketball" required style="border-radius: 10px; padding: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">คำอธิบาย (ถ้ามี)</label>
                        <textarea name="cat_detail" class="form-control" rows="3" style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 10px;">ยกเลิก</button>
                    <button type="submit" name="Submit" class="btn btn-orange px-4 fw-bold">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>