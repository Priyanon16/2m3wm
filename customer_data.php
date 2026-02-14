<?php
include_once("check_login.php");
include_once("connectdb.php");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการลูกค้า - 2M3WM ADMIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f8f9fa; }
        header { background: #111; padding: 1rem 0; color: white; }
        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<header>
    <div class="container d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-people me-2"></i>จัดการลูกค้า</h4>
        <a href="index.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-house-door"></i> กลับหน้าหลัก
        </a>
    </div>
</header>

<div class="container mt-4">
    <div class="table-container">
        <h5 class="fw-bold mb-4">รายชื่อสมาชิกทั้งหมด</h5>

        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th width="10%">ID</th>
                    <th width="30%">ชื่อ-นามสกุล</th>
                    <th width="30%">อีเมล</th>
                    <th width="20%">วันที่สมัคร</th>
                    <th width="10%" class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $sql = "SELECT id, name, email, created_at 
                        FROM users 
                        WHERE role = 'member' 
                        ORDER BY id DESC";

                $result = mysqli_query($conn, $sql);

                while($row = mysqli_fetch_assoc($result)) {
                ?>

                <tr>
                    <td><?= $row['id']; ?></td>
                    <td class="fw-bold"><?= $row['name']; ?></td>
                    <td><?= $row['email']; ?></td>
                    <td>
                        <small class="text-muted">
                            <?= date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                        </small>
                    </td>

                    <td class="text-center">

                        <!-- ปุ่มแก้ไข -->
                        <a href="edit_customer_data.php?id=<?= $row['id']; ?>" 
                           class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <!-- ปุ่มลบ -->
                        <a href="delete_data.php?id=<?= $row['id']; ?>" 
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('คุณต้องการลบสมาชิกคนนี้หรือไม่?');">
                            <i class="bi bi-trash"></i>
                        </a>

                    </td>
                </tr>

                <?php } ?>

            </tbody>
        </table>

    </div>
</div>

</body>
</html>
