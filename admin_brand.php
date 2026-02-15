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

            $file_tmp  = $_FILES['brand_img']['tmp_name'];
            $file_ext  = strtolower(pathinfo($_FILES['brand_img']['name'], PATHINFO_EXTENSION));
            $allowed   = ['jpg','jpeg','png','webp'];

            if(!in_array($file_ext,$allowed)){
                echo "<script>alert('รองรับเฉพาะไฟล์ JPG, PNG, WEBP');</script>";
            }else{

                $file_name  = time()."_".uniqid().".".$file_ext;
                $upload_dir = __DIR__ . "/uploads/brands/";

                if(!is_dir($upload_dir)){
                    mkdir($upload_dir,0777,true);
                }

                $target = $upload_dir.$file_name;

                if(move_uploaded_file($file_tmp,$target)){

                    $sql = "INSERT INTO brand (brand_name,brand_img)
                            VALUES ('$brand_name','$file_name')";

                    if(mysqli_query($conn,$sql)){
                        echo "<script>
                                alert('เพิ่มแบรนด์สำเร็จ');
                                window.location='admin_brand.php';
                              </script>";
                        exit();
                    }else{
                        echo "SQL Error: ".mysqli_error($conn);
                    }

                }else{
                    echo "<script>alert('อัปโหลดรูปไม่สำเร็จ');</script>";
                }
            }
        }else{
            echo "<script>alert('กรุณาเลือกรูปภาพ');</script>";
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

    if($imgRow && file_exists("uploads/brands/".$imgRow['brand_img'])){
        unlink("uploads/brands/".$imgRow['brand_img']);
    }

    mysqli_query($conn,"DELETE FROM brand WHERE brand_id=$id");

    echo "<script>window.location='admin_brand.php';</script>";
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
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
  font-family:'Kanit',sans-serif;
  background:#f4f6f9;
}

/* HEADER */
.header{
  background:#111;
  color:#fff;
  padding:20px 30px;
  box-shadow:0 5px 20px rgba(0,0,0,.2);
}

.header h3{
  margin:0;
  font-weight:600;
}

/* CARD */
.card{
  border:none;
  border-radius:16px;
  box-shadow:0 10px 25px rgba(0,0,0,.05);
}

/* BUTTON */
.btn-orange{
  background:#ff7a00;
  border:none;
  color:#fff;
  font-weight:500;
  border-radius:10px;
}
.btn-orange:hover{
  background:#e96f00;
  transform:translateY(-2px);
}

/* TABLE */
.table thead{
  background:#111;
  color:#fff;
}

.brand-img{
  width:70px;
  height:70px;
  object-fit:cover;
  border-radius:12px;
  border:2px solid #eee;
}

.btn-danger{
  border-radius:8px;
}
</style>
</head>
<body>

<div class="header">
  <h3>🏷 จัดการแบรนด์</h3>
</div>

<div class="container py-5">

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
               class="form-control"
               accept="image/*" required>
      </div>

      <div class="col-md-2">
        <button type="submit" name="add_brand"
                class="btn btn-orange w-100">
          เพิ่มแบรนด์
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
      <?php if(mysqli_num_rows($rs)>0): ?>
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
               onclick="return confirm('ต้องการลบแบรนด์นี้?')"
               class="btn btn-danger btn-sm">
               ลบ
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="4">ยังไม่มีข้อมูลแบรนด์</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</div>
</body>
</html>
