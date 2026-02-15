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
    
    // ดึง path รูปมาเช็คก่อนลบ
    $sql_img = "SELECT p_img FROM products WHERE p_id = '$id'";
    $res_img = mysqli_query($conn, $sql_img);
    $row_img = mysqli_fetch_assoc($res_img);
    
    // ลบข้อมูลใน DB
    if(mysqli_query($conn, "DELETE FROM products WHERE p_id = '$id'")){
        if(!empty($row_img['p_img'])){
            $files = explode(',', $row_img['p_img']); 
            foreach ($files as $file) {
                $file = trim($file);
                if(!empty($file) && file_exists($file)){
                    unlink($file); 
                }
            }
        }
        echo "<script>alert('ลบสินค้าเรียบร้อย'); window.location='admin_product.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// 3. ดึงข้อมูล (มั่นใจว่ามี p_qty ใน database แล้ว)
$sql = "SELECT p.*, c.c_name FROM products p LEFT JOIN category c ON p.c_id = c.c_id ORDER BY p.p_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสินค้า - 2M3WM Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --theme-orange: #ff5722;
            --theme-orange-hover: #e64a19;
            --theme-dark: #1a1a1a;
            --theme-bg: linear-gradient(135deg, #f8f9fa, #eef1f4);
        }

        body {
            font-family: 'Kanit', sans-serif;
            background: var(--theme-bg);
            min-height: 100vh;
        }

        header {
            background: linear-gradient(90deg, #111, var(--theme-dark));
            padding: 1rem 0;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            margin-bottom: 2rem;
        }
        
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 2px;
            color: #fff !important;
        }

        .btn-theme {
            background: var(--theme-orange);
            color: white !important;
            border: none;
            border-radius: 50px;
            padding: 8px 22px;
            transition: .3s;
        }

        .btn-theme:hover {
            background: var(--theme-orange-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 87, 34, 0.3);
        }

        .content-card {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-top: 5px solid var(--theme-orange);
            margin-bottom: 2rem;
        }

        .card-title-custom {
            color: var(--theme-dark);
            font-weight: 700;
        }

        .table img.img-thumb {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 12px;
        }
        
        .price-tag {
            color: var(--theme-orange);
            font-weight: 700;
        }

        .badge-size {
            background: #f1f1f1;
            color: #333;
            border: 1px solid #ddd;
            margin: 1px;
            font-size: 0.7rem;
        }

        /* ตกแต่งตัวเลขจำนวนสินค้า */
        .qty-text {
            font-weight: 600;
        }
        .qty-out { color: #dc3545; } /* หมด */
        .qty-low { color: #fd7e14; } /* ใกล้หมด */
        .qty-ok { color: #198754; }  /* มีของ */
    </style>
</head>
<body>

    <header>
        <div class="container d-flex align-items-center justify-content-between">
            <a class="navbar-brand" href="index_admin.php">
                <i class="bi bi-shield-check me-2"></i>2M3WM ADMIN
            </a>
            <div class="d-flex align-items-center gap-4">
                <a href="logout.php" class="btn btn-theme text-decoration-none">
                    <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="index_admin.php" class="text-secondary text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
            </a>
            <a href="admin_add.php" class="btn btn-theme">
                <i class="bi bi-plus-lg me-1"></i> เพิ่มสินค้าใหม่
            </a>
        </div>

        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <h3 class="card-title-custom">
                    <i class="bi bi-box-seam me-2 text-warning"></i>รายการสินค้าในระบบ
                </h3>
                <span class="badge bg-secondary rounded-pill">ทั้งหมด <?= mysqli_num_rows($result); ?> รายการ</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">ID</th>
                            <th width="10%">รูปภาพ</th>
                            <th width="20%">ชื่อสินค้า</th>
                            <th width="15%">หมวดหมู่/สเปค</th>
                            <th class="text-center" width="12%">สต็อก (คู่)</th> <th class="text-end" width="13%">ราคา</th>
                            <th class="text-center" width="15%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)){ 
                                $img_src = "";
                                if(!empty($row['p_img'])){
                                    $img_arr = explode(',', $row['p_img']);
                                    $img_src = $img_arr[0];
                                }

                                // --- ส่วนคำนวณสีของสต็อก ---
                                $qty = $row['p_qty'];
                                if($qty <= 0) {
                                    $qty_display = '<span class="qty-text qty-out"><i class="bi bi-exclamation-octagon me-1"></i>หมด</span>';
                                } elseif($qty < 5) {
                                    $qty_display = '<span class="qty-text qty-low">'.$qty.' <small>(ใกล้หมด)</small></span>';
                                } else {
                                    $qty_display = '<span class="qty-text qty-ok">'.$qty.'</span>';
                                }
                        ?>
                        <tr>
                            <td class="text-center text-muted fw-bold">#<?= $row['p_id']; ?></td>
                            <td>
                                <?php if(!empty($img_src)): ?> 
                                    <img src="<?= $img_src; ?>" class="img-thumb shadow-sm">
                                <?php else: ?>
                                    <div class="img-thumb bg-light d-flex align-items-center justify-content-center text-muted small border">No Pic</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <h6 class="fw-bold mb-0 text-dark"><?= $row['p_name']; ?></h6>
                                <small class="text-muted text-truncate d-block" style="max-width: 180px;"><?= $row['p_detail']; ?></small>
                            </td>
                            <td>
                                <div class="mb-1">
                                    <span class="badge bg-dark fw-light" style="font-size: 0.7rem;">
                                        <?= $row['c_name'] ?? 'ไม่มีหมวด'; ?>
                                    </span>
                                    <?php 
                                        $badge_cls = ($row['p_type'] == 'male') ? 'bg-primary' : (($row['p_type'] == 'female') ? 'bg-danger' : 'bg-success');
                                        $type_txt = ($row['p_type'] == 'male') ? 'M' : (($row['p_type'] == 'female') ? 'W' : 'U');
                                    ?>
                                    <span class="badge <?= $badge_cls; ?> bg-opacity-75" style="font-size: 0.7rem;"><?= $type_txt; ?></span>
                                </div>
                                <div class="d-flex flex-wrap">
                                    <?php 
                                    if(!empty($row['p_size'])) {
                                        $sizes = explode(',', $row['p_size']);
                                        foreach(array_slice($sizes, 0, 3) as $s) { // โชว์แค่ 3 ไซส์แรกเพื่อความสะอาด
                                            echo '<span class="badge badge-size rounded-pill">'.$s.'</span>';
                                        }
                                        if(count($sizes) > 3) echo '<span class="text-muted small">...</span>';
                                    }
                                    ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <?= $qty_display; ?>
                            </td>

                            <td class="text-end">
                                <span class="price-tag">฿<?= number_format($row['p_price']); ?></span>
                            </td>
                            
                            <td class="text-center">
                                <div class="btn-group shadow-sm">
                                    <a href="admin_edit.php?id=<?= $row['p_id']; ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?delete_id=<?= $row['p_id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('ยืนยันที่จะลบสินค้า?');">
                                        <i class="bi bi-trash"></i>
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
                                <h5 class="fw-light text-secondary">ยังไม่มีสินค้าในระบบ</h5>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>