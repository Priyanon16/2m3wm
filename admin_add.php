<?php
// admin_add.php
session_start();
include "data.php";

// ดึงหมวดหมู่มาแสดงใน Select Box
$cat_query = mysqli_query($conn, "SELECT * FROM category");

if(isset($_POST['save'])){
    $name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = $_POST['p_price'];
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $c_id = $_POST['c_id'];
    $img = $_POST['p_img']; // รับเป็น URL (หากต้องการอัปโหลดไฟล์ต้องแก้ส่วนนี้เพิ่ม)

    $sql = "INSERT INTO products (p_name, p_price, p_detail, p_img, c_id) 
            VALUES ('$name', '$price', '$detail', '$img', '$c_id')";
    
    if(mysqli_query($conn, $sql)){
        echo "<script>alert('เพิ่มสินค้าสำเร็จ!'); window.location='admin_product.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
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
                                    <label>หมวดหมู่</label>
                                    <select name="c_id" class="form-select" required>
                                        <option value="">-- เลือกหมวดหมู่ --</option>
                                        <?php while($c = mysqli_fetch_assoc($cat_query)){ ?>
                                            <option value="<?= $c['c_id']; ?>"><?= $c['c_name']; ?></option>
                                        <?php } ?>
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