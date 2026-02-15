<?php
session_start();
include_once("connectdb.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

/* =========================
   ลบที่อยู่
========================= */
if(isset($_GET['delete'])){

    $id = intval($_GET['delete']);

    mysqli_query($conn,"
        DELETE FROM addresses 
        WHERE address_id='$id' 
        AND user_id='$uid'
    ");

    header("Location: address.php");
    exit;
}

/* =========================
   ดึงข้อมูลแก้ไข
========================= */
$editData = null;

if(isset($_GET['edit'])){

    $id = intval($_GET['edit']);

    $result = mysqli_query($conn,"
        SELECT * FROM addresses
        WHERE address_id='$id'
        AND user_id='$uid'
    ");

    $editData = mysqli_fetch_assoc($result);
}

/* =========================
   บันทึก (เพิ่ม / แก้ไข)
========================= */
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $fullname    = mysqli_real_escape_string($conn,$_POST['fullname']);
    $phone       = mysqli_real_escape_string($conn,$_POST['phone']);
    $address     = mysqli_real_escape_string($conn,$_POST['address']);
    $district    = mysqli_real_escape_string($conn,$_POST['district']);
    $province    = mysqli_real_escape_string($conn,$_POST['province']);
    $postal_code = mysqli_real_escape_string($conn,$_POST['postal_code']);

    /* ถ้ามี address_id แสดงว่าแก้ไข */
    if(!empty($_POST['address_id'])){

        $id = intval($_POST['address_id']);

        mysqli_query($conn,"
            UPDATE addresses SET
                fullname='$fullname',
                phone='$phone',
                address='$address',
                district='$district',
                province='$province',
                postal_code='$postal_code'
            WHERE address_id='$id'
            AND user_id='$uid'
        ");

    }else{

        mysqli_query($conn,"
            INSERT INTO addresses
            (user_id, fullname, phone, address, district, province, postal_code)
            VALUES
            ('$uid','$fullname','$phone','$address','$district','$province','$postal_code')
        ");
    }

    header("Location: address.php");
    exit;
}

/* =========================
   ดึงข้อมูลทั้งหมด
========================= */
$sql = "SELECT * FROM addresses 
        WHERE user_id='$uid'
        ORDER BY address_id DESC";

$rs = mysqli_query($conn,$sql);
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

<input type="hidden" name="address_id" 
       value="<?= $editData['address_id'] ?? '' ?>">

<div class="mb-3">
<label>ชื่อผู้รับ</label>
<input type="text" name="fullname" class="form-control"
value="<?= $editData['fullname'] ?? '' ?>" required>
</div>

<div class="mb-3">
<label>เบอร์โทร</label>
<input type="text" name="phone" class="form-control"
value="<?= $editData['phone'] ?? '' ?>" required>
</div>

<div class="mb-3">
<label>ที่อยู่</label>
<textarea name="address" class="form-control" required><?= $editData['address'] ?? '' ?></textarea>
</div>

<div class="mb-3">
<label>ตำบล</label>
<input type="text" name="district" class="form-control"
value="<?= $editData['district'] ?? '' ?>" required>
</div>

<div class="mb-3">
<label>จังหวัด</label>
<input type="text" name="province" class="form-control"
value="<?= $editData['province'] ?? '' ?>" required>
</div>

<div class="mb-3">
<label>รหัสไปรษณีย์</label>
<input type="text" name="postal_code" class="form-control"
value="<?= $editData['postal_code'] ?? '' ?>" required>
</div>

<button class="btn btn-orange w-100">
<?= $editData ? 'อัปเดตที่อยู่' : 'บันทึกที่อยู่' ?>
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
<div class="address-card mb-3">

<strong><?= htmlspecialchars($row['fullname']) ?></strong><br>
<?= htmlspecialchars($row['phone']) ?><br>
<?= htmlspecialchars($row['address']) ?><br>
<?= htmlspecialchars($row['district']) ?>
<?= htmlspecialchars($row['province']) ?>
<?= htmlspecialchars($row['postal_code']) ?>

<div class="mt-3">
<a href="address.php?edit=<?= $row['address_id'] ?>"
   class="btn btn-sm btn-warning">
   แก้ไข
</a>

<a href="address.php?delete=<?= $row['address_id'] ?>"
   class="btn btn-sm btn-danger"
   onclick="return confirm('ยืนยันการลบ?')">
   ลบ
</a>
</div>

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
