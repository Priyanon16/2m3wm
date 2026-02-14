<?php
    include_once("check_login.php"); 
    include_once("connectdb.php");

    // --- ส่วนประมวลผล: เมื่อมีการกดปุ่ม Submit ---
    if (isset($_POST['Submit'])) {
        // รับค่าจากฟอร์มและป้องกัน SQL Injection
        // ใช้ชื่อคอลัมน์ c_name และ c_details ตามฐานข้อมูลของคุณ
        $name = mysqli_real_escape_string($conn, $_POST['c_name']);
        $details = mysqli_real_escape_string($conn, $_POST['c_details']);

        $sql = "INSERT INTO category (c_name, c_details) VALUES ('$name', '$details')";

        if (mysqli_query($conn, $sql)) {
            // บันทึกสำเร็จ ให้เด้งกลับหน้าหลักพร้อมส่งค่า success=add
            echo "<script>
                alert('บันทึกหมวดหมู่ใหม่สำเร็จ!');
                window.location.href='category_products.php?success=add';
            </script>";
            exit;
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . mysqli_error($conn) . "');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มหมวดหมู่สินค้า - 2M3WM ADMIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f8f9fa; }
        .card-add { background: white; border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: 50px; }
        .btn-orange { background: #ff5722; color: white; border: none; border-radius: 10px; padding: 10px 25px; }
        .btn-orange:hover { background: #e64a19; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-add">
                <div class="card-header bg-dark text-white p-3 fw-bold text-center">
                    เพิ่มหมวดหมู่สินค้าใหม่
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อหมวดหมู่</label>
                            <input type="text" name="c_name" class="form-control" placeholder="เช่น Running, Lifestyle" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">รายละเอียด</label>
                            <textarea name="c_details" class="form-control" rows="4" placeholder="ระบุรายละเอียดเพิ่มเติม..."></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="category_products.php" class="btn btn-light">ยกเลิก</a>
                            <button type="submit" name="Submit" class="btn btn-orange">บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>