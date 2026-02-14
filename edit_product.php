<?php 
    // 1. เปิดแสดง Error (เอาไว้ดูว่าพังตรงไหน)
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();

    // 2. เช็คว่ามีไฟล์เหล่านี้จริงไหม (ถ้าไม่มี ระบบจะแจ้งเตือน)
    if (!file_exists("check_login.php")) { die("หาไฟล์ check_login.php ไม่เจอ"); }
    if (!file_exists("connect.php")) { die("หาไฟล์ connect.php ไม่เจอ"); }

    include_once("check_login.php"); 
    include_once("connect.php"); 

    // 3. ตรวจสอบการเชื่อมต่อฐานข้อมูล
    if ($conn->connect_error) {
        die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
    }

    // ดึงข้อมูลสินค้าทั้งหมด
    $sql = "SELECT * FROM products ORDER BY p_id DESC";
    $result = $conn->query($sql);

    // ถ้า Query พัง ให้บอกสาเหตุ
    if (!$result) {
        die("ดึงข้อมูลไม่ได้ SQL Error: " . $conn->error);
    }
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการสินค้า - 2M3WM Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f4f6f8; }
        .navbar { background-color: #111; }
        .btn-orange { background-color: #ff9900; color: white; border: none; }
        .btn-orange:hover { background-color: #e68a00; color: white; }
        .card-header { background-color: white; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4 p-3">
    <div class="container">
        <span class="navbar-brand mb-0 h1 fw-bold">2M3WM ADMIN</span>
        <div class="d-flex align-items-center text-white">
            <span class="me-3">Admin: <span style="color: #ff9900;"><?php echo isset($_SESSION['aname']) ? $_SESSION['aname'] : 'Guest'; ?></span></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-box-seam me-2"></i>จัดการสต็อกสินค้า</h3>
        <a href="product_form.php" class="btn btn-orange rounded-pill px-4">
            <i class="bi bi-plus-lg me-1"></i> เพิ่มสินค้าใหม่
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">รูปภาพ</th>
                            <th>ชื่อสินค้า</th>
                            <th>ราคา</th>
                            <th>รายละเอียด</th>
                            <th class="text-end pe-4">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td class="ps-4">
                                    <?php $img_show = !empty($row['p_img']) ? "uploads/".$row['p_img'] : "https://dummyimage.com/60x60/dee2e6/6c757d.jpg"; ?>
                                    <img src="<?php echo $img_show; ?>" width="60" height="60" class="rounded object-fit-cover bg-light" alt="img">
                                </td>
                                <td class="fw-bold"><?php echo $row['p_name']; ?></td>
                                <td class="text-success fw-bold"><?php echo number_format($row['p_price']); ?> ฿</td>
                                <td class="text-secondary small">
                                    <?php 
                                        // ใช้ substr แทน mb_strimwidth เพื่อความชัวร์เรื่อง Server รองรับ
                                        $detail = $row['p_detail'];
                                        if(strlen($detail) > 50) {
                                            echo substr($detail, 0, 50) . "...";
                                        } else {
                                            echo $detail;
                                        }
                                    ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="product_form.php?id=<?php echo $row['p_id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                    <a href="product_save.php?del=<?php echo $row['p_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ยืนยันการลบสินค้า?');"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php } } else { ?>
                            <tr><td colspan="5" class="text-center py-5 text-secondary">ยังไม่มีสินค้าในระบบ</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>