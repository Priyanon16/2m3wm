<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>แก้ไขโปรไฟล์ลูกค้า</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f5f5f5;
    font-family: 'Segoe UI', sans-serif;
}

.profile-container {
    max-width: 750px;
    margin: 60px auto;
}

.card {
    border-radius: 20px;
    border: none;
    background: #ffffff;
}

.header-title {
    font-weight: 700;
    color: #000;
}

.profile-img {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #ff6600;
    padding: 4px;
    background: #fff;
}

.form-control {
    border-radius: 10px;
    border: 1px solid #ddd;
    padding: 10px;
}

.form-control:focus {
    border-color: #ff6600;
    box-shadow: 0 0 0 0.2rem rgba(255,102,0,0.2);
}

.section-title {
    font-weight: 600;
    color: #ff6600;
    margin-top: 25px;
    margin-bottom: 10px;
}

.readonly-field {
    background: #f1f1f1;
    font-weight: 500;
}

.btn-orange {
    background: linear-gradient(45deg, #ff6600, #ff8533);
    border: none;
    color: #fff;
    font-weight: 600;
    padding: 12px;
    border-radius: 10px;
    transition: 0.3s;
}

.btn-orange:hover {
    background: #000;
    color: #fff;
}
</style>
</head>

<body>

<div class="profile-container">
<div class="card shadow-lg p-4">

<h3 class="text-center header-title mb-4">แก้ไขโปรไฟล์ลูกค้า</h3>

<form id="profileForm">

<!-- รูปโปรไฟล์ -->
<div class="text-center mb-3">
    <img src="https://via.placeholder.com/140" id="previewImage" class="profile-img">
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">เปลี่ยนรูปโปรไฟล์ <span class="text-danger">*</span></label>
    <input type="file" class="form-control" id="profileImage" accept="image/*">
</div>

<hr>

<div class="section-title">ข้อมูลบัญชี</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="fw-semibold">Username <span class="text-danger">*</span></label>
        <input type="text" class="form-control" placeholder="กรอก Username">
    </div>

   <div class="col-md-3">
                    <label for="birthDate" class="form-label">วันที่สมัคร <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="birthDate" name="birthDate" required>
                </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="fw-semibold">Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control" placeholder="example@email.com">
    </div>

    <div class="col-md-6 mb-3">
        <label class="fw-semibold">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
        <input type="text" class="form-control" placeholder="08xxxxxxxx">
    </div>
</div>

<div class="mb-3">
    <label class="fw-semibold">ที่อยู่จัดส่ง <span class="text-danger">*</span></label>
    <textarea class="form-control" rows="3" placeholder="กรอกที่อยู่ของคุณ"></textarea>
</div>

<hr>

<div class="section-title">เปลี่ยนรหัสผ่าน</div>

<div class="mb-3">
    <label class="fw-semibold">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
    <input type="password" class="form-control" placeholder="กรอกรหัสผ่านใหม่">
</div>

<div class="mb-3">
    <label class="fw-semibold">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
    <input type="password" class="form-control" placeholder="กรอกรหัสผ่านอีกครั้ง">
</div>

<button type="submit" class="btn btn-orange w-100 mt-3">
    บันทึกการเปลี่ยนแปลง
</button>

</form>

</div>
</div>

<script>
// แสดงตัวอย่างรูปโปรไฟล์
document.getElementById("profileImage").addEventListener("change", function(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById("previewImage").src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
});

// แจ้งเตือนเมื่อกดบันทึก
document.getElementById("profileForm").addEventListener("submit", function(e){
    e.preventDefault();
    alert("บันทึกข้อมูลสำเร็จ (Demo)");
});
</script>

</body>
</html>
    