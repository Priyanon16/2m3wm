<?php
include_once("check_login.php"); 
include_once("connectdb.php"); 
include("bootstrap.php")
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการหมวดหมู่สินค้า - 2M3WM ADMIN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <style>
        body { background-color: #f8f9fa; font-family: 'Kanit', sans-serif; }
        .logo-text { font-size: 1.6rem; font-weight: 600; letter-spacing: 0.5px; }
        .brand-accent { color: #ff7a00; }

        /* Sidebar Essential CSS (ควรเก็บไว้เพื่อให้ Layout ทำงานได้) */
        .sidebar {
            width: 280px; min-height: 100vh; background: #212529; color: #fff;
            transition: all 0.3s ease; display: flex; flex-direction: column; overflow-x: hidden;
        }
        .sidebar.collapsed { width: 80px; }
        .sidebar.collapsed span, .sidebar.collapsed .logo-text, .sidebar.collapsed .user-text, .sidebar.collapsed .submenu-arrow { display: none; }
        .sidebar.collapsed .collapse { display: none !important; }
        .sidebar .nav-link { color: #fff; display: flex; align-items: center; gap: 12px; padding: 12px; transition: 0.2s; text-decoration: none; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,0.15); }
        .sidebar .nav-link.active { background: #ff7a00; color: #fff; border-radius: 8px; }
        .sidebar-toggle i { color: #ff7a00; font-size: 1.8rem; }

        /* Main Content Styling */
        .content { flex: 1; background: #f8f9fa; min-height: 100vh; padding: 30px; }
        .main-card { background: #fff; border-radius: 16px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none; }
        .badge-category { background-color: #fff3e0; color: #ff7a00; padding: 8px 15px; border-radius: 50px; font-weight: 500; }
        .btn-orange { background-color: #ff7a00; color: white; border-radius: 8px; border: none; transition: 0.3s; }
        .btn-orange:hover { background-color: #e66e00; transform: translateY(-2px); color: white; }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include_once("sidebar.php"); ?>

    <div class="content">
        <?php if(isset($_GET['success'])) { ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php
                    if($_GET['success']=="add") echo "เพิ่มหมวดหมู่ใหม่เรียบร้อยแล้ว";
                    if($_GET['success']=="edit") echo "แก้ไขข้อมูลหมวดหมู่เรียบร้อยแล้ว";
                    if($_GET['success']=="delete") echo "ลบหมวดหมู่สินค้าสำเร็จ";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold mb-0">
                <i class="bi bi-tags-fill me-2 brand-accent"></i> จัดการหมวดหมู่สินค้า
            </h2>
            <a href="add_category.php" class="btn btn-orange px-4 shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> เพิ่มหมวดหมู่ใหม่
            </a>
        </div>

        <div class="main-card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="10%" class="ps-4">ID</th>
                            <th width="40%">ชื่อหมวดหมู่</th>
                            <th width="25%">จำนวนสินค้า</th>
                            <th width="25%" class="text-center pe-4">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $sql = "SELECT * FROM category ORDER BY c_id DESC";
                        $result = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_array($result)) {
                                $current_id = $row['c_id'];
                    ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted"><?php echo $current_id; ?></td>
                            <td><span class="badge-category"><?php echo htmlspecialchars($row['c_name']); ?></span></td>
                            <td>
                                <?php
                                $count_sql = "SELECT COUNT(*) as total FROM products WHERE c_id = '$current_id'";
                                $count_result = mysqli_query($conn, $count_sql);
                                $count_data = mysqli_fetch_assoc($count_result);
                                echo number_format($count_data['total'] ?? 0) . " รายการ";
                                ?>
                            </td>
                            <td class="text-center pe-4">
                                <a href="edit_category.php?id=<?php echo $current_id; ?>" class="btn btn-sm btn-outline-dark me-2">
                                    <i class="bi bi-pencil-square"></i> แก้ไข
                                </a>
                                <a href="delete_category.php?id=<?php echo $current_id; ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('คุณต้องการลบหมวดหมู่ [<?php echo htmlspecialchars($row['c_name']); ?>] หรือไม่?');">
                                    <i class="bi bi-trash"></i> ลบ
                                </a>
                            </td>
                        </tr>
                    <?php 
                            } 
                        } else {
                            echo '<tr><td colspan="4" class="text-center py-5 text-muted">ยังไม่มีข้อมูลในระบบ</td></tr>';
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="text-center mt-5 text-muted">
            <small>&copy; 2026 2M3WM SNEAKER HUB. All rights reserved.</small>
        </footer>
    </div>
</div>

</body>
</html>