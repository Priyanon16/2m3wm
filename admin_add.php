<?php
session_start();

// ==========================================
// ส่วนตั้งค่าการเชื่อมต่อฐานข้อมูล (สำคัญมาก!)
// ==========================================
$servername = "localhost";
$username = "root";     // ⚠️ ถ้าขึ้น Server จริง ต้องแก้เป็น user ที่โฮสต์ให้มา
$password = "";         // ⚠️ ถ้าขึ้น Server จริง ต้องแก้เป็นรหัสผ่านที่โฮสต์ให้มา
$dbname = "2m3wm";      // ชื่อฐานข้อมูล (ต้องตรงเป๊ะๆ)

// เชื่อมต่อฐานข้อมูล
$conn = mysqli_connect($servername, $username, $password, $dbname);

// เช็คว่าเชื่อมต่อติดไหม
if (!$conn) {
    die("<div class='alert alert-danger'>เชื่อมต่อฐานข้อมูลล้มเหลว: " . mysqli_connect_error() . "</div>");
}
// ตั้งค่าภาษาไทย
mysqli_set_charset($conn, "utf8");

// ==========================================
// ส่วนบันทึกข้อมูลเมื่อกดปุ่ม Save
// ==========================================
if (isset($_POST['save'])) {
    
    // รับค่าจากฟอร์ม
    $name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = $_POST['p_price'];
    $type = mysqli_real_escape_string($conn, $_POST['p_type']);
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $img = mysqli_real_escape_string($conn, $_POST['p_img']);

    // 🔴 จุดสำคัญ: กำหนด c_id (หมวดหมู่) อัตโนมัติ
    // เพราะตาราง products บังคับว่า c_id ห้ามว่าง (Not Null)
    // เราจึงต้องใส่เลข 1 ลงไปก่อน (สมมติว่าเป็นหมวดหมู่แรก)
    $c_id = 1; 

    // คำสั่ง SQL สำหรับบันทึก (เรียงตามตารางเป๊ะๆ)
    $sql = "INSERT INTO products (p_name, p_price, p_type, p_img, p_detail, c_id) 
            VALUES ('$name', '$price', '$type', '$img', '$detail', '$c_id')";
    
    // สั่งรันคำสั่ง SQL
    if (mysqli_query($conn, $sql)) {
        // ถ้าสำเร็จ ให้แจ้งเตือนและกลับไปหน้าแสดงสินค้า
        echo "<script>
                alert('บันทึกข้อมูลสำเร็จ!');
                window.location='admin_product.php';
              </script>";
    } else {
        // ถ้าไม่สำเร็จ ให้แสดง Error ตัวแดงๆ ออกมาดูว่าผิดตรงไหน
        echo "<div class='alert alert-danger mt-3'>
                <strong>เกิดข้อผิดพลาด!</strong> ไม่สามารถบันทึกข้อมูลได้<br>
                สาเหตุ: " . mysqli_error($conn) . "
              </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้าใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 15px; overflow: hidden; }
        .card-header { background-color: #198754; color: white; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header text-center py-3">
                        <h4 class="mb-0">เพิ่มสินค้าใหม่</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">ชื่อสินค้า</label>
                                <input type="text" name="p_name" class="form-control" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ราคา (บาท)</label>
                                    <input type="number" name="p_price" class="form-control" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ประเภทสินค้า</label>
                                    <select name="p_type" class="form-select" required>
                                        <option value="" selected disabled>-- เลือกประเภท --</option>
                                        <option value="male">ผู้ชาย</option>
                                        <option value="female">ผู้หญิง</option>
                                        <option value="unisex">Unisex</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">URL รูปภาพ</label>
                                <input type="text" name="p_img" class="form-control" placeholder="https://example.com/image.jpg">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">รายละเอียดสินค้า</label>
                                <textarea name="p_detail" class="form-control" rows="4"></textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="save" class="btn btn-success btn-lg">บันทึกข้อมูล</button>
                                <a href="admin_product.php" class="btn btn-secondary">ยกเลิก</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>