<?php
    include_once("check_login.php"); 
    // สมมติว่าคุณมีการเชื่อมต่อฐานข้อมูลใน config.php หรือไฟล์ที่เกี่ยวข้อง
    // include_once("config.php"); 
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหมวดหมู่สินค้า - ปรียานนท์</title>
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
        .main-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 5px 15px rgba(240, 98, 146, 0.1);
            border-top: 5px solid #f06292;
        }
        .table thead {
            background-color: #f8bbd0;
            color: #ad1457;
        }
        .btn-pink {
            background-color: #f06292;
            color: white;
            border-radius: 10px;
            transition: 0.3s;
        }
        .btn-pink:hover {
            background-color: #d81b60;
            color: white;
        }
        .btn-outline-pink {
            border: 2px solid #f06292;
            color: #f06292;
            border-radius: 10px;
        }
        .btn-outline-pink:hover {
            background-color: #f06292;
            color: white;
        }
        .badge-category {
            background-color: #fce4ec;
            color: #d81b60;
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 400;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-heart-pulse me-2"></i>Admin</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="index.php" class="nav-link">หน้าหลัก</a></li>
                <li class="nav-item"><a href="products.php" class="nav-link">สินค้า</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link text-warning">ออกจากระบบ</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-dark"><i class="fa-solid fa-tags me-2 text-pink" style="color:#f06292;"></i> จัดการหมวดหมู่สินค้า</h2>
        <button class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fa-solid fa-plus-circle me-1"></i> เพิ่มหมวดหมู่ใหม่
        </button>
    </div>

    <div class="main-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="50%">ชื่อหมวดหมู่</th>
                        <th width="20%">จำนวนสินค้า</th>
                        <th width="20%" class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><span class="badge-category">เครื่องสำอางผิวหน้า</span></td>
                        <td>24 รายการ</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><span class="badge-category">อุปกรณ์ทำผม</span></td>
                        <td>12 รายการ</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="text-center mt-5 pt-5 pb-4 text-muted">
        <small>&copy; 2026 Preeyanon Krutnit. All rights reserved.</small>
    </footer>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="background-color: #fce4ec; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title" style="color: #ad1457;">เพิ่มหมวดหมู่สินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="process_category.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">ชื่อหมวดหมู่</label>
                        <input type="text" name="cat_name" class="form-control" placeholder="เช่น สกินแคร์, ลิปสติก" required style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">คำอธิบายสั้นๆ (ถ้ามี)</label>
                        <textarea name="cat_detail" class="form-control" rows="3" style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">ยกเลิก</button>
                    <button type="submit" class="btn btn-pink px-4">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>