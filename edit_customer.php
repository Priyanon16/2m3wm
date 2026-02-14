<?php
include_once("check_login.php");
include_once("connectdb.php");

// 1. รับ ID ลูกค้าจาก URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location='customer_data.php';</script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. ดึงข้อมูลลูกค้ารายนี้ออกมาแสดง
$sql = "SELECT * FROM users WHERE id = '$id' AND role = 'member' LIMIT 1";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_array($result);

if (!$data) {
    echo "<script>alert('ไม่พบข้อมูลลูกค้า'); window.location='customer_data.php';</script>";
    exit;
}

// 3. ส่วนประมวลผลการอัปเดตข้อมูล
if (isset($_POST['update_customer'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $update_sql = "UPDATE users SET name = '$name', email = '$email' WHERE id = '$id'";
    
    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('แก้ไขข้อมูลสำเร็จ'); window.location='customer_data.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลลูกค้า - 2M3WM ADMIN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; font-family: 'Kanit', sans-serif; }
        .content { flex: 1; padding: 30px; min-height: 100vh; }
        .main-card { background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none; }
        .btn-orange { background-color: #ff7a00; color: white; border-radius: 8px; border: none; padding: 10px 25px; transition: 0.3s; }
        .btn-orange:hover { background-color: #e66e00; transform: translateY(-2px); color: white; }
        .brand-accent { color: #ff7a00; }
        /* Sidebar Styling (Essential for layout) */
        .sidebar { width: 280px; min-height: 100vh; background: #212529; color: #fff; display: flex; flex-direction: column; }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include_once("sidebar.php"); ?>

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold mb-0">
                <i class="bi bi-person-gear me-2 brand-accent"></i> แก้ไขข้อมูลลูกค้า
            </h2>
            <a href="customer_data.php" class="btn btn-outline-secondary border-0">
                <i class="bi bi-arrow-left"></i> กลับไปหน้ารายชื่อ
            </a>
        </div>

        <div class="main-card">
            <h5 class="fw-bold mb-4 text-muted">ข้อมูลพื้นฐานลูกค้า (ID: <?= $data['id'] ?>)</h5>
            
            <form action="" method="POST">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small text-muted">ชื่อ-นามสกุล</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" class="form-control bg-light border-0" 
                                   value="<?= htmlspecialchars($data['name']) ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-muted">อีเมล (Email)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control bg-light border-0" 
                                   value="<?= htmlspecialchars($data['email']) ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small text-muted">วันที่สมัครสมาชิก</label>
                        <input type="text" class="form-control bg-light border-0" 
                               value="<?= date('d/m/Y H:i', strtotime($data['created_at'])) ?>" disabled>
                    </div>
                </div>

                <div class="mt-5">
                    <button type="submit" name="update_customer" class="btn btn-orange px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i> บันทึกข้อมูล
                    </button>
                    <button type="reset" class="btn btn-light ms-2 px-4">รีเซ็ตค่า</button>
                </div>
            </form>
        </div>

        <footer class="text-center mt-5 text-muted">
            <small>&copy; 2026 2M3WM SNEAKER HUB. All rights reserved.</small>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script สำหรับปุ่ม Toggle Sidebar
    document.getElementById('toggleBtn')?.addEventListener('click', function () {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
    });
</script>
</body>
</html>