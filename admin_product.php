<?php
// admin_product.php
session_start();

// 1. เชื่อมต่อฐานข้อมูล
include_once("check_login.php"); 
include_once("connectdb.php");

if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }
mysqli_set_charset($conn, "utf8");

// 2. ส่วนคำสั่งลบสินค้า
if(isset($_GET['delete_id'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    // ดึงรูปมาเช็คก่อนลบ
    $sql_img = "SELECT p_img FROM products WHERE p_id = '$id'";
    $res_img = mysqli_query($conn, $sql_img);
    $row_img = mysqli_fetch_assoc($res_img);
    
    // ลบใน DB
    if(mysqli_query($conn, "DELETE FROM products WHERE p_id = '$id'")){
        // ลบไฟล์รูป
        if(!empty($row_img['p_img']) && file_exists($row_img['p_img'])){
            unlink($row_img['p_img']); 
        }
        echo "<script>alert('ลบสินค้าเรียบร้อย'); window.location='admin_product.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// 3. ดึงข้อมูล
$sql = "SELECT p.*, c.c_name FROM products p LEFT JOIN category c ON p.c_id = c.c_id ORDER BY p.p_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายการสินค้า</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --theme-black: #121212;       /* 60% พื้นหลังหลัก */
            --theme-dark-card: #1e1e1e;   /* พื้นหลังส่วน Sidebar หรือ Card เข้ม */
            --theme-white: #ffffff;       /* 30% พื้นหลังเนื้อหา */
            --theme-orange: #ff6600;      /* 10% สีเน้น (Accent) */
            --theme-orange-hover: #e65c00;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--theme-black); /* สีดำ */
            color: #333;
        }

        /* Sidebar Area (สมมติว่า include sidebar มา) */
        .sidebar-area {
            min-height: 100vh;
            background-color: #000000; /* ดำสนิท */
            border-right: 1px solid #333;
        }

        /* Card Container */
        .custom-card {
            background-color: var(--theme-white); /* ขาว */
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0, 0.5); /* เงาฟุ้งๆ */
            border: none;
            overflow: hidden;
        }

        /* หัวตาราง */
        .table-head-custom {
            background-color: #000000 !important; /* ดำ */
            color: var(--theme-orange) !important; /* ตัวหนังสือส้ม */
            text-transform: uppercase;
            font-weight: 500;
            border-bottom: 2px solid var(--theme-orange);
        }

        /* ปุ่มหลัก (สีส้ม) */
        .btn-theme-orange {
            background-color: var(--theme-orange);
            color: #fff;
            border: none;
            box-shadow: 0 4px 10px rgba(255, 102, 0, 0.3);
            transition: all 0.3s ease;
        }
        .btn-theme-orange:hover {
            background-color: var(--theme-orange-hover);
            color: #fff;
            transform: translateY(-2px);
        }

        /* รูปภาพ */
        .img-thumb {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #eee;
        }

        /* Badge ไซส์ */
        .badge-size {
            background-color: #fff;
            color: #333;
            border: 1px solid #ddd;
            font-weight: 400;
            margin: 2px;
        }
        
        /* Badge ราคา */
        .price-tag {
            color: var(--theme-orange);
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Scrollbar สวยๆ */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #121212; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--theme-orange); }
    </style>
</head>
<body>

    <div class="d-flex">
        
        <div class="sidebar-area flex-shrink-0 d-none d-md-block">
            <?php include "sidebar.php"; ?>
        </div>

        <div class="content-area flex-grow-1 p-4">
            <div class="container-fluid">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="text-white fw-bold">📦 จัดการสินค้า</h2>
                        <p class="text-secondary mb-0">รายการสินค้าทั้งหมดในระบบ</p>
                    </div>
                    <a href="admin_add.php" class="btn btn-theme-orange px-4 py-2 rounded-pill">
                        <i class="bi bi-plus-lg"></i> + เพิ่มสินค้าใหม่
                    </a>
                </div>

                <div class="custom-card p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-head-custom">
                                <tr>
                                    <th class="py-3 text-center rounded-start">ID</th>
                                    <th class="py-3">สินค้า</th>
                                    <th class="py-3">รายละเอียด</th>
                                    <th class="py-3">ไซส์</th>
                                    <th class="py-3">หมวดหมู่/เพศ</th>
                                    <th class="py-3 text-end">ราคา</th>
                                    <th class="py-3 text-center rounded-end">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)){ 
                                ?>
                                <tr>
                                    <td class="text-center text-muted fw-bold">#<?= $row['p_id']; ?></td>
                                    
                                    <td width="100">
                                        <?php if(!empty($row['p_img'])): ?>
                                            <img src="<?= $row['p_img']; ?>" class="img-thumb shadow-sm">
                                        <?php else: ?>
                                            <div class="img-thumb bg-light d-flex align-items-center justify-content-center text-muted small">
                                                No Pic
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <h6 class="fw-bold mb-1 text-dark"><?= $row['p_name']; ?></h6>
                                        <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                            <?= $row['p_detail']; ?>
                                        </small>
                                    </td>

                                    <td width="150">
                                        <div class="d-flex flex-wrap" style="max-width: 150px;">
                                        <?php 
                                        if(!empty($row['p_size'])) {
                                            $sizes = explode(',', $row['p_size']);
                                            foreach($sizes as $s) {
                                                // แต่งป้ายไซส์ให้ดู Minimal
                                                echo '<span class="badge-size badge rounded-pill">EU '.$s.'</span>';
                                            }
                                        } else {
                                            echo '<span class="text-muted small">-</span>';
                                        }
                                        ?>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="mb-1">
                                            <span class="badge bg-dark text-white fw-light border border-secondary">
                                                <?= $row['c_name'] ?? 'ไม่มีหมวด'; ?>
                                            </span>
                                        </div>
                                        <?php 
                                            $badge_cls = ($row['p_type'] == 'male') ? 'bg-primary' : (($row['p_type'] == 'female') ? 'bg-danger' : 'bg-success');
                                            $type_txt = ($row['p_type'] == 'male') ? 'Men' : (($row['p_type'] == 'female') ? 'Women' : 'Unisex');
                                        ?>
                                        <span class="badge <?= $badge_cls; ?> bg-opacity-75" style="font-size: 0.75rem;"><?= $type_txt; ?></span>
                                    </td>

                                    <td class="text-end">
                                        <span class="price-tag">฿<?= number_format($row['p_price']); ?></span>
                                    </td>
                                    
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="admin_edit.php?id=<?= $row['p_id']; ?>" class="btn btn-outline-dark btn-sm rounded-start">
                                                แก้ไข
                                            </a>
                                            <a href="?delete_id=<?= $row['p_id']; ?>" class="btn btn-outline-danger btn-sm rounded-end" onclick="return confirm('ยืนยันที่จะลบสินค้านี้?');">
                                                ลบ
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    } 
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted opacity-50 mb-3">
                                                <h1 style="font-size: 4rem;">📦</h1>
                                            </div>
                                            <h4 class="fw-light text-secondary">ยังไม่มีสินค้าในระบบ</h4>
                                            <a href="admin_add.php" class="btn btn-theme-orange mt-3 px-4 rounded-pill">
                                                + เพิ่มสินค้าชิ้นแรก
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div> </div> 
        </div> 
    </div> 
</body>
</html>