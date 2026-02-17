<?php
session_start();
include_once("check_login.php");
include_once("connectdb.php");
include_once("bootstrap.php");
mysqli_set_charset($conn,"utf8");

/* =========================
   ลบสินค้า
========================= */
if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);

    // หมายเหตุ: ตรงนี้โค้ดยังเป็นแบบเดิมที่ดึงจาก products 
    // ถ้าอนาคตต้องการลบรูปจริงจาก folder ด้วย อาจต้องแก้ให้ดึงจาก product_images
    $img = mysqli_query($conn,"SELECT p_img FROM products WHERE p_id=$id");
    $imgRow = mysqli_fetch_assoc($img);

    if(mysqli_query($conn,"DELETE FROM products WHERE p_id=$id")){
        if(!empty($imgRow['p_img']) && file_exists($imgRow['p_img'])){
            unlink($imgRow['p_img']);
        }
        echo "<script>alert('ลบสินค้าเรียบร้อย');window.location='admin_product.php';</script>";
        exit();
    }
}

/* =========================
   ดึงแบรนด์
========================= */
$brand_rs = mysqli_query($conn,"SELECT * FROM brand ORDER BY brand_name ASC");

/* =========================
   Filter
========================= */
$where = "";
if(isset($_GET['brand_id']) && $_GET['brand_id'] != ""){
    $brand_id = intval($_GET['brand_id']);
    $where = "WHERE p.brand_id = $brand_id";
}

/* =========================
   ดึงสินค้า (แก้ไข SQL)
========================= */
// [แก้ไข] เพิ่ม Subquery เพื่อดึงรูปภาพ 1 รูปมาเป็น thumbnail
$sql = "SELECT p.*, c.c_name, b.brand_name,
        (SELECT img_path FROM product_images WHERE p_id = p.p_id LIMIT 1) AS thumbnail
        FROM products p
        LEFT JOIN category c ON p.c_id = c.c_id
        LEFT JOIN brand b ON p.brand_id = b.brand_id
        $where
        ORDER BY p.p_id DESC";

$result = mysqli_query($conn,$sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการสินค้า</title>

<style>
body{
    font-family:'Kanit',sans-serif;
    background:#f4f6f9;
}

.layout{
    display:flex;
    min-height:100vh;
}

.main-content{
    flex:1;
    padding:30px;
}

.card{
    border:none;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,.05);
}

.table thead{
    background:#111;
    color:#fff;
}

.price{
    color:#ff5722;
    font-weight:700;
}

.btn-theme{
    background:#ff5722;
    color:#fff;
    border:none;
}
.btn-theme:hover{
    background:#e64a19;
}

/* เพิ่ม CSS สำหรับรูปภาพ thumbnail */
.img-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #ddd;
}
</style>
</head>

<body>

<div class="layout">

    <?php include("sidebar.php"); ?>

    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">
                <i class="bi bi-box-seam text-warning me-2"></i>
                จัดการสินค้า
            </h3>

            <a href="admin_add.php" class="btn btn-theme">
                <i class="bi bi-plus-lg"></i> เพิ่มสินค้า
            </a>
        </div>

        <div class="card p-3 mb-4">
            <form method="GET" class="row align-items-center g-3">
                <div class="col-md-4">
                    <label class="fw-semibold">เลือกแบรนด์</label>
                    <select name="brand_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- ทุกแบรนด์ --</option>
                        <?php while($b=mysqli_fetch_assoc($brand_rs)): ?>
                        <option value="<?= $b['brand_id']; ?>"
                            <?= (isset($_GET['brand_id']) && $_GET['brand_id']==$b['brand_id'])?'selected':''; ?>>
                            <?= $b['brand_name']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <?php if(isset($_GET['brand_id']) && $_GET['brand_id']!=""): ?>
                <div class="col-md-2">
                    <a href="admin_product.php" class="btn btn-secondary mt-4">
                        รีเซ็ต
                    </a>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>รูป</th> <th>ชื่อสินค้า</th>
                            <th>หมวดหมู่</th>
                            <th>แบรนด์</th>
                            <th>เพศ</th>
                            <th>คงเหลือ</th> <th>ราคา</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if(mysqli_num_rows($result)>0): ?>
                    <?php while($row=mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?= $row['p_id']; ?></td>
                        
                        <td>
                            <?php if(!empty($row['thumbnail'])): ?>
                                <img src="<?= $row['thumbnail']; ?>" class="img-thumb">
                            <?php else: ?>
                                <span class="text-muted small">ไม่มีรูป</span>
                            <?php endif; ?>
                        </td>

                        <td><?= $row['p_name']; ?></td>
                        <td><?= $row['c_name'] ?? '-'; ?></td>
                        <td>
                            <span class="badge bg-dark">
                                <?= $row['brand_name'] ?? '-'; ?>
                            </span>
                        </td>
                        <td>
                        <?php
                        if($row['p_type']=='male')
                            echo '<span class="badge bg-primary">Men</span>';
                        elseif($row['p_type']=='female')
                            echo '<span class="badge bg-danger">Women</span>';
                        else
                            echo '<span class="badge bg-success">Unisex</span>';
                        ?>
                        </td>

                        <td>
                            <?php if($row['p_qty'] <= 0): ?>
                                <span class="badge bg-danger">สินค้าหมด</span>
                            <?php else: ?>
                                <?= number_format($row['p_qty']); ?> ชิ้น
                            <?php endif; ?>
                        </td>

                        <td class="price">฿<?= number_format($row['p_price']); ?></td>
                        <td class="text-center">
                            <a href="admin_edit.php?id=<?= $row['p_id']; ?>" 
                               class="btn btn-sm btn-outline-secondary">
                               <i class="bi bi-pencil"></i>
                            </a>

                            <a href="?delete_id=<?= $row['p_id']; ?>" 
                               onclick="return confirm('ยืนยันการลบ?')"
                               class="btn btn-sm btn-outline-danger">
                               <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4"> ยังไม่มีสินค้าในระบบ
                        </td>
                    </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

</body>
</html>