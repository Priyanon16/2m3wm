<?php
session_start();
include_once("connectdb.php");

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['uid'];

// ดึงข้อมูลจากตาราง users เชื่อมกับตาราง address
$sql = "SELECT u.*, a.phone, a.address 
        FROM users u 
        LEFT JOIN address a ON u.id = a.user_id 
        WHERE u.id = '$uid' LIMIT 1";
$rs = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($rs);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขโปรไฟล์ลูกค้า - 2M3WM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f5f5f5; font-family: 'Kanit', sans-serif; }
        .profile-container { max-width: 750px; margin: 40px auto; }
        .card { border-radius: 20px; border: none; }
        .profile-img { width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 4px solid #ff6600; padding: 4px; background: #fff; }
        .section-title { font-weight: 600; color: #ff6600; margin-top: 20px; margin-bottom: 10px; }
        .btn-orange { background: linear-gradient(45deg, #ff6600, #ff8533); border: none; color: #fff; font-weight: 600; padding: 12px; border-radius: 10px; transition: 0.3s; }
        .btn-orange:hover { background: #000; color: #fff; }
        .readonly-field { background: #f1f1f1 !important; }
    </style>
</head>
<body>

<?php include("header.php"); ?>

<div class="profile-container">
    <div class="card shadow-lg p-4">
        <h3 class="text-center fw-bold mb-4">แก้ไขโปรไฟล์ลูกค้า</h3>
        
        <form action="update_profile_db.php" method="POST" enctype="multipart/form-data">
            <div class="text-center mb-3">
                <img src="https://via.placeholder.com/140" id="previewImage" class="profile-img">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">เปลี่ยนรูปโปรไฟล์</label>
                <input type="file" name="p_img" class="form-control" id="profileImage" accept="image/*">
            </div>

            <hr>

            <div class="section-title">ข้อมูลบัญชี</div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="fw-semibold">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">วันที่สมัคร</label>
                    <input type="text" class="form-control readonly-field" value="<?= date('d/m/Y', strtotime($user['created_at'])) ?>" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="fw-semibold">Email</label>
                    <input type="email" class="form-control readonly-field" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-semibold">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="08xxxxxxxx" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="fw-semibold">ที่อยู่จัดส่ง <span class="text-danger">*</span></label>
                <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>

            <hr>

            <div class="section-title">เปลี่ยนรหัสผ่าน (ปล่อยว่างถ้าไม่เปลี่ยน)</div>
            <div class="mb-3">
                <label class="fw-semibold">รหัสผ่านใหม่</label>
                <input type="password" name="new_password" class="form-control" placeholder="กรอกรหัสผ่านใหม่">
            </div>

            <button type="submit" name="Submit" class="btn btn-orange w-100 mt-3">บันทึกการเปลี่ยนแปลง</button>
        </form>
    </div>
</div>

<script>
    document.getElementById("profileImage").addEventListener("change", function(event) {
        const reader = new FileReader();
        reader.onload = function(){ document.getElementById("previewImage").src = reader.result; }
        reader.readAsDataURL(event.target.files[0]);
    });
</script>
</body>
</html>