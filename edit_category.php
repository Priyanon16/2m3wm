<?php
    include_once("check_login.php"); 
    include_once("connectdb.php"); // เชื่อมต่อ DB เพื่อดึงข้อมูลเดิมมาโชว์

    // รับค่า ID ของหมวดหมู่ที่ต้องการแก้ไข
    if (isset($_GET['id'])) {
        $cat_id = mysqli_real_escape_string($conn, $_GET['id']);
        $sql = "SELECT * FROM category WHERE cat_id = '$cat_id'"; 
        $result = mysqli_query($conn, $sql);
        $data = mysqli_fetch_assoc($result);

        // ถ้าไม่พบข้อมูลให้เด้งกลับ
        if (!$data) {
            header("Location: category_products.php");
            exit;
        }
    } else {
        header("Location: category_products.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขหมวดหมู่สินค้า - 2M3WM ADMIN</title>
    
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

        /* HEADER สไตล์ 2M3WM */
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

        /* CARD แก้ไขข้อมูล */
        .card-edit {
            background: white;
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-top: 20px;
        }
        .card-header-dark {
            background-color: #111;
            color: #fff;
            border-bottom: 4px solid #ff5722; /* เส้นใต้สีส้ม */
            padding: 20px;
        }
        
        .btn-orange {
            background-color: #ff5722;
            color: white;
            border-radius: 10px;
            padding: 10px 25px;
            border: none;
            transition: 0.3s;
            font-weight: 500;
        }
        .btn-orange:hover {
            background-color: #e64a19;
            color: white;
            transform: translateY(-2px);
        }
        
        .form-label {
            color: #333;
            font-size: 0.95rem;
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px;
            transition: 0.3s;
        }
        .form-control:focus {
            border-color: #ff5722;
            box-shadow: 0 0 0 0.25rem rgba(255, 87, 34, 0.1);
        }
        
        .btn-back-link {
            color: #666;
            transition: 0.3s;
        }
        .btn-back-link:hover {
            color: #ff5722;
        }
    </style>
</head>
<body>

<header>
    <div class="container d-flex align-items-center justify-content-between">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-shield-check me-2"></i>2M3WM ADMIN
        </a>
    </div>
</header>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="mb-4">
                <a href="category_products.php" class="text-decoration-none btn-back-link">
                    <i class="bi bi-arrow-left-circle me-1"></i> กลับหน้าจัดการหมวดหมู่
                </a>
            </div>
            
            <div class="card card-edit">
                <div class="card-header-dark">
                    <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>แก้ไขหมวดหมู่สินค้า</h4>
                </div>
                <div class="card-body p-4 p-lg-5">
                    <form action="update_category.php" method="POST">
                        <input type="hidden" name="cat_id" value="<?php echo $data['cat_id']; ?>">

                        <div class="mb-4">
                            <label class="form-label fw-bold">ชื่อหมวดหมู่</label>
                            <input type="text" name="cat_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($data['cat_name']); ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">คำอธิบายเพิ่มเติม</label>
                            <textarea name="cat_detail" class="form-control" rows="5"><?php echo htmlspecialchars($data['cat_detail']); ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                            <a href="category_products.php" class="btn btn-light px-4" style="border-radius: 10px;">ยกเลิก</a>
                            <button type="submit" class="btn btn-orange px-5">
                                <i class="bi bi-check-all me-1"></i> อัปเดตข้อมูลหมวดหมู่
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <footer class="text-center mt-5 mb-4 text-muted">
                <small>&copy; 2026 2M3WM SNEAKER HUB. All rights reserved.</small>
            </footer>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>