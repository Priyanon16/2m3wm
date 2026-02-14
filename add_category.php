<?php
    include_once("check_login.php"); 
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มหมวดหมู่สินค้า - ปรียานนท์</title>
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
        }
        .card-add {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 25px rgba(240, 98, 146, 0.1);
            overflow: hidden;
        }
        .card-header-pink {
            background-color: #fce4ec;
            color: #ad1457;
            border-bottom: none;
            padding: 20px;
            font-weight: 500;
        }
        .btn-pink {
            background-color: #f06292;
            color: white;
            border-radius: 10px;
            padding: 10px 25px;
            border: none;
            transition: 0.3s;
        }
        .btn-pink:hover {
            background-color: #d81b60;
            color: white;
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #f8bbd0;
            padding: 12px;
        }
        .form-control:focus {
            border-color: #f06292;
            box-shadow: 0 0 0 0.25 row rgba(240, 98, 146, 0.25);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-heart-pulse me-2"></i>Admin</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="mb-3">
                <a href="category_products.php" class="text-decoration-none text-muted">
                    <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้าจัดการหมวดหมู่
                </a>
            </div>
            
            <div class="card card-add">
                <div class="card-header card-header-pink">
                    <h4 class="mb-0"><i class="fa-solid fa-folder-plus me-2"></i>เพิ่มหมวดหมู่สินค้าใหม่</h4>
                </div>
                <div class="card-body p-4">
                    <form action="process_category.php" method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold">ชื่อหมวดหมู่ <span class="text-danger">*</span></label>
                            <input type="text" name="cat_name" class="form-control" placeholder="ระบุชื่อหมวดหมู่ เช่น น้ำหอม, บำรุงผิว" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">คำอธิบายเพิ่มเติม</label>
                            <textarea name="cat_detail" class="form-control" rows="5" placeholder="ระบุรายละเอียดสั้นๆ เกี่ยวกับหมวดหมู่นี้"></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-light px-4" style="border-radius: 10px;">ล้างข้อมูล</button>
                            <button type="submit" class="btn btn-pink">
                                <i class="fa-solid fa-save me-1"></i> บันทึกข้อมูลหมวดหมู่
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <footer class="text-center mt-5 mb-4 text-muted">
                <small>&copy; 2026 Preeyanon Krutnit. All rights reserved.</small>
            </footer>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>