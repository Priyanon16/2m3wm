<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

/* =========================
   ตรวจสอบการเข้าสู่ระบบ
========================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

/* =========================
   ดึงข้อมูล user จริงจากฐานข้อมูล
========================= */
$sql = "SELECT * FROM users WHERE id = '$uid' LIMIT 1";
$rs  = mysqli_query($conn,$sql);
$user = mysqli_fetch_assoc($rs);

/* =========================
   แปลงวันที่สมัคร
========================= */
$reg_date = "";
if (!empty($user['created_at'])) {
    $reg_date = date('Y-m-d', strtotime($user['created_at']));
}
?>

<?php include("header.php"); ?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แก้ไขโปรไฟล์ - 2M3WM</title>


<style>
body{
    background:#f5f5f5;
    font-family:'Kanit',sans-serif;
}
.profile-card{
    max-width:800px;
    margin:50px auto;
    background:#fff;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
}
.section-title{
    color:#ff7a00;
    font-weight:600;
    border-bottom:2px solid #eee;
    padding-bottom:10px;
    margin-bottom:20px;
}
.form-control-readonly{
    background:#e9ecef !important;
    cursor:not-allowed;
}
.btn-orange{
    background:#ff7a00;
    color:#fff;
    border:none;
    font-weight:600;
}
.btn-orange:hover{
    background:#e66e00;
}
</style>
</head>

<body>

<div class="container">
<div class="profile-card p-4 p-md-5">

<h3 class="text-center mb-4 fw-bold">แก้ไขโปรไฟล์ลูกค้า</h3>

<form action="update_profile_db.php" method="POST">

<h5 class="section-title">ข้อมูลบัญชี</h5>

<div class="row g-3">

<div class="col-md-6">
<label class="form-label fw-bold">ชื่อ-นามสกุล</label>
<input type="text" name="name"
class="form-control"
value="<?= htmlspecialchars($user['name']) ?>"
required>
</div>

<div class="col-md-6">
<label class="form-label fw-bold text-muted">วันที่สมัคร</label>
<input type="date"
class="form-control form-control-readonly"
value="<?= $reg_date ?>"
readonly>
</div>

<div class="col-md-6">
<label class="form-label fw-bold">Email</label>
<input type="email" name="email"
class="form-control"
value="<?= htmlspecialchars($user['email']) ?>"
required>
</div>

<div class="col-md-6">
<label class="form-label fw-bold">เบอร์โทร</label>
<input type="text" name="phone"
class="form-control"
value="<?= htmlspecialchars($user['phone']) ?>"
required>
</div>

</div>

<h5 class="section-title mt-5">เปลี่ยนรหัสผ่าน</h5>

<div class="row g-3">

<div class="col-md-6">
<input type="password"
name="new_password"
class="form-control"
placeholder="รหัสผ่านใหม่ (ถ้าไม่เปลี่ยนให้เว้นว่าง)">
</div>

<div class="col-md-6">
<input type="password"
name="confirm_password"
class="form-control"
placeholder="ยืนยันรหัสผ่านใหม่">
</div>

</div>

<div class="mt-5">
<button type="submit" class="btn btn-orange w-100">
บันทึกการเปลี่ยนแปลง
</button>
</div>

</form>
</div>
</div>

</body>
</html>
