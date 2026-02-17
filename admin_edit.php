<?php
session_start();
include_once("check_login.php");
include_once("connectdb.php");
mysqli_set_charset($conn, "utf8");

if (!isset($_GET['id'])) { header("Location: admin_product.php"); exit(); }
$id = intval($_GET['id']);

/* 1. ดึงข้อมูลสินค้า */
$product_query = mysqli_query($conn, "SELECT * FROM products WHERE p_id=$id");
$row = mysqli_fetch_assoc($product_query);
if (!$row) { header("Location: admin_product.php"); exit(); }

/* 2. ดึงข้อมูลสต็อกรายไซส์ */
$stock_map = []; // เก็บค่าไซส์และจำนวน เช่น [39=>5, 45=>5]
$stock_rs = mysqli_query($conn, "SELECT * FROM product_stock WHERE p_id=$id");
while($s = mysqli_fetch_assoc($stock_rs)){
    $stock_map[ $s['p_size'] ] = $s['p_qty_stock'];
}

/* 3. ดึงหมวดหมู่/แบรนด์ */
$cat_query   = mysqli_query($conn, "SELECT * FROM category ORDER BY c_name ASC");
$brand_query = mysqli_query($conn, "SELECT * FROM brand ORDER BY brand_name ASC");

$upload_dir = __DIR__ . "/uploads/products/";

/* 4. ลบรูป (เหมือนเดิม) */
if (isset($_GET['delete_img'])) {
    $img_id = intval($_GET['delete_img']);
    $img_rs = mysqli_query($conn, "SELECT img_path FROM product_images WHERE img_id=$img_id");
    $img = mysqli_fetch_assoc($img_rs);
    if ($img) {
        $file_path = __DIR__ . "/" . $img['img_path'];
        if (file_exists($file_path)) unlink($file_path);
        mysqli_query($conn, "DELETE FROM product_images WHERE img_id=$img_id");
    }
    header("Location: admin_edit.php?id=" . $id);
    exit();
}

/* 5. UPDATE ข้อมูล */
if (isset($_POST['update'])) {
    $name   = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price  = floatval($_POST['p_price']);
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $c_id   = intval($_POST['c_id']);
    $brand_id = intval($_POST['brand_id']);
    $type   = mysqli_real_escape_string($conn, $_POST['p_type']);

    // --- จัดการสต็อก ---
    $stocks = $_POST['stock_qty'] ?? [];
    $total_qty = 0;
    $available_sizes = [];

    // ล้างสต็อกเก่าทิ้งก่อน (เพื่อลงใหม่)
    mysqli_query($conn, "DELETE FROM product_stock WHERE p_id=$id");

    foreach($stocks as $size => $qty){
        $qty = intval($qty);
        if($qty > 0){
            // Insert ใหม่
            mysqli_query($conn, "INSERT INTO product_stock (p_id, p_size, p_qty_stock) VALUES ($id, '$size', $qty)");
            $total_qty += $qty;
            $available_sizes[] = $size;
        }
    }
    
    // String รายชื่อไซส์ (สำหรับ Frontend)
    $p_size_str = implode(",", $available_sizes);

    // อัปเดตตารางหลัก
    mysqli_query($conn, "
        UPDATE products SET
        p_name='$name', p_price='$price',
        p_qty='$total_qty', p_size='$p_size_str',
        p_type='$type', p_detail='$detail',
        c_id='$c_id', brand_id='$brand_id'
        WHERE p_id=$id
    ");

    /* เพิ่มรูปใหม่ (เหมือนเดิม) */
    if (isset($_FILES['p_img']) && !empty($_FILES['p_img']['name'][0])) {
        foreach ($_FILES['p_img']['name'] as $key => $val) {
            if ($_FILES['p_img']['error'][$key] === 0) {
                $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($ext, $allowed)) {
                    $new_name = "product_" . time() . "_" . uniqid() . "." . $ext;
                    $target_path = $upload_dir . $new_name;
                    if (move_uploaded_file($_FILES['p_img']['tmp_name'][$key], $target_path)) {
                        $db_path = "uploads/products/" . $new_name;
                        mysqli_query($conn, "INSERT INTO product_images (p_id,img_path) VALUES ($id,'$db_path')");
                    }
                }
            }
        }
    }
    echo "<script>alert('อัปเดตข้อมูลและสต็อกเรียบร้อย');window.location='admin_product.php';</script>";
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
body { font-family: 'Kanit', sans-serif; background: #f8f9fa; }
.stock-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; }
.stock-item { background: #fff; border: 1px solid #ced4da; border-radius: 8px; padding: 5px; text-align: center; }
.stock-item input { text-align: center; }
.btn-theme { background: #ff5722; color: #fff; border: none; }
.btn-theme:hover { background: #e64a19; }
</style>
</head>
<body>

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-lg-9">
<div class="card shadow border-0">
    <div class="card-header bg-dark text-white">
        <h4 class="m-0">แก้ไขสินค้า ID: <?= $row['p_id']; ?></h4>
    </div>
    <div class="card-body p-4">

    <form method="post" enctype="multipart/form-data">

        <div class="mb-3">
            <label>ชื่อสินค้า</label>
            <input type="text" name="p_name" class="form-control" value="<?= $row['p_name']; ?>" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>ราคา</label>
                <input type="number" name="p_price" class="form-control" value="<?= $row['p_price']; ?>" required>
            </div>
            </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>หมวดหมู่</label>
                <select name="c_id" class="form-select">
                    <?php while ($c = mysqli_fetch_assoc($cat_query)): ?>
                        <option value="<?= $c['c_id']; ?>" <?= ($row['c_id'] == $c['c_id']) ? 'selected' : ''; ?>>
                            <?= $c['c_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>แบรนด์</label>
                <select name="brand_id" class="form-select">
                    <?php while ($b = mysqli_fetch_assoc($brand_query)): ?>
                        <option value="<?= $b['brand_id']; ?>" <?= ($row['brand_id'] == $b['brand_id']) ? 'selected' : ''; ?>>
                            <?= $b['brand_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label>เพศ</label><br>
            <input type="radio" name="p_type" value="male" <?= ($row['p_type'] == 'male') ? 'checked' : ''; ?>> ชาย
            <input type="radio" name="p_type" value="female" <?= ($row['p_type'] == 'female') ? 'checked' : ''; ?>> หญิง
            <input type="radio" name="p_type" value="unisex" <?= ($row['p_type'] == 'unisex') ? 'checked' : ''; ?>> Unisex
        </div>

        <div class="card bg-light p-3 mb-4">
            <label class="fw-bold mb-2">แก้ไขจำนวนสินค้าแต่ละไซส์</label>
            <div class="stock-grid">
                <?php for ($i = 36; $i <= 46; $i++): ?>
                    <?php 
                        // ดึงค่าเก่ามาแสดง (ถ้าไม่มีให้เป็นค่าว่าง)
                        $val = isset($stock_map[$i]) ? $stock_map[$i] : ''; 
                    ?>
                    <div class="stock-item">
                        <label class="small text-muted">Size <?= $i ?></label>
                        <input type="number" 
                               name="stock_qty[<?= $i ?>]" 
                               class="form-control form-control-sm" 
                               value="<?= $val ?>" 
                               min="0" placeholder="0">
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <hr>
        <h5>รูปภาพปัจจุบัน</h5>
        <div class="d-flex flex-wrap mb-3">
            <?php
            $imgs = mysqli_query($conn, "SELECT * FROM product_images WHERE p_id=$id");
            while ($img = mysqli_fetch_assoc($imgs)) { ?>
                <div class="text-center me-3 mb-3">
                    <img src="<?= $img['img_path']; ?>" width="100" class="rounded border">
                    <br>
                    <a href="?id=<?= $id ?>&delete_img=<?= $img['img_id']; ?>"
                       class="btn btn-sm btn-outline-danger mt-1"
                       onclick="return confirm('ลบรูปนี้?')">ลบ</a>
                </div>
            <?php } ?>
        </div>

        <div class="mb-3">
            <label>เพิ่มรูปใหม่</label>
            <input type="file" name="p_img[]" class="form-control" multiple>
        </div>

        <div class="mb-3">
            <label>รายละเอียด</label>
            <textarea name="p_detail" class="form-control" rows="4"><?= $row['p_detail']; ?></textarea>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" name="update" class="btn btn-theme">บันทึกการแก้ไข</button>
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