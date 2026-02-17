<?php
session_start();
include_once("check_login.php");
include_once("connectdb.php");
mysqli_set_charset($conn, "utf8");

if (!isset($_GET['id'])) {
    header("Location: admin_product.php");
    exit();
}

$id = intval($_GET['id']);

/* =========================
   ดึงข้อมูลสินค้า
========================= */
$product_query = mysqli_query($conn, "SELECT * FROM products WHERE p_id=$id");
$row = mysqli_fetch_assoc($product_query);

if (!$row) {
    header("Location: admin_product.php");
    exit();
}

/* =========================
   ดึงหมวดหมู่ / แบรนด์
========================= */
$cat_query   = mysqli_query($conn, "SELECT * FROM category ORDER BY c_name ASC");
$brand_query = mysqli_query($conn, "SELECT * FROM brand ORDER BY brand_name ASC");

/* =========================
   โฟลเดอร์อัปโหลด (สำคัญมาก)
========================= */
$upload_dir = __DIR__ . "/uploads/products/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

/* =========================
   ลบรูป
========================= */
if (isset($_GET['delete_img'])) {
    $img_id = intval($_GET['delete_img']);

    $img_rs = mysqli_query($conn, "SELECT img_path FROM product_images WHERE img_id=$img_id");
    $img = mysqli_fetch_assoc($img_rs);

    if ($img) {
        $file_path = __DIR__ . "/" . $img['img_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        mysqli_query($conn, "DELETE FROM product_images WHERE img_id=$img_id");
    }

    header("Location: admin_edit.php?id=" . $id);
    exit();
}

/* =========================
   UPDATE ข้อมูลสินค้า
========================= */
if (isset($_POST['update'])) {

    $name   = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price  = floatval($_POST['p_price']);
    $qty    = intval($_POST['p_qty']);
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $c_id   = intval($_POST['c_id']);
    $brand_id = intval($_POST['brand_id']);
    $type   = mysqli_real_escape_string($conn, $_POST['p_type']);

    $p_size = "";
    if (isset($_POST['p_size'])) {
        $p_size = implode(",", $_POST['p_size']);
    }

    mysqli_query($conn, "
        UPDATE products SET
        p_name='$name',
        p_price='$price',
        p_qty='$qty',
        p_size='$p_size',
        p_type='$type',
        p_detail='$detail',
        c_id='$c_id',
        brand_id='$brand_id'
        WHERE p_id=$id
    ");

    /* ===== เพิ่มรูปใหม่หลายรูป ===== */
    if (isset($_FILES['p_img']) && !empty($_FILES['p_img']['name'][0])) {

        foreach ($_FILES['p_img']['name'] as $key => $val) {

            if ($_FILES['p_img']['error'][$key] === 0) {

                $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($ext, $allowed)) {

                    $new_name = "product_" . time() . "_" . uniqid() . "." . $ext;
                    $target_path = $upload_dir . $new_name;

                    if (move_uploaded_file($_FILES['p_img']['tmp_name'][$key], $target_path)) {

                        // เก็บ path ลง DB
                        $db_path = "uploads/products/" . $new_name;

                        mysqli_query($conn, "
                            INSERT INTO product_images (p_id,img_path)
                            VALUES ($id,'$db_path')
                        ");
                    }
                }
            }
        }
    }

    echo "<script>alert('อัปเดตสำเร็จ');window.location='admin_product.php';</script>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>แก้ไขสินค้า</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: #f8f9fa;
        }

        .card {
            border-radius: 18px;
            border: none;
        }

        .img-thumb {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            margin: 5px;
        }

        .btn-theme {
            background: #ff5722;
            color: #fff;
            border: none;
        }

        .btn-theme:hover {
            background: #e64a19;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">

                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h4>แก้ไขสินค้า ID: <?= $row['p_id']; ?></h4>
                    </div>

                    <div class="card-body">

                        <form method="post" enctype="multipart/form-data">

                            <div class="mb-3">
                                <label>ชื่อสินค้า</label>
                                <input type="text" name="p_name" class="form-control"
                                    value="<?= $row['p_name']; ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>ราคา</label>
                                    <input type="number" name="p_price" class="form-control"
                                        value="<?= $row['p_price']; ?>" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>จำนวน</label>
                                    <input type="number" name="p_qty" class="form-control"
                                        value="<?= $row['p_qty']; ?>" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>หมวดหมู่</label>
                                    <select name="c_id" class="form-select">
                                        <?php while ($c = mysqli_fetch_assoc($cat_query)): ?>
                                            <option value="<?= $c['c_id']; ?>"
                                                <?= ($row['c_id'] == $c['c_id']) ? 'selected' : ''; ?>>
                                                <?= $c['c_name']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>แบรนด์</label>
                                <select name="brand_id" class="form-select">
                                    <?php while ($b = mysqli_fetch_assoc($brand_query)): ?>
                                        <option value="<?= $b['brand_id']; ?>"
                                            <?= ($row['brand_id'] == $b['brand_id']) ? 'selected' : ''; ?>>
                                            <?= $b['brand_name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>เพศ</label><br>
                                <input type="radio" name="p_type" value="male"
                                    <?= ($row['p_type'] == 'male') ? 'checked' : ''; ?>> ชาย
                                <input type="radio" name="p_type" value="female"
                                    <?= ($row['p_type'] == 'female') ? 'checked' : ''; ?>> หญิง
                                <input type="radio" name="p_type" value="unisex"
                                    <?= ($row['p_type'] == 'unisex') ? 'checked' : ''; ?>> Unisex
                            </div>

                            <div class="mb-3">
                                <label class="fw-semibold">ไซส์ที่มี</label>

                                <?php
                                $current_sizes = [];
                                if (!empty($row['p_size'])) {
                                    $current_sizes = explode(",", $row['p_size']);
                                }
                                ?>

                                <div class="d-flex flex-wrap gap-3 mt-2">

                                    <?php for ($i = 36; $i <= 46; $i++): ?>
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                type="checkbox"
                                                name="p_size[]"
                                                value="<?= $i ?>"
                                                id="size<?= $i ?>"
                                                <?= in_array($i, $current_sizes) ? 'checked' : '' ?>>

                                            <label class="form-check-label"
                                                for="size<?= $i ?>">
                                                <?= $i ?>
                                            </label>
                                        </div>
                                    <?php endfor; ?>

                                </div>
                            </div>


                            <hr>

                            <h5>รูปปัจจุบัน</h5>
                            <div class="d-flex flex-wrap">

                                <?php
                                $imgs = mysqli_query($conn, "SELECT * FROM product_images WHERE p_id=$id");

                                while ($img = mysqli_fetch_assoc($imgs)) {
                                ?>

                                    <div class="text-center me-3 mb-3">
                                        <img src="<?= $img['img_path']; ?>" width="120" class="rounded">
                                        <br>
                                        <a href="?id=<?= $id ?>&delete_img=<?= $img['img_id']; ?>"
                                            class="btn btn-sm btn-danger mt-1"
                                            onclick="return confirm('ลบรูปนี้?')">
                                            ลบ
                                        </a>
                                    </div>

                                <?php } ?>
                            </div>


                            <hr>

                            <div class="mb-3">
                                <label>เพิ่มรูปใหม่ (หลายรูปได้)</label>
                                <input type="file" name="p_img[]" class="form-control" multiple>
                            </div>

                            <div class="mb-3">
                                <label>รายละเอียด</label>
                                <textarea name="p_detail" class="form-control" rows="4"><?= $row['p_detail']; ?></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="update" class="btn btn-theme">
                                    บันทึกการแก้ไข
                                </button>
                                <a href="admin_product.php" class="btn btn-secondary">
                                    ยกเลิก
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>