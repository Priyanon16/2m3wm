<?php
session_start();
include_once("connectdb.php");

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['uid'];

// ดึงข้อมูลที่อยู่ล่าสุดจากตาราง address
$sql = "SELECT * FROM address WHERE user_id = '$uid' LIMIT 1";
$rs = mysqli_query($conn, $sql);
$addr = mysqli_fetch_assoc($rs);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ที่อยู่จัดส่ง - 2M3WM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f4f4f4; }
        .setting-container { max-width: 900px; margin: 50px auto; }
        .profile-card { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: none; }
        .side-menu { background: #111; color: #fff; padding: 20px; min-height: 500px; }
        .side-menu .nav-link { color: #aaa; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .side-menu .nav-link:hover, .side-menu .nav-link.active { background: #ff7a00; color: #fff; }
        .form-label { font-weight: 500; color: #555; font-size: 0.9rem; }
        .form-control, .form-select { border-radius: 8px; background-color: #f9f9f9; border: 1px solid #eee; padding: 10px 15px; }
        .form-control:focus { border-color: #ff7a00; box-shadow: none; background-color: #fff; }
        .btn-orange { background: #ff7a00; border: none; color: #fff; padding: 12px 30px; border-radius: 50px; font-weight: 500; transition: 0.3s; }
        .btn-orange:hover { background: #e66e00; transform: translateY(-2px); }
    </style>
</head>
<body>

<?php include("header.php"); ?>

<div class="container setting-container">
    <div class="row profile-card">
        <div class="col-md-4 side-menu">
            <div class="text-center mb-4">
                <i class="bi bi-geo-alt-fill" style="font-size: 60px; color: #ff7a00;"></i>
                <h5 class="mt-2">ตั้งค่าที่อยู่</h5>
                <p class="small text-secondary">จัดการที่อยู่สำหรับการจัดส่ง</p>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link" href="setting.php"><i class="bi bi-person me-2"></i> ข้อมูลส่วนตัว</a>
                <a class="nav-link active" href="address.php"><i class="bi bi-geo-alt me-2"></i> ที่อยู่จัดส่ง</a>
                <a class="nav-link" href="ordersatatus.php"><i class="bi bi-truck me-2"></i> เช็คสถานะออเดอร์</a>
                <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> ออกจากระบบ</a>
            </nav>
        </div>

        <div class="col-md-8 p-5">
            <h4 class="mb-4 fw-bold"><i class="bi bi-house-door me-2 text-warning"></i>ที่อยู่จัดส่ง</h4>
            
            <form action="save_address.php" method="POST">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">ชื่อ-นามสกุล (ผู้รับสินค้า)</label>
                        <input type="text" name="fullname" class="form-control" 
                               value="<?= htmlspecialchars($addr['fullname'] ?? '') ?>" placeholder="ระบุชื่อจริง-นามสกุล" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">เบอร์โทรศัพท์ติดต่อ</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="<?= htmlspecialchars($addr['phone'] ?? '') ?>" placeholder="08X-XXX-XXXX" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">ที่อยู่ (บ้านเลขที่, หมู่บ้าน, ถนน)</label>
                        <textarea name="address" class="form-control" rows="3" 
                                  placeholder="ระบุที่อยู่โดยละเอียด" required><?= htmlspecialchars($addr['address'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">ตำบล / แขวง</label>
                        <input type="text" name="district" class="form-control" 
                               value="<?= htmlspecialchars($addr['district'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">อำเภอ / เขต</label>
                        <input type="text" name="amphure" class="form-control" 
                               value="<?= htmlspecialchars($addr['amphure'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">จังหวัด</label>
                        <input type="text" name="province" class="form-control" 
                               value="<?= htmlspecialchars($addr['province'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">รหัสไปรษณีย์</label>
                        <input type="text" name="postal_code" class="form-control" 
                               value="<?= htmlspecialchars($addr['postal_code'] ?? '') ?>" maxlength="5" required>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-orange shadow-sm px-5">บันทึกที่อยู่</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>