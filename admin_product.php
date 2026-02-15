<?php
session_start();
include_once("check_login.php");
include_once("connectdb.php");
mysqli_set_charset($conn,"utf8");

/* ลบสินค้า */
if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);

    $img = mysqli_query($conn,"SELECT p_img FROM products WHERE p_id=$id");
    $imgRow = mysqli_fetch_assoc($img);

    if(mysqli_query($conn,"DELETE FROM products WHERE p_id=$id")){
        if(!empty($imgRow['p_img']) && file_exists($imgRow['p_img'])){
            unlink($imgRow['p_img']);
        }
        echo "<script>alert('ลบเรียบร้อย');window.location='admin_product.php';</script>";
        exit();
    }
}

/* ดึงข้อมูล */
$sql = "SELECT p.*, 
               c.c_name, 
               b.brand_name
        FROM products p
        LEFT JOIN category c ON p.c_id=c.c_id
        LEFT JOIN brand b ON p.brand_id=b.brand_id
        ORDER BY p.p_id DESC";

$result = mysqli_query($conn,$sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการสินค้า</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Kanit',sans-serif;background:#f4f6f9;}
.card{border:none;border-radius:18px;box-shadow:0 10px 25px rgba(0,0,0,.05);}
.table thead{background:#111;color:#fff;}
.price{color:#ff5722;font-weight:700;}
</style>
</head>
<body class="p-4">

<div class="container">

<div class="d-flex justify-content-between mb-3">
<h3>จัดการสินค้า</h3>
<a href="admin_add.php" class="btn btn-warning">
<i class="bi bi-plus"></i> เพิ่มสินค้า
</a>
</div>

<div class="card p-4">
<table class="table align-middle">
<thead>
<tr>
<th>ID</th>
<th>ชื่อสินค้า</th>
<th>หมวด</th>
<th>แบรนด์</th>
<th>เพศ</th>
<th>ราคา</th>
<th>จัดการ</th>
</tr>
</thead>
<tbody>
<?php while($row=mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $row['p_id']; ?></td>
<td><?= $row['p_name']; ?></td>
<td><?= $row['c_name'] ?? '-'; ?></td>
<td><?= $row['brand_name'] ?? '-'; ?></td>
<td>
<?php
if($row['gender']=='male') echo '<span class="badge bg-primary">Men</span>';
elseif($row['gender']=='female') echo '<span class="badge bg-danger">Women</span>';
else echo '<span class="badge bg-success">Unisex</span>';
?>
</td>
<td class="price">฿<?= number_format($row['p_price']); ?></td>
<td>
<a href="admin_edit.php?id=<?= $row['p_id']; ?>" class="btn btn-sm btn-secondary">
<i class="bi bi-pencil"></i>
</a>
<a href="?delete_id=<?= $row['p_id']; ?>" 
class="btn btn-sm btn-danger"
onclick="return confirm('ลบ?')">
<i class="bi bi-trash"></i>
</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</div>
</body>
</html>
