<?php
session_start();
include_once("check_login.php");
include_once("connectdb.php");
mysqli_set_charset($conn,"utf8");

/* =========================
   ดึงหมวดหมู่
========================= */
$result_category = mysqli_query($conn,
"SELECT * FROM category ORDER BY c_name ASC");

/* =========================
   ดึงแบรนด์
========================= */
$result_brand = mysqli_query($conn,
"SELECT * FROM brand ORDER BY brand_name ASC");

/* =========================
   สร้างโฟลเดอร์รูป
========================= */
$upload_dir = __DIR__."/FileUpload/";
if(!is_dir($upload_dir)){
    mkdir($upload_dir,0777,true);
}

/* =========================
   บันทึกสินค้า
========================= */
if(isset($_POST['save'])){

    $name   = mysqli_real_escape_string($conn,$_POST['p_name']);
    $price  = $_POST['p_price'];
    $qty    = $_POST['p_qty'];
    $type   = mysqli_real_escape_string($conn,$_POST['p_type']);
    $detail = mysqli_real_escape_string($conn,$_POST['p_detail']);
    $c_id   = $_POST['c_id'];
    $brand_id = $_POST['brand_id'];

    /* ===== SIZE ===== */
    $p_size = "";
    if(isset($_POST['p_size'])){
        $p_size = implode(",",$_POST['p_size']);
    }

    /* ===== อัปโหลดรูปหลายรูป ===== */
    $uploaded_files = [];

    if(isset($_FILES['p_img']) && !empty($_FILES['p_img']['name'][0])){

        foreach($_FILES['p_img']['name'] as $key => $val){

            if($_FILES['p_img']['error'][$key] === 0){

                $ext = strtolower(pathinfo($val,PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];

                if(in_array($ext,$allowed)){

                    $new_name = "product_".time()."_".uniqid().".".$ext;
                    $target = $upload_dir.$new_name;

                    if(move_uploaded_file($_FILES['p_img']['tmp_name'][$key],$target)){
                        $uploaded_files[] = "FileUpload/".$new_name;
                    }
                }
            }
        }
    }

    $p_img = implode(",",$uploaded_files);

    /* ===== INSERT ===== */
    $sql = "INSERT INTO products
            (p_name,p_price,p_qty,p_size,p_type,p_img,p_detail,c_id,brand_id)
            VALUES
            ('$name','$price','$qty','$p_size','$type','$p_img','$detail','$c_id','$brand_id')";

    if(mysqli_query($conn,$sql)){
        echo "<script>alert('เพิ่มสินค้าสำเร็จ');window.location='admin_product.php';</script>";
        exit();
    }else{
        echo mysqli_error($conn);
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
.card-box{
    background:#fff;
    border-radius:20px;
    padding:40px;
    box-shadow:0 15px 35px rgba(0,0,0,.08);
    border-top:5px solid #ff5722;
}
.btn-theme{
    background:#ff5722;
    color:#fff;
    border:none;
    border-radius:50px;
    padding:10px 25px;
}
.btn-theme:hover{background:#e64a19;}
.size-box{
    background:#f8f9fa;
    padding:20px;
    border-radius:12px;
    border:1px dashed #ccc;
}
</style>
</head>
<body>

<div class="container py-5">
<div class="card-box">

<h3 class="mb-4 text-center fw-bold">เพิ่มสินค้าใหม่</h3>

<form method="post" enctype="multipart/form-data">

<div class="mb-3">
<label>ชื่อสินค้า</label>
<input type="text" name="p_name" class="form-control" required>
</div>

<div class="row">
<div class="col-md-4 mb-3">
<label>ราคา</label>
<input type="number" name="p_price" class="form-control" required>
</div>

<div class="col-md-4 mb-3">
<label>จำนวน</label>
<input type="number" name="p_qty" class="form-control" required>
</div>

<div class="col-md-4 mb-3">
<label>หมวดหมู่</label>
<select name="c_id" class="form-select" required>
<option value="">-- เลือกหมวด --</option>
<?php while($c=mysqli_fetch_assoc($result_category)){ ?>
<option value="<?= $c['c_id']; ?>">
<?= $c['c_name']; ?>
</option>
<?php } ?>
</select>
</div>
</div>

<div class="mb-3">
<label>แบรนด์</label>
<select name="brand_id" class="form-select" required>
<option value="">-- เลือกแบรนด์ --</option>
<?php while($b=mysqli_fetch_assoc($result_brand)){ ?>
<option value="<?= $b['brand_id']; ?>">
<?= $b['brand_name']; ?>
</option>
<?php } ?>
</select>
</div>

<div class="mb-3">
<label>เพศ</label><br>
<input type="radio" name="p_type" value="male" required> ชาย
<input type="radio" name="p_type" value="female"> หญิง
<input type="radio" name="p_type" value="unisex"> Unisex
</div>

<div class="mb-3">
<label>ไซส์</label>
<div class="size-box">
<?php for($i=36;$i<=46;$i++){ ?>
<input type="checkbox" name="p_size[]" value="<?= $i ?>"> <?= $i ?>
<?php } ?>
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
