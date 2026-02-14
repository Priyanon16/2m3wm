<?php
session_start();

// 1. เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "admin_man";    
$password = "66010914015";         
$dbname = "2m3wm";      

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");

// --------------------------------------------------------
// 2. ดึงข้อมูลหมวดหมู่ (Category) มารอไว้ก่อน
// --------------------------------------------------------
$sql_category = "SELECT * FROM category";
$result_category = mysqli_query($conn, $sql_category);
// --------------------------------------------------------

if (isset($_POST['save'])) {
    $name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = $_POST['p_price'];
    $type = mysqli_real_escape_string($conn, $_POST['p_type']);
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $img = mysqli_real_escape_string($conn, $_POST['p_img']);
    
    // รับค่า c_id จากที่เลือกใน Dropdown
    $c_id = $_POST['c_id']; 

    $sql = "INSERT INTO products (p_name, p_price, p_type, p_img, p_detail, c_id) 
            VALUES ('$name', '$price', '$type', '$img', '$detail', '$c_id')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ!'); window.location='admin_product.php';</script>";
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
            <div class="col-md-8">
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
                                <div class="col-md-4 mb-3">
                                    <label>ราคา (บาท)</label>
                                    <input type="number" name="p_price" class="form-control" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label>ประเภท (เพศ)</label>
                                    <select name="p_type" class="form-select" required>
                                        <option value="" selected disabled>-- เลือก --</option>
                                        <option value="male">ผู้ชาย</option>
                                        <option value="female">ผู้หญิง</option>
                                        <option value="unisex">Unisex</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>หมวดหมู่สินค้า</label>
                                    <select name="c_id" class="form-select" required>
                                        <option value="" selected disabled>-- เลือกหมวดหมู่ --</option>
                                        <?php 
                                        // วนลูปดึงข้อมูล category มาสร้างเป็นตัวเลือก
                                        if (mysqli_num_rows($result_category) > 0) {
                                            while($row_c = mysqli_fetch_assoc($result_category)) { 
                                        ?>
                                            <option value="<?php echo $row_c['c_id']; ?>">
                                                <?php echo $row_c['c_name']; ?> 
                                            </option>
                                        <?php 
                                            }
                                        } 
                                        ?>
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