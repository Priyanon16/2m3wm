<?php
// admin_add.php
session_start();

// --- ส่วนเชื่อมต่อฐานข้อมูล (แทนไฟล์ data.php) ---
// แก้ไข username และ password ให้ตรงกับ Server ของคุณนะครับ
$servername = "localhost";
$username = "admin_man";     // ใส่ username ของ database (ถ้าใช้ XAMPP ปกติคือ root)
$password = "66010914015";         // ใส่ password ของ database (ถ้าใช้ XAMPP ปกติคือว่างไว้)
$dbname = "2m3wm";      // ชื่อฐานข้อมูลของคุณ (ดูจากรูปที่คุณส่งมา)

$conn = mysqli_connect($servername, $username, $password, $dbname);

// เช็คการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// ------------------------------------------------

if(isset($_POST['save'])){
    $name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = $_POST['p_price'];
    
    // รับค่าประเภท (ชาย/หญิง)
    $type = mysqli_real_escape_string($conn, $_POST['p_type']); 
    
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $img = $_POST['p_img'];

    // --- จุดสำคัญ ---
    // กำหนดค่า c_id เป็น 1 (เพื่อให้ Database ยอมบันทึก เพราะห้ามเป็นค่าว่าง)
    $c_id = 1; 

    // เพิ่ม c_id กลับเข้าไปในคำสั่ง SQL
    $sql = "INSERT INTO products (p_name, p_price, p_type, p_detail, p_img, c_id) 
            VALUES ('$name', '$price', '$type', '$detail', '$img', '$c_id')";
    
    if(mysqli_query($conn, $sql)){
        echo "<script>alert('เพิ่มสินค้าสำเร็จ!'); window.location='admin_product.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้าใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">เพิ่มสินค้าใหม่</h4>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label>ชื่อสินค้า</label>
                                <input type="text" name="p_name" class="form-control" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>ราคา (บาท)</label>
                                    <input type="number" name="p_price" class="form-control" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label>ประเภทสินค้า</label>
                                    <select name="p_type" class="form-select" required>
                                        <option value="" selected disabled>-- เลือกประเภท --</option>
                                        <option value="male">ผู้ชาย</option>
                                        <option value="female">ผู้หญิง</option>
                                        <option value="unisex">Unisex</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>URL รูปภาพ</label>
                                <input type="text" name="p_img" class="form-control" placeholder="https://...">
                            </div>
                            
                            <div class="mb-3">
                                <label>รายละเอียดสินค้า</label>
                                <textarea name="p_detail" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="save" class="btn btn-success">บันทึกข้อมูล</button>
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