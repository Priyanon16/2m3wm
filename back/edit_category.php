<?php
    include_once("check_login.php"); 
    include_once("connectdb.php"); // ต้องเชื่อมต่อ DB เพื่อดึงข้อมูลเดิมมาโชว์

    // รับค่า ID ของหมวดหมู่ที่ต้องการแก้ไข
    if (isset($_GET['id'])) {
        $cat_id = mysqli_real_escape_string($conn, $_GET['id']);
        $sql = "SELECT * FROM category WHERE cat_id = '$cat_id'"; // ตรวจสอบชื่อฟิลด์ cat_id ให้ตรงกับ DB
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
    <title>แก้ไขหมวดหมู่สินค้า - ปรียานนท์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #fff5f8; font-family: 'Kanit', sans-serif; }
        .navbar { background-color: #f06292 !important; }
        .card-edit {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 25px rgba(240, 98, 146, 0.1);
        }
        .card-header-pink {
            background-color: #fce4ec;
            color: #ad1457;
            border-radius: 20px 20px 0 0;
            padding: 20px;
        }
        .btn-pink {
            background-color: #f06292;
            color: white;
            border-radius: 10px;
            padding: 10px 25px;
            border: none;
            transition: 0.3s;
        }
        .btn-pink:hover { background-color: #d81b60; color: white; }
        .form-control {
            border-radius: 10px;
            border: 1px solid #f8bbd0;
            padding: 12px;
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
            
            <div class="card card-edit">
                <div class="card-header card-header-pink">
                    <h4 class="mb-0"><i class="fa-solid fa-pen-to-square me-2"></i>แก้ไขหมวดหมู่สินค้า</h4>
                </div>
                <div class="card-body p-4">
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

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="category_products.php" class="btn btn-light px-4" style="border-radius: 10px;">ยกเลิก</a>
                            <button type="submit" class="btn btn-pink">
                                <i class="fa-solid fa-check me-1"></i> อัปเดตข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>