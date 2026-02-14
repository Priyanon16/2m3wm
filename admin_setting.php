<?php
include_once("check_login.php"); 
include_once("connectdb.php"); 

// ดึงข้อมูล Admin ปัจจุบันจากฐานข้อมูล
$aid = $_SESSION['aid']; 
$sql = "SELECT * FROM users WHERE id = '$aid' LIMIT 1";
$result = mysqli_query($conn, $sql);
$admin = mysqli_fetch_array($result);

// ตรวจสอบการบันทึกข้อมูล
if (isset($_POST['save_settings'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_password = $_POST['new_password'];
    
    // อัปเดตข้อมูลพื้นฐาน
    $update_sql = "UPDATE users SET name = '$name', email = '$email' WHERE id = '$aid'";
    mysqli_query($conn, $update_sql);
    
    // ถ้ามีการกรอกรหัสผ่านใหม่ ให้ทำการอัปเดตด้วย
    if (!empty($new_password)) {
        $update_pwd = "UPDATE users SET password = '$new_password' WHERE id = '$aid'";
        mysqli_query($conn, $update_pwd);
    }
    
    // อัปเดต Session ชื่อใหม่
    $_SESSION['uname'] = $name;
    
    echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location='admin_setting.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตั้งค่าผู้ดูแลระบบ - 2M3WM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; font-family: 'Kanit', sans-serif; }
        .sidebar { width: 280px; min-height: 100vh; background: #212529; color: #fff; } /* */
        .content { flex: 1; padding: 30px; }
        .setting-card { background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .btn-orange { background-color: #ff7a00; color: white; border-radius: 8px; border: none; padding: 10px 25px; transition: 0.3s; }
        .btn-orange:hover { background-color: #e66e00; transform: translateY(-2px); color: white; }
        .brand-accent { color: #ff7a00; }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include_once("sidebar.php"); ?>

    <div class="content">
        <h2 class="mb-4 fw-bold"><i class="bi bi-gear-fill me-2 brand-accent"></i> ตั้งค่าผู้ดูแลระบบ</h2>

        <div class="setting-card">
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">ชื่อ-นามสกุล</label>
                        <input type="text" name="name" class="form-control border-0 bg-light" value="<?= htmlspecialchars($admin['name']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">อีเมล (ใช้สำหรับ Login)</label>
                        <input type="email" name="email" class="form-control border-0 bg-light" value="<?= htmlspecialchars($admin['email']) ?>" required>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">รหัสผ่านใหม่ (ปล่อยว่างถ้าไม่ต้องการเปลี่ยน)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-key"></i></span>
                            <input type="password" name="new_password" class="form-control border-0 bg-light" placeholder="ระบุรหัสผ่านใหม่">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" name="save_settings" class="btn btn-orange">
                        <i class="bi bi-check-circle me-1"></i> บันทึกการตั้งค่า
                    </button>
                    <a href="index_admin.php" class="btn btn-outline-secondary ms-2 border-0">ยกเลิก</a>
                </div>
            </form>
        </div>

        <footer class="text-center mt-5 text-muted">
            <small>&copy; 2026 2M3WM SNEAKER HUB. All rights reserved.</small>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>