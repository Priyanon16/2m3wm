<?php
include_once("check_login.php");
include_once("connectdb.php");
include_once("bootstrap.php");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการลูกค้า - 2M3WM ADMIN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body { background-color: #f8f9fa; font-family: 'Kanit', sans-serif; }
        
        /* สไตล์สำหรับเนื้อหาหลักที่อยู่ข้าง Sidebar */
        .content { 
            flex: 1; 
            min-height: 100vh; 
            padding: 30px; 
            background-color: #f8f9fa;
        }

        .main-card { 
            background: #fff; 
            border-radius: 16px; 
            padding: 25px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            border: none; 
        }

        .brand-accent { color: #ff7a00; }
        
        /* Sidebar CSS Essential */
        .sidebar {
            width: 280px; min-height: 100vh; background: #212529; color: #fff;
            transition: all 0.3s ease; display: flex; flex-direction: column;
        }
        .sidebar.collapsed { width: 80px; }
        .sidebar.collapsed span, .sidebar.collapsed .logo-text, .sidebar.collapsed .user-text { display: none; }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include_once("sidebar.php"); ?>

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold mb-0">
                <i class="bi bi-people-fill me-2 brand-accent"></i> จัดการข้อมูลลูกค้า
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index_admin.php" class="text-decoration-none text-muted">หน้าแรก</a></li>
                    <li class="breadcrumb-item active">จัดการลูกค้า</li>
                </ol>
            </nav>
        </div>

        <div class="main-card">
            <h5 class="fw-bold mb-4">รายชื่อสมาชิกทั้งหมด (Member)</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="10%" class="ps-4">ID</th>
                            <th width="30%">ชื่อ-นามสกุล</th>
                            <th width="30%">อีเมล</th>
                            <th width="20%">วันที่สมัคร</th>
                            <th width="10%" class="text-center pe-4">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, name, email, created_at 
                                FROM users 
                                WHERE role = 'member' 
                                ORDER BY id DESC";
                        $result = mysqli_query($conn, $sql);

                        if(mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted"><?= $row['id']; ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?= date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                </small>
                            </td>
                            <td class="text-center pe-4">
                                <div class="btn-group">
                                    <a href="edit_customer.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-dark">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="delete_customer.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('คุณต้องการลบสมาชิก [<?= $row['name']; ?>] ใช่หรือไม่?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>ไม่มีข้อมูลลูกค้าในระบบ</td></tr>";
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