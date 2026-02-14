<?php
include_once("check_login.php"); 
include_once("connectdb.php"); 

// 1. รับค่า ID จาก URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // 2. ดึงข้อมูลเดิมมาแสดงในฟอร์ม
    $sql = "SELECT * FROM category WHERE c_id = '$id'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_array($result);

    if (!$data) {
        echo "<script>alert('ไม่พบข้อมูลหมวดหมู่นี้'); window.location='category_products.php';</script>";
        exit;
    }
} else {
    header("Location: category_products.php");
    exit;
}

// 3. ประมวลผลเมื่อกดปุ่มอัปเดต (Update)
if (isset($_POST['Submit_Update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['c_name']);
    $details = mysqli_real_escape_string($conn, $_POST['c_details']);
    
    // สั่ง Update ข้อมูลกลับเข้า Database
    $sql_up = "UPDATE category SET c_name='$name', c_details='$details' WHERE c_id='$id'";
    
    if (mysqli_query($conn, $sql_up)) {
        echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location='category_products.php?success=edit';</script>";
        exit;
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดต: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขหมวดหมู่ - 2M3WM ADMIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f8f9fa; }
        .card-edit { background: white; border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: 50px; }
        .btn-orange { background: #ff5722; color: white; border: none; border-radius: 10px; padding: 10px 25px; transition: 0.3s; }
        .btn-orange:hover { background: #e64a19; transform: translateY(-2px); }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-edit overflow-hidden">
                <div class="card-header bg-dark text-white p-3 fw-bold text-center border-0">
                    แก้ไขหมวดหมู่สินค้า
                </div>
                <div class="card-body p-4 p-lg-5">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อหมวดหมู่</label>
                            <input type="text" name="c_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($data['c_name']); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">รายละเอียด</label>
                            <textarea name="c_details" class="form-control" rows="4"><?php echo htmlspecialchars($data['c_details']); ?></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="category_products.php" class="btn btn-light px-4">ยกเลิก</a>
                            <button type="submit" name="Submit_Update" class="btn btn-orange">
                                บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>