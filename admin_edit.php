<?php
// admin_edit.php
session_start();
include "data.php";

$id = $_GET['id'];

// ดึงข้อมูลสินค้าเดิม
$pro_query = mysqli_query($conn, "SELECT * FROM products WHERE p_id = '$id'");
$row = mysqli_fetch_assoc($pro_query);

// ดึงหมวดหมู่ทั้งหมด
$cat_query = mysqli_query($conn, "SELECT * FROM category");

if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = $_POST['p_price'];
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $c_id = $_POST['c_id'];
    $img = $_POST['p_img'];

    $sql = "UPDATE products SET 
            p_name = '$name',
            p_price = '$price',
            p_detail = '$detail',
            p_img = '$img',
            c_id = '$c_id'
            WHERE p_id = '$id'";
    
    if(mysqli_query($conn, $sql)){
        echo "<script>alert('แก้ไขข้อมูลสำเร็จ!'); window.location='admin_product.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">แก้ไขสินค้า ID: <?= $row['p_id']; ?></h4>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label>ชื่อสินค้า</label>
                                <input type="text" name="p_name" class="form-control" value="<?= $row['p_name']; ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>ราคา</label>
                                    <input type="number" name="p_price" class="form-control" value="<?= $row['p_price']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>หมวดหมู่</label>
                                    <select name="c_id" class="form-select" required>
                                        <?php while($c = mysqli_fetch_assoc($cat_query)){ ?>
                                            <option value="<?= $c['c_id']; ?>" <?= ($row['c_id'] == $c['c_id']) ? 'selected' : ''; ?>>
                                                <?= $c['c_name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>URL รูปภาพ</label>
                                <input type="text" name="p_img" class="form-control" value="<?= $row['p_img']; ?>">
                                <img src="<?= $row['p_img']; ?>" class="mt-2 rounded" width="80">
                            </div>
                            <div class="mb-3">
                                <label>รายละเอียดสินค้า</label>
                                <textarea name="p_detail" class="form-control" rows="3"><?= $row['p_detail']; ?></textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="update" class="btn btn-warning">อัปเดตข้อมูล</button>
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