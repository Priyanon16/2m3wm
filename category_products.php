<?php
include_once("check_login.php"); 
include_once("connectdb.php"); 
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหมวดหมู่สินค้า - 2M3WM ADMIN</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: #f8f9fa;
        }

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
            border: none;
        }

        .btn-orange {
            background-color: #ff5722;
            color: white;
            border-radius: 8px;
            border: none;
        }

        .btn-orange:hover {
            background-color: #e64a19;
        }

        .main-card {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .table thead {
            background-color: #111;
            color: #fff;
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
        <div class="d-flex gap-4">
            <a href="index.php" class="text-white text-decoration-none">หน้าหลัก</a>
            <a href="products.php" class="text-white text-decoration-none">สินค้า</a>
            <a href="logout.php" class="btn-logout text-decoration-none">
                <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
            </a>
        </div>
    </div>
</header>

<div class="container mt-5">

    <!-- แจ้งเตือน -->
    <?php if(isset($_GET['success'])) { ?>
        <div class="alert alert-success">
            <?php
                if($_GET['success']=="add") echo "เพิ่มหมวดหมู่สำเร็จ";
                if($_GET['success']=="edit") echo "แก้ไขหมวดหมู่สำเร็จ";
                if($_GET['success']=="delete") echo "ลบหมวดหมู่สำเร็จ";
            ?>
        </div>
    <?php } ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold">
            <i class="bi bi-tags-fill me-2" style="color:#ff5722;"></i>
            จัดการหมวดหมู่สินค้า
        </h2>

        <!-- ปุ่มเพิ่ม -->
        <a href="add_category.php" class="btn btn-orange px-4">
            <i class="bi bi-plus-circle me-1"></i> เพิ่มหมวดหมู่ใหม่
        </a>
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

                <?php
                $sql = "SELECT * FROM category ORDER BY cat_id DESC";
                $result = mysqli_query($conn, $sql);

                while($row = mysqli_fetch_array($result)) {
                ?>
                    <tr>
                        <td><?php echo $row['cat_id']; ?></td>

                        <td>
                            <span class="badge-category">
                                <?php echo $row['cat_name']; ?>
                            </span>
                        </td>

                        <td>
                            <?php
                            // ถ้ามีตารางสินค้า (products) และมี cat_id
                            /*
                            $count_sql = "SELECT COUNT(*) as total 
                                          FROM products 
                                          WHERE cat_id=".$row['cat_id'];
                            $count_result = mysqli_query($conn, $count_sql);
                            $count_data = mysqli_fetch_assoc($count_result);
                            echo $count_data['total']." รายการ";
                            */
                            echo "0 รายการ";
                            ?>
                        </td>

                        <td class="text-center">

                            <!-- ปุ่มแก้ไข -->
                            <a href="edit_category.php?id=<?php echo $row['cat_id']; ?>" 
                               class="btn btn-sm btn-outline-dark btn-action me-1">
                                <i class="bi bi-pencil-square"></i> แก้ไข
                            </a>

                            <!-- ปุ่มลบ -->
                            <a href="delete_category.php?id=<?php echo $row['cat_id']; ?>" 
                               class="btn btn-sm btn-outline-danger btn-action"
                               onclick="return confirm('คุณต้องการลบหมวดหมู่นี้หรือไม่?');">
                                <i class="bi bi-trash"></i> ลบ
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
