<?php
session_start();
include "data.php"; // เชื่อมต่อฐานข้อมูล

// ---------------------------------------------------
// 1. ส่วนจัดการข้อมูล (PHP Logic: Insert, Update, Delete)
// ---------------------------------------------------

// เช็คสถานะปัจจุบัน (ถ้าไม่มีให้เป็น 'index' คือหน้าตาราง)
$act = isset($_GET['act']) ? $_GET['act'] : 'index';

// --- ลบข้อมูล ---
if(isset($_GET['delete_id'])){
    $del_id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM products WHERE p_id = '$del_id'");
    echo "<script>alert('ลบข้อมูลเรียบร้อย'); window.location='admin_manage.php';</script>";
}

// --- บันทึกข้อมูลใหม่ (Insert) ---
if(isset($_POST['save_product'])){
    $name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = $_POST['p_price'];
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $c_id = $_POST['c_id'];
    $img = $_POST['p_img'];

    $sql = "INSERT INTO products (p_name, p_price, p_detail, p_img, c_id) 
            VALUES ('$name', '$price', '$detail', '$img', '$c_id')";
    
    if(mysqli_query($conn, $sql)){
        echo "<script>alert('เพิ่มสินค้าเรียบร้อย'); window.location='admin_manage.php';</script>";
    }
}

// --- อัปเดตข้อมูล (Update) ---
if(isset($_POST['update_product'])){
    $id = $_POST['p_id'];
    $name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = $_POST['p_price'];
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $c_id = $_POST['c_id'];
    $img = $_POST['p_img'];

    $sql = "UPDATE products SET 
            p_name='$name', p_price='$price', p_detail='$detail', p_img='$img', c_id='$c_id' 
            WHERE p_id='$id'";

    if(mysqli_query($conn, $sql)){
        echo "<script>alert('แก้ไขข้อมูลเรียบร้อย'); window.location='admin_manage.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบจัดการสินค้า | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-box-seam"></i> จัดการสินค้าหลังบ้าน</h3>
        <?php if($act != 'index'){ ?>
            <a href="admin_manage.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> ย้อนกลับ</a>
        <?php } ?>
    </div>

    <?php 
    // === VIEW 1: ฟอร์มเพิ่มสินค้า ===
    if($act == 'add'){ 
        // ดึงหมวดหมู่มาใส่ Select
        $cats = mysqli_query($conn, "SELECT * FROM category");
    ?>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">เพิ่มสินค้าใหม่</div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label>ชื่อสินค้า</label>
                        <input type="text" name="p_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>ราคา</label>
                            <input type="number" name="p_price" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>หมวดหมู่</label>
                            <select name="c_id" class="form-select" required>
                                <option value="">-- เลือก --</option>
                                <?php while($c = mysqli_fetch_assoc($cats)){ ?>
                                    <option value="<?=$c['c_id']?>"><?=$c['c_name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>URL รูปภาพ</label>
                        <input type="text" name="p_img" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>รายละเอียด</label>
                        <textarea name="p_detail" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" name="save_product" class="btn btn-success w-100">บันทึก</button>
                </form>
            </div>
        </div>

    <?php 
    // === VIEW 2: ฟอร์มแก้ไขสินค้า ===
    } elseif($act == 'edit'){ 
        $id = $_GET['id'];
        $res = mysqli_query($conn, "SELECT * FROM products WHERE p_id='$id'");
        $row = mysqli_fetch_assoc($res);
        $cats = mysqli_query($conn, "SELECT * FROM category");
    ?>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark">แก้ไขสินค้า ID: <?=$id?></div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="p_id" value="<?=$row['p_id']?>">
                    <div class="mb-3">
                        <label>ชื่อสินค้า</label>
                        <input type="text" name="p_name" class="form-control" value="<?=$row['p_name']?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>ราคา</label>
                            <input type="number" name="p_price" class="form-control" value="<?=$row['p_price']?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>หมวดหมู่</label>
                            <select name="c_id" class="form-select" required>
                                <?php while($c = mysqli_fetch_assoc($cats)){ ?>
                                    <option value="<?=$c['c_id']?>" <?=($c['c_id']==$row['c_id'])?'selected':''?>>
                                        <?=$c['c_name']?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>URL รูปภาพ</label>
                        <input type="text" name="p_img" class="form-control" value="<?=$row['p_img']?>">
                        <?php if($row['p_img']){ echo "<img src='{$row['p_img']}' width='80' class='mt-2 rounded'>"; } ?>
                    </div>
                    <div class="mb-3">
                        <label>รายละเอียด</label>
                        <textarea name="p_detail" class="form-control" rows="3"><?=$row['p_detail']?></textarea>
                    </div>
                    <button type="submit" name="update_product" class="btn btn-warning w-100">อัปเดตข้อมูล</button>
                </form>
            </div>
        </div>

    <?php 
    // === VIEW 3: ตารางแสดงข้อมูล (Default) ===
    } else { 
        $products = mysqli_query($conn, "SELECT p.*, c.c_name FROM products p LEFT JOIN category c ON p.c_id = c.c_id ORDER BY p.p_id DESC");
    ?>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-3">
                    <a href="?act=add" class="btn btn-primary">+ เพิ่มสินค้าใหม่</a>
                </div>
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>รูป</th>
                            <th>ชื่อสินค้า</th>
                            <th>ราคา</th>
                            <th>หมวดหมู่</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($p = mysqli_fetch_assoc($products)){ ?>
                        <tr>
                            <td><img src="<?=$p['p_img']?>" width="50" class="rounded"></td>
                            <td><?=$p['p_name']?></td>
                            <td class="text-success fw-bold">฿<?=number_format($p['p_price'])?></td>
                            <td><span class="badge bg-secondary"><?=$p['c_name']?></span></td>
                            <td>
                                <a href="?act=edit&id=<?=$p['p_id']?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <a href="?delete_id=<?=$p['p_id']?>" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันลบ?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>

</div>
</body>
</html>