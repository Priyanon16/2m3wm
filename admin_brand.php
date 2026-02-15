<?php
session_start();
include_once("connectdb.php");

/* =========================
   เพิ่มแบรนด์
========================= */
if(isset($_POST['add_brand'])){

    if(empty($_POST['brand_name'])){
        echo "<script>alert('กรุณากรอกชื่อแบรนด์');</script>";
    }else{

        $brand_name = mysqli_real_escape_string($conn,$_POST['brand_name']);

        if(isset($_FILES['brand_img']) && $_FILES['brand_img']['error'] == 0){

            $file_tmp  = $_FILES['brand_img']['tmp_name'];   // ✅ เพิ่มบรรทัดนี้
            $file_name = time()."_".$_FILES['brand_img']['name'];
            $target = "uploads/brands/".$file_name;

            if(move_uploaded_file($file_tmp,$target)){

                $sql = "INSERT INTO brand (brand_name,brand_img)
                        VALUES ('$brand_name','$file_name')";

                if(mysqli_query($conn,$sql)){
                    echo "<script>alert('เพิ่มแบรนด์สำเร็จ');window.location='admin_brand.php';</script>";
                }else{
                    echo "<script>alert('เกิดข้อผิดพลาด SQL');</script>";
                }

            }else{
                echo "<script>alert('อัปโหลดรูปไม่สำเร็จ');</script>";
            }

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

    if(file_exists("../uploads/brands/".$imgRow['brand_img'])){
        unlink("../uploads/brands/".$imgRow['brand_img']);
    }

    mysqli_query($conn,"DELETE FROM brand WHERE brand_id=$id");
}

/* =========================
   ดึงข้อมูลแบรนด์
========================= */
$rs = mysqli_query($conn,"SELECT * FROM brand ORDER BY brand_id DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการแบรนด์</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{
  background:#f5f5f5;
  font-family:'Kanit',sans-serif;
}
.header{
  background:#000;
  color:#fff;
  padding:20px;
}
.btn-orange{
  background:#ff7a00;
  color:#fff;
  border:none;
}
.btn-orange:hover{
  background:#e96f00;
}
.card{
  border:none;
  border-radius:12px;
}
.table thead{
  background:#000;
  color:#fff;
}
.brand-img{
  width:60px;
  height:60px;
  object-fit:cover;
  border-radius:8px;
}
</style>
</head>
<body>

<div class="header">
  <h3>🏷 จัดการแบรนด์</h3>
</div>

<div class="container py-4">

<!-- เพิ่มแบรนด์ -->
<div class="card p-4 mb-4 shadow-sm">
  <h5 class="mb-3">เพิ่มแบรนด์ใหม่</h5>
  <form method="POST" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-md-4">
        <input type="text" name="brand_name" class="form-control"
               placeholder="ชื่อแบรนด์" required>
      </div>
      <div class="col-md-4">
        <input type="file" name="brand_img" class="form-control"
               accept="image/*" required>
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
<div class="card p-4 shadow-sm">
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
            <img src="../uploads/brands/<?= $b['brand_img']; ?>"
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

</div>
</body>
</html>
