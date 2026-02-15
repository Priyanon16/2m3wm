<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

$upload_dir = __DIR__."/uploads/brands/";
if(!is_dir($upload_dir)){
    mkdir($upload_dir,0777,true);
}

/* =========================
   เพิ่มแบรนด์
========================= */
if(isset($_POST['add_brand'])){

    $brand_name = mysqli_real_escape_string($conn,$_POST['brand_name']);

    if(isset($_FILES['brand_img']) && $_FILES['brand_img']['error'] == 0){

        $ext = strtolower(pathinfo($_FILES['brand_img']['name'], PATHINFO_EXTENSION));
        $allow = ['jpg','jpeg','png','webp'];

        if(in_array($ext,$allow)){

            $file_name = time()."_".uniqid().".".$ext;
            move_uploaded_file($_FILES['brand_img']['tmp_name'],$upload_dir.$file_name);

            mysqli_query($conn,"INSERT INTO brand (brand_name,brand_img)
                                VALUES ('$brand_name','$file_name')");

            echo "<script>alert('เพิ่มแบรนด์สำเร็จ');window.location='admin_brand.php';</script>";
            exit();
        }
    }
}

/* =========================
   ลบแบรนด์
========================= */
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    $getImg = mysqli_query($conn,"SELECT brand_img FROM brand WHERE brand_id=$id");
    $imgRow = mysqli_fetch_assoc($getImg);

    if(file_exists($upload_dir.$imgRow['brand_img'])){
        unlink($upload_dir.$imgRow['brand_img']);
    }

    mysqli_query($conn,"DELETE FROM brand WHERE brand_id=$id");
    echo "<script>window.location='admin_brand.php';</script>";
    exit();
}

$rs = mysqli_query($conn,"SELECT * FROM brand ORDER BY brand_id DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการแบรนด์</title>

<style>
body{
    font-family:'Kanit',sans-serif;
    background:#f4f6f9;
}

/* Layout */
.layout{
    display:flex;
    min-height:100vh;
}

.main-content{
    flex:1;
    background:#ffffff;
}

/* Header */
.page-header{
    background:#ffffff;
    border-radius:18px;
    padding:20px 30px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:20px;
    position:relative;
    box-shadow:0 8px 25px rgba(0,0,0,.05);
}

.page-header::before{
    content:"";
    position:absolute;
    left:0;
    top:0;
    height:100%;
    width:6px;
    background:#ff7a00;
    border-top-left-radius:18px;
    border-bottom-left-radius:18px;
}

.page-title{
    display:flex;
    align-items:center;
    gap:15px;
    font-size:22px;
    font-weight:600;
    color:#ff7a00;
}

.page-sub{
    color:#6c757d;
    font-weight:500;
}

/* Cards */
.card{
    border:none;
    border-radius:16px;
    box-shadow:0 10px 25px rgba(0,0,0,.05);
}

/* Table */
.table thead{
    background:#111;
    color:#fff;
}

/* Buttons */
.btn-orange{
    background:#ff7a00;
    color:#fff;
    border:none;
}

.btn-orange:hover{
    background:#ff9a3c;
}

.brand-img{
    width:70px;
    height:70px;
    object-fit:cover;
    border-radius:10px;
    border:1px solid #eee;
}
</style>
</head>

<body>

<div class="layout">

    <?php include("sidebar.php"); ?>

    <div class="main-content">

        <div class="container py-4">

            <!-- Header -->
            <div class="page-header">
                <div class="page-title">
                    <i class="bi bi-award"></i>
                    <span>จัดการแบรนด์</span>
                </div>
                <div class="page-sub">
                    Brand Management
                </div>
            </div>

            <!-- เพิ่มแบรนด์ -->
            <div class="card p-4 mb-4">
                <h5 class="mb-3">เพิ่มแบรนด์ใหม่</h5>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <input type="text" name="brand_name"
                                   class="form-control"
                                   placeholder="ชื่อแบรนด์" required>
                        </div>
                        <div class="col-md-4">
                            <input type="file" name="brand_img"
                                   class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="add_brand"
                                    class="btn btn-orange w-100">
                                เพิ่ม
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ตารางแบรนด์ -->
            <div class="card p-4">
                <h5 class="mb-3">รายการแบรนด์</h5>

                <div class="table-responsive">
                    <table class="table align-middle text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>โลโก้</th>
                                <th>ชื่อแบรนด์</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($b=mysqli_fetch_assoc($rs)): ?>
                            <tr>
                                <td><?= $b['brand_id']; ?></td>
                                <td>
                                    <img src="uploads/brands/<?= $b['brand_img']; ?>"
                                         class="brand-img">
                                </td>
                                <td><?= $b['brand_name']; ?></td>
                                <td>
                                    <a href="?delete=<?= $b['brand_id']; ?>"
                                       onclick="return confirm('ลบแบรนด์นี้?')"
                                       class="btn btn-danger btn-sm">
                                       ลบ
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div> <!-- container -->

    </div> <!-- main-content -->

</div> <!-- layout -->

</body>
</html>
