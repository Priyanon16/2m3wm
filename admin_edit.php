<?php
session_start();
include_once("check_login.php");
include_once("connectdb.php");

mysqli_set_charset($conn,"utf8");

// รับ id
if(!isset($_GET['id'])){
    header("Location: admin_product.php");
    exit();
}

$id = intval($_GET['id']);

// ดึงข้อมูลสินค้า
$product_query = mysqli_query($conn,"SELECT * FROM products WHERE p_id=$id");
$row = mysqli_fetch_assoc($product_query);

if(!$row){
    header("Location: admin_product.php");
    exit();
}

// ดึงหมวดหมู่
$cat_query = mysqli_query($conn,"SELECT * FROM category");

// ดึงแบรนด์
$brand_query = mysqli_query($conn,"SELECT * FROM brand");

// สร้างโฟลเดอร์
$upload_dir = "FileUpload/";
if(!is_dir($upload_dir)){
    mkdir($upload_dir,0777,true);
}

// ===== UPDATE =====
if(isset($_POST['update'])){

    $name   = mysqli_real_escape_string($conn,$_POST['p_name']);
    $price  = $_POST['p_price'];
    $qty    = $_POST['p_qty'];
    $detail = mysqli_real_escape_string($conn,$_POST['p_detail']);
    $c_id   = $_POST['c_id'];
    $brand_id = $_POST['brand_id'];
    $type   = $_POST['p_type'];

    // ===== SIZE =====
    $p_size = "";
    if(isset($_POST['p_size'])){
        $p_size = implode(",",$_POST['p_size']);
    }

    $p_img = $row['p_img']; // ค่าเดิม

    // ===== อัปโหลดรูปใหม่ =====
    if($_FILES['p_img']['name'] != ""){

        $ext = strtolower(pathinfo($_FILES['p_img']['name'],PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if(in_array($ext,$allowed)){

            $new_name = "product_".time()."_".uniqid().".".$ext;
            $target = $upload_dir.$new_name;

            if(move_uploaded_file($_FILES['p_img']['tmp_name'],$target)){

                // ลบรูปเก่า
                if(!empty($row['p_img']) && file_exists($row['p_img'])){
                    unlink($row['p_img']);
                }

                $p_img = $target;
            }
        }
    }

    // ===== UPDATE SQL =====
    $sql = "UPDATE products SET
            p_name   = '$name',
            p_price  = '$price',
            p_qty    = '$qty',
            p_size   = '$p_size',
            p_type   = '$type',
            p_detail = '$detail',
            p_img    = '$p_img',
            c_id     = '$c_id',
            brand_id = '$brand_id'
            WHERE p_id = $id";

    if(mysqli_query($conn,$sql)){
        echo "<script>alert('อัปเดตสำเร็จ');window.location='admin_product.php';</script>";
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
<title>แก้ไขสินค้า</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Kanit',sans-serif;
    background:#f8f9fa;
}
.card{
    border-radius:18px;
    border:none;
}
.btn-theme{
    background:#ff5722;
    color:#fff;
}
.btn-theme:hover{
    background:#e64a19;
}
</style>
</head>
<body>

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-lg-8">

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
<select name="c_id" class="form-select" required>
<?php while($c=mysqli_fetch_assoc($cat_query)){ ?>
<option value="<?= $c['c_id']; ?>"
<?= ($row['c_id']==$c['c_id'])?'selected':''; ?>>
<?= $c['c_name']; ?>
</option>
<?php } ?>
</select>
</div>
</div>

<div class="mb-3">
<label>แบรนด์</label>
<select name="brand_id" class="form-select" required>
<?php while($b=mysqli_fetch_assoc($brand_query)){ ?>
<option value="<?= $b['brand_id']; ?>"
<?= ($row['brand_id']==$b['brand_id'])?'selected':''; ?>>
<?= $b['brand_name']; ?>
</option>
<?php } ?>
</select>
</div>

<div class="mb-3">
<label>เพศ</label><br>
<input type="radio" name="p_type" value="male"
<?= ($row['p_type']=='male')?'checked':''; ?>> ชาย
<input type="radio" name="p_type" value="female"
<?= ($row['p_type']=='female')?'checked':''; ?>> หญิง
<input type="radio" name="p_type" value="unisex"
<?= ($row['p_type']=='unisex')?'checked':''; ?>> Unisex
</div>

<div class="mb-3">
<label>รูปปัจจุบัน</label><br>
<?php if(!empty($row['p_img'])){ ?>
<img src="<?= $row['p_img']; ?>" width="120" class="rounded mb-2">
<?php } ?>
<input type="file" name="p_img" class="form-control">
<small class="text-muted">ถ้าไม่เลือก = ใช้รูปเดิม</small>
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
