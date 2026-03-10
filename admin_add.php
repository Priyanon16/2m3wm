<?php
session_start();
include_once("check_login.php");
include_once("connectdb.php");

mysqli_set_charset($conn,"utf8");

/* =========================
   ดึงหมวดหมู่ / แบรนด์
========================= */
$result_category = mysqli_query($conn, "SELECT * FROM category ORDER BY c_name ASC");
$result_brand = mysqli_query($conn, "SELECT * FROM brand ORDER BY brand_name ASC");

/* =========================
   สร้างโฟลเดอร์
========================= */
$upload_dir = __DIR__ . "/uploads/products/";
if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0755, true);
}

/* =========================
   บันทึกสินค้า
========================= */
if(isset($_POST['save'])){

    $name   = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price  = floatval($_POST['p_price']);
    $type   = mysqli_real_escape_string($conn, $_POST['p_type']);
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $c_id   = intval($_POST['c_id']);
    $brand_id = intval($_POST['brand_id']);

    // รับค่า Array ของ Stock ที่กรอกมา (key=size, value=qty)
    $stocks = $_POST['stock_qty'] ?? [];

    // คำนวณผลรวมทั้งหมด และ สร้าง string ไซส์ เพื่อบันทึกในตารางหลัก
    $total_qty = 0;
    $available_sizes = [];

    foreach($stocks as $size => $qty){
        $qty = intval($qty);
        if($qty > 0){
            $total_qty += $qty;
            $available_sizes[] = $size;
        }
    }
    
    // แปลง array ไซส์ เป็น string (เช่น "39,40,41") เพื่อให้ frontend เดิมทำงานได้
    $p_size_str = implode(",", $available_sizes);

    /* ===== 1️⃣ INSERT ลงตารางหลัก (products) ===== */
    $sql = "INSERT INTO products
            (p_name, p_price, p_qty, p_size, p_type, p_detail, c_id, brand_id)
            VALUES
            ('$name', '$price', '$total_qty', '$p_size_str', '$type', '$detail', '$c_id', '$brand_id')";

    if(mysqli_query($conn, $sql)){

        $new_product_id = mysqli_insert_id($conn);

        /* ===== 2️⃣ INSERT ลงตารางสต็อกแยกไซส์ (product_stock) ===== */
        foreach($stocks as $size => $qty){
            $qty = intval($qty);
            if($qty > 0){
                $s_sql = "INSERT INTO product_stock (p_id, p_size, p_qty_stock) 
                          VALUES ($new_product_id, '$size', $qty)";
                mysqli_query($conn, $s_sql);
            }
        }

        /* ===== 3️⃣ อัปโหลดรูปภาพ ===== */
        if(isset($_FILES['p_img']) && !empty($_FILES['p_img']['name'][0])){
            foreach($_FILES['p_img']['name'] as $key => $val){
                if($_FILES['p_img']['error'][$key] === 0){
                    $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
                    $allowed = ['jpg','jpeg','png','gif','webp'];

                    if(in_array($ext, $allowed)){
                        $new_name = "product_" . time() . "_" . uniqid() . "." . $ext;
                        $target = $upload_dir . $new_name;

                        if(move_uploaded_file($_FILES['p_img']['tmp_name'][$key], $target)){
                            $img_path = "uploads/products/" . $new_name;
                            mysqli_query($conn,"INSERT INTO product_images (p_id, img_path) VALUES ('$new_product_id', '$img_path')");
                        }
                    }
                }
            }
        }

        echo "<script>alert('เพิ่มสินค้าและสต็อกเรียบร้อย'); window.location='admin_product.php';</script>";
        exit();

    } else {
        echo "<div style='color:red;'>Error: ".mysqli_error($conn)."</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เพิ่มสินค้าใหม่</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Kanit',sans-serif;background:#f4f6f9;}
.card-box{background:#fff;border-radius:20px;padding:40px;box-shadow:0 15px 35px rgba(0,0,0,.08);border-top:5px solid #ff5722;}
.btn-theme{background:#ff5722;color:#fff;border:none;border-radius:50px;padding:10px 25px;}
.btn-theme:hover{background:#e64a19;}
/* CSS สำหรับตารางไซส์ */
.stock-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 10px;
}
.stock-item {
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 5px;
    text-align: center;
}
.stock-item label { font-weight: bold; display: block; margin-bottom: 2px; }
.stock-item input { text-align: center; font-size: 14px; }
</style>
</head>
<body>

<div class="container py-5">
<div class="card-box">
<h3 class="mb-4 text-center fw-bold">เพิ่มสินค้าใหม่ (ระบุจำนวนตามไซส์)</h3>

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
        </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label>หมวดหมู่</label>
            <select name="c_id" class="form-select" required>
                <option value="">-- เลือกหมวด --</option>
                <?php while($c=mysqli_fetch_assoc($result_category)){ ?>
                <option value="<?= $c['c_id']; ?>"><?= $c['c_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label>แบรนด์</label>
            <select name="brand_id" class="form-select" required>
                <option value="">-- เลือกแบรนด์ --</option>
                <?php while($b=mysqli_fetch_assoc($result_brand)){ ?>
                <option value="<?= $b['brand_id']; ?>"><?= $b['brand_name']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label>เพศ</label><br>
        <input type="radio" name="p_type" value="male" required> ชาย
        <input type="radio" name="p_type" value="female"> หญิง
        <input type="radio" name="p_type" value="unisex"> Unisex
    </div>

    <div class="card p-3 bg-light mb-3">
        <label class="fw-bold mb-2">กำหนดจำนวนสินค้า ในแต่ละไซส์</label>
        <p class="small text-muted mb-2">* กรอกจำนวนเฉพาะไซส์ที่มี (ถ้าไม่มีให้เว้นว่างหรือใส่ 0)</p>
        
        <div class="stock-grid">
            <?php for($i=36; $i<=46; $i++): ?>
            <div class="stock-item">
                <label>Size <?= $i ?></label>
                <input type="number" name="stock_qty[<?= $i ?>]" class="form-control form-control-sm" min="0" placeholder="0">
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="mb-3">
        <label>รูปภาพ (หลายรูปได้)</label>
        <input type="file" name="p_img[]" class="form-control" accept="image/*" multiple required>
    </div>

    <div class="mb-4">
        <label>รายละเอียด</label>
        <textarea name="p_detail" class="form-control" rows="4"></textarea>
    </div>

    <div class="text-center">
        <button type="submit" name="save" class="btn btn-theme">
            บันทึกสินค้า
        </button>
    </div>

</form>
</div>
</div>
</body>
</html>