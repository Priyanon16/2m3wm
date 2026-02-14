<?php 
    include_once("check_login.php");
    include_once("connectdb.php"); // ดึงไฟล์เชื่อมต่อฐานข้อมูล
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการสินค้า - จีรวัฒน์ ศักดิ์วงษ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: white; }
        .nav-link { color: rgba(255,255,255,0.7); }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(255,255,255,0.1); }
        .main-content { padding: 25px; }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-3 shadow">
            <div class="text-center my-4">
                <h6 class="mb-0"><?php echo $_SESSION['aname']; ?></h6>
                <small class="text-success fw-bold">Online</small>
            </div>
            <hr class="text-secondary">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item"><a href="index2.php" class="nav-link mb-2"><i class="bi bi-house-door me-2"></i> หน้าแรก</a></li>
                <li><a href="products.php" class="nav-link active mb-2"><i class="bi bi-box-seam me-2"></i> จัดการสินค้า</a></li>
                <li><a href="orders.php" class="nav-link mb-2"><i class="bi bi-cart-check me-2"></i> จัดการออเดอร์</a></li>
                <li><a href="customers.php" class="nav-link mb-2"><i class="bi bi-people me-2"></i> จัดการลูกค้า</a></li>
                <li class="mt-4"><a href="logout.php" class="nav-link text-danger border border-danger border-opacity-25 text-center rounded">ออกจากระบบ</a></li>
            </ul>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">จัดการข้อมูลสินค้า</h1>
                <a href="product_add.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มสินค้าใหม่
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4">รหัสสินค้า</th>
                                    <th>รูปภาพ</th>
                                    <th>ชื่อสินค้า</th>
                                    <th>ราคา</th>
                                    <th>คงเหลือ</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sql = "SELECT * FROM products ORDER BY p_id DESC"; // เปลี่ยนชื่อตารางตามจริง
                                    $rs = mysqli_query($conn, $sql);
                                    while ($data = mysqli_fetch_array($rs)) {
                                ?>
                                <tr class="align-middle">
                                    <td class="px-4"><?php echo $data['p_id']; ?></td>
                                    <td>
                                        <img src="images/<?php echo $data['p_image']; ?>" class="product-img" onerror="this.src='https://placehold.co/50x50?text=No+Img'">
                                    </td>
                                    <td><?php echo $data['p_name']; ?></td>
                                    <td><?php echo number_format($data['p_price'], 2); ?> ฿</td>
                                    <td><?php echo $data['p_stock']; ?> ชิ้น</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="product_edit.php?id=<?php echo $data['p_id']; ?>" class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="product_delete.php?id=<?php echo $data['p_id']; ?>" 
                                               class="btn btn-outline-danger btn-sm" 
                                               onclick="return confirm('ยืนยันการลบสินค้าชิ้นนี้?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <footer class="mt-5 text-center text-muted small">
                &copy; 2026 ระบบจัดการหลังบ้าน 
            </footer>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>