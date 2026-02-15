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
   แก้ไขแบรนด์
========================= */
if(isset($_POST['update_brand'])){

    $brand_id   = intval($_POST['brand_id']);
    $brand_name = mysqli_real_escape_string($conn,$_POST['brand_name']);

    $old = mysqli_query($conn,"SELECT brand_img FROM brand WHERE brand_id=$brand_id");
    $oldData = mysqli_fetch_assoc($old);
    $oldImg  = $oldData['brand_img'];

    if($_FILES['brand_img']['name'] != ""){

        $ext = strtolower(pathinfo($_FILES['brand_img']['name'], PATHINFO_EXTENSION));
        $allow = ['jpg','jpeg','png','webp'];

        if(in_array($ext,$allow)){

            $file_name = time()."_".uniqid().".".$ext;
            move_uploaded_file($_FILES['brand_img']['tmp_name'],$upload_dir.$file_name);

            if(file_exists($upload_dir.$oldImg)){
                unlink($upload_dir.$oldImg);
            }

            mysqli_query($conn,"UPDATE brand 
                                SET brand_name='$brand_name',
                                    brand_img='$file_name'
                                WHERE brand_id=$brand_id");
        }

    }else{
        mysqli_query($conn,"UPDATE brand 
                            SET brand_name='$brand_name'
                            WHERE brand_id=$brand_id");
    }

    echo "<script>alert('แก้ไขสำเร็จ');window.location='admin_brand.php';</script>";
    exit();
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body{font-family:'Kanit',sans-serif;background:#f4f6f9;}
.header{background:#111;color:#fff;padding:20px 30px;}
.btn-orange{background:#ff7a00;color:#fff;border:none;}
.btn-orange:hover{background:#e96f00;}
.table thead{background:#111;color:#fff;}
.brand-img{width:70px;height:70px;object-fit:cover;border-radius:10px;}
.card{border:none;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,.05);}

.layout{
    display:flex;
    min-height:100vh;
}

.sidebar{
    width:250px;
    background:#111;
    color:#fff;
}

.main-content{
    flex:1;
    background:#f4f6f9;
}

</style>
</head>
<body>

<div class="layout">

    <?php include("sidebar.php"); ?>

    <div class="main-content">

        <div class="header">
          <h3>🏷 จัดการแบรนด์</h3>
        </div>

        <div class="container py-5">


<!-- เพิ่มแบรนด์ -->
<div class="card p-4 mb-4">
  <h5 class="mb-3">เพิ่มแบรนด์ใหม่</h5>
  <form method="POST" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-md-4">
        <input type="text" name="brand_name" class="form-control"
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

<!-- ตาราง -->
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
        <td><img src="uploads/brands/<?= $b['brand_img']; ?>" class="brand-img"></td>
        <td><?= $b['brand_name']; ?></td>
        <td>
          <button class="btn btn-warning btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#edit<?= $b['brand_id']; ?>">
            แก้ไข
          </button>
          <a href="?delete=<?= $b['brand_id']; ?>"
             onclick="return confirm('ลบแบรนด์นี้?')"
             class="btn btn-danger btn-sm">ลบ</a>
        </td>
      </tr>

      <!-- Modal แก้ไข -->
      <div class="modal fade" id="edit<?= $b['brand_id']; ?>">
        <div class="modal-dialog">
          <div class="modal-content">
          <form method="POST" enctype="multipart/form-data">
            <div class="modal-header bg-dark text-white">
              <h5 class="modal-title">แก้ไขแบรนด์</h5>
            </div>
            <div class="modal-body">
              <input type="hidden" name="brand_id" value="<?= $b['brand_id']; ?>">
              <div class="mb-3">
                <label>ชื่อแบรนด์</label>
                <input type="text" name="brand_name"
                       value="<?= $b['brand_name']; ?>"
                       class="form-control" required>
              </div>
              <div class="mb-3">
                <label>เปลี่ยนรูป (ถ้าต้องการ)</label>
                <input type="file" name="brand_img" class="form-control">
              </div>
              <img src="uploads/brands/<?= $b['brand_img']; ?>" class="brand-img">
            </div>
            <div class="modal-footer">
              <button type="submit" name="update_brand"
                      class="btn btn-orange">บันทึก</button>
            </div>
          </form>
          </div>
        </div>
      </div>

      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        </div> <!-- container -->
    </div> <!-- main-content -->

</div> <!-- layout -->

</body>
</html>
