<?php
session_start();
include_once("check_login.php");
include_once("connectdb.php");

if (!$conn) {
    die("Connect Failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");

// ดึงหมวดหมู่
$result_category = mysqli_query($conn, "SELECT * FROM category");

if (isset($_POST['save'])) {
    $name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = $_POST['p_price'];
    $type = mysqli_real_escape_string($conn, $_POST['p_type']);
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $c_id = $_POST['c_id'];

    // ==================================================================
    // 👟 1. จัดการเรื่องไซส์ (SIZE) - เพิ่มใหม่
    // ==================================================================
    $p_size = "";
    if (isset($_POST['p_size'])) {
        // แปลง Array ที่ติ๊กเลือกหลายๆ อัน ให้เป็นข้อความเดียวคั่นด้วยลูกน้ำ
        // เช่น เลือก 38, 39, 40 -> จะเก็บเป็น "38,39,40"
        $p_size = implode(",", $_POST['p_size']);
    }
    // ==================================================================

    // ==================================================================
    // 📷 2. จัดการรูปภาพ (โค้ดเดิม)
    // ==================================================================
    $p_img = "";
    if (isset($_FILES['p_img']) && $_FILES['p_img']['name'] != "") {
        $ext = pathinfo($_FILES['p_img']['name'], PATHINFO_EXTENSION);
        $new_name = "product_" . uniqid() . "." . $ext;
        $upload_path = "FileUpload/" . $new_name;
        $allowed = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array(strtolower($ext), $allowed)) {
            if (move_uploaded_file($_FILES['p_img']['tmp_name'], $upload_path)) {
                $p_img = $upload_path;
            }
        }
    }

    // ==================================================================
    // 💾 3. บันทึกข้อมูล (เพิ่ม p_size ลง SQL)
    // ==================================================================
    $sql = "INSERT INTO products (p_name, p_price, p_size, p_type, p_img, p_detail, c_id) 
            VALUES ('$name', '$price', '$p_size', '$type', '$p_img', '$detail', '$c_id')";

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

                        <form method="post" enctype="multipart/form-data">

                            <div class="mb-3">
                                <label>ชื่อสินค้า</label>
                                <input type="text" name="p_name" class="form-control" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>ราคา (บาท)</label>
                                    <input type="number" name="p_price" class="form-control" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">เลือกไซส์ที่พร้อมส่ง:</label>
                                    <div class="card p-3 bg-light">
                                        <div class="row">
                                            <?php
                                            // วนลูปสร้าง Checkbox เบอร์ 36-45 (แก้เลขได้ตามต้องการ)
                                            for ($i = 36; $i <= 45; $i++) {
                                            ?>
                                                <div class="col-3 col-md-2 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="p_size[]" value="<?= $i; ?>" id="size<?= $i; ?>">
                                                        <label class="form-check-label" for="size<?= $i; ?>">
                                                            EU <?= $i; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>ประเภท (เพศ)</label>
                                    <select name="p_type" class="form-select" required>
                                        <option value="" selected disabled>-- เลือก --</option>
                                        <option value="male">ผู้ชาย</option>
                                        <option value="female">ผู้หญิง</option>
                                        <option value="unisex">Unisex</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>หมวดหมู่สินค้า</label>
                                    <select name="c_id" class="form-select" required>
                                        <option value="" selected disabled>-- เลือกหมวดหมู่ --</option>
                                        <?php while ($row_c = mysqli_fetch_assoc($result_category)) { ?>
                                            <option value="<?= $row_c['c_id']; ?>"><?= $row_c['c_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>รูปภาพสินค้า</label>
                                <input type="file" name="p_img" class="form-control" accept="image/*" required>
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