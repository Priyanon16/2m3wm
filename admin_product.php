<?php
// admin_product.php
session_start();

// ============================================
// 1. ตั้งค่าการเชื่อมต่อฐานข้อมูล
include_once("check_login.php"); 
include_once("connectdb.php");

if (!$conn) {
    die("<h3>เชื่อมต่อฐานข้อมูลล้มเหลว</h3><p>" . mysqli_connect_error() . "</p>");
}
mysqli_set_charset($conn, "utf8");

// ============================================
// 2. ส่วนคำสั่งลบสินค้า (และลบรูปไฟล์ออกจากโฟลเดอร์ด้วย)
// ============================================
if(isset($_GET['delete_id'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    // 🔴 1. ดึงชื่อรูปภาพมาก่อน เพื่อจะลบทิ้ง
    $sql_img = "SELECT p_img FROM products WHERE p_id = '$id'";
    $res_img = mysqli_query($conn, $sql_img);
    $row_img = mysqli_fetch_assoc($res_img);
    
    // ลบข้อมูลในฐานข้อมูล
    $sql_delete = "DELETE FROM products WHERE p_id = '$id'";
    
    if(mysqli_query($conn, $sql_delete)){
        // 🔴 2. ถ้าลบในฐานข้อมูลสำเร็จ ให้ไปลบไฟล์รูปจริงด้วย (เพื่อไม่ให้หนักเครื่อง)
        if(!empty($row_img['p_img']) && file_exists($row_img['p_img'])){
            unlink($row_img['p_img']); // คำสั่งลบไฟล์
        }

        echo "<script>alert('ลบสินค้าเรียบร้อย'); window.location='admin_product.php';</script>";
    } else {
        echo "<script>alert('ลบสินค้าล้มเหลว: " . mysqli_error($conn) . "');</script>";
    }
}

// ============================================
// 3. ดึงข้อมูลสินค้าออกมาแสดง
// ============================================
$sql = "SELECT p.*, c.c_name 
        FROM products p 
        LEFT JOIN category c ON p.c_id = c.c_id 
        ORDER BY p.p_id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสินค้า | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .img-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
        .sidebar-area { min-height: 100vh; background-color: #343a40; }
        /* เพิ่มสไตล์ให้ไซส์ดูสวยงาม */
        .badge-size { font-size: 0.8rem; font-weight: normal; margin-right: 2px; margin-bottom: 2px; }
    </style>
</head>
<body class="bg-light">

    <div class="d-flex">
        
        <div class="sidebar-area flex-shrink-0">
            <?php include "sidebar.php"; ?>
        </div>

        <div class="content-area flex-grow-1 p-4">
            <div class="container-fluid"> 
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>📦 รายการสินค้าทั้งหมด</h3>
                    <a href="admin_add.php" class="btn btn-success">+ เพิ่มสินค้าใหม่</a>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50" class="text-center">ID</th>
                                    <th width="80">รูปภาพ</th>
                                    <th>ชื่อสินค้า</th>
                                    <th width="150">ไซส์</th> 
                                    <th width="100">ประเภท</th> 
                                    <th width="100">ราคา</th>
                                    <th width="120">หมวดหมู่</th>
                                    <th width="150" class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)){ 
                                ?>
                                <tr>
                                    <td class="text-center text-muted"><?= $row['p_id']; ?></td>
                                    
                                    <td>
                                        <?php if(!empty($row['p_img'])): ?>
                                            <img src="<?= $row['p_img']; ?>" class="img-thumb" alt="Product Image">
                                        <?php else: ?>
                                            <span class="text-muted small">ไม่มีรูป</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <div class="fw-bold"><?= $row['p_name']; ?></div>
                                        <small class="text-muted" style="font-size: 0.85rem;">
                                            <?= mb_substr($row['p_detail'], 0, 40); ?>...
                                        </small>
                                    </td>

                                    <td>
                                        <?php 
                                        if(!empty($row['p_size'])) {
                                            // แปลงข้อความ "38,39,40" กลับเป็น Array
                                            $sizes = explode(',', $row['p_size']);
                                            
                                            // วนลูปสร้างป้ายเล็กๆ
                                            foreach($sizes as $s) {
                                                echo '<span class="badge bg-info text-dark badge-size">'.$s.'</span> ';
                                            }
                                        } else {
                                            echo '<span class="text-muted small">-</span>';
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <?php 
                                            $type_show = $row['p_type']; 
                                            $type_color = 'secondary'; 
                                            if($row['p_type'] == 'male') { $type_show = 'ชาย'; $type_color = 'primary'; }
                                            elseif($row['p_type'] == 'female') { $type_show = 'หญิง'; $type_color = 'danger'; }
                                            elseif($row['p_type'] == 'unisex') { $type_show = 'Unisex'; $type_color = 'success'; }
                                        ?>
                                        <span class="badge bg-<?= $type_color; ?>"><?= $type_show; ?></span>
                                    </td>

                                    <td class="text-primary fw-bold">฿<?= number_format($row['p_price']); ?></td>
                                    
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= $row['c_name'] ?? '-'; ?>
                                        </span>
                                    </td>
                                    
                                    <td class="text-center">
                                        <a href="admin_edit.php?id=<?= $row['p_id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                        <a href="?delete_id=<?= $row['p_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันที่จะลบสินค้านี้?');">ลบ</a>
                                    </td>
                                </tr>
                                <?php 
                                    } 
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <h4 class="fw-light">ยังไม่มีสินค้าในระบบ</h4>
                                            <a href="admin_add.php" class="btn btn-outline-success mt-2">เพิ่มสินค้าชิ้นแรก</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div> 
        </div> 
    </div> 
</body>
</html>