<?php
session_start();
include_once("connectdb.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

/* =========================
   บันทึกที่อยู่ใหม่
========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname    = mysqli_real_escape_string($conn,$_POST['fullname']);
    $phone       = mysqli_real_escape_string($conn,$_POST['phone']);
    $address     = mysqli_real_escape_string($conn,$_POST['address']);
    $district    = mysqli_real_escape_string($conn,$_POST['district']);
    $province    = mysqli_real_escape_string($conn,$_POST['province']);
    $postal_code = mysqli_real_escape_string($conn,$_POST['postal_code']);

    $sql_insert = "INSERT INTO addresses 
        (user_id, fullname, phone, address, district, province, postal_code)
        VALUES
        ('$uid','$fullname','$phone','$address','$district','$province','$postal_code')
    ";

    mysqli_query($conn,$sql_insert) or die(mysqli_error($conn));

    header("Location: address.php");
    exit;
}

/* =========================
   ดึงข้อมูลทั้งหมด
========================= */
$sql = "SELECT * FROM addresses 
        WHERE user_id='$uid' 
        ORDER BY address_id DESC";

$rs  = mysqli_query($conn,$sql);
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
body{
    font-family:'Kanit',sans-serif;
    background:#f4f6f9;
}
.card-custom{
    border-radius:15px;
    border:none;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}
.btn-orange{
    background:#ff7a00;
    border:none;
    color:#fff;
}
.btn-orange:hover{
    background:#e66e00;
}
.address-card{
    border:1px solid #eee;
    border-radius:10px;
    padding:15px;
    transition:.2s;
}
.address-card:hover{
    background:#fff3e6;
}
</style>
</head>

<body>

<?php include("header.php"); ?>

<div class="container mt-5 mb-5">
<div class="row">

<!-- ================== เพิ่มที่อยู่ใหม่ ================== -->
<div class="col-md-6">

<div class="card card-custom p-4">
<h4 class="mb-4 text-warning">
<i class="bi bi-plus-circle"></i> เพิ่มที่อยู่ใหม่
</h4>

<form method="POST">

<div class="mb-3">
<label>ชื่อผู้รับ</label>
<input type="text" name="fullname" class="form-control" required>
</div>

<div class="mb-3">
<label>เบอร์โทร</label>
<input type="text" name="phone" class="form-control" required>
</div>

<div class="mb-3">
<label>ที่อยู่</label>
<textarea name="address" class="form-control" rows="2" required></textarea>
</div>

<div class="row">
<div class="col-md-6 mb-3">
<label>ตำบล</label>
<input type="text" name="district" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>อำเภอ</label>
<input type="text" name="amphure" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>จังหวัด</label>
<input type="text" name="province" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>รหัสไปรษณีย์</label>
<input type="text" name="postal_code" maxlength="5" class="form-control" required>
</div>
</div>

<button type="submit" class="btn btn-orange w-100">
บันทึกที่อยู่
</button>

</form>
</div>
</div>

<!-- ================== รายการที่อยู่ทั้งหมด ================== -->
<div class="col-md-6">

<div class="card card-custom p-4">
<h4 class="mb-4">
<i class="bi bi-geo-alt"></i> รายการที่อยู่ของฉัน
</h4>

<?php if(mysqli_num_rows($rs) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($rs)): ?>
        <div class="card mb-3 p-3">
            <strong><?= htmlspecialchars($row['fullname']) ?></strong><br>
            <?= htmlspecialchars($row['phone']) ?><br>
            <?= htmlspecialchars($row['address']) ?><br>
            <?= htmlspecialchars($row['district']) ?>
            <?= htmlspecialchars($row['province']) ?>
            <?= htmlspecialchars($row['postal_code']) ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p class="text-muted">ยังไม่มีที่อยู่</p>
<?php endif; ?>


</div>
</div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
