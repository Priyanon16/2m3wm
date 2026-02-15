<?php
session_start();
include_once("connectdb.php");


// ดึงข้อมูลจากตาราง users และเชื่อมกับตาราง address
$sql = "SELECT u.*, a.phone as addr_phone, a.address, a.district, a.province, a.postal_code 
        FROM users u 
        LEFT JOIN address a ON u.id = a.user_id 
        WHERE u.id = '$uid' LIMIT 1";
$rs = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($rs);

// แปลงรูปแบบวันที่สมัคร (created_at) ให้เป็น YYYY-MM-DD เพื่อแสดงใน input type="date"
$reg_date = "";
if (!empty($user['created_at'])) {
    $reg_date = date('Y-m-d', strtotime($user['created_at']));
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขโปรไฟล์ลูกค้า - 2M3WM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { background: #f5f5f5; font-family: 'Kanit', sans-serif; }
        .profile-card { max-width: 800px; margin: 50px auto; background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .section-title { color: #ff7a00; font-weight: 600; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .form-control-readonly { background-color: #e9ecef !important; color: #6c757d; cursor: not-allowed; }
        .btn-orange { background: #ff7a00; color: #fff; border: none; font-weight: 600; transition: 0.3s; padding: 15px; border-radius: 10px; }
        .btn-orange:hover { background: #e66e00; transform: translateY(-2px); }
    </style>
</head>
<body>

<?php include("header.php"); ?>

<div class="container">
    <div class="profile-card p-4 p-md-5">
        <h3 class="text-center mb-4 fw-bold">แก้ไขโปรไฟล์ลูกค้า</h3>
        
        <form action="update_profile_db.php" method="POST">
            <h5 class="section-title">ข้อมูลบัญชี</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? "") ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted">วันที่สมัคร</label>
                    <input type="date" class="form-control form-control-readonly" value="<?= $reg_date ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? "") ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['addr_phone'] ?? $user['phone'] ?? "") ?>" required>
                </div>
            </div>

            <h5 class="section-title mt-5">เปลี่ยนรหัสผ่านใหม่</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">รหัสผ่านใหม่</label>
                    <input type="password" name="new_password" class="form-control" placeholder="ปล่อยว่างหากไม่ต้องการเปลี่ยน">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="กรอกรหัสผ่านอีกครั้ง">
                </div>
            </div>

            <div class="mt-5">
                <button type="submit" name="Submit" class="btn btn-orange w-100 shadow-sm">
                    บันทึกการเปลี่ยนแปลงทั้งหมด
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>