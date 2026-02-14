<?php
// admin_product.php
session_start();

// ----------------------------------------------------
// 1. ส่วนเชื่อมต่อฐานข้อมูล (ใส่แทน include data.php)
// ----------------------------------------------------
$servername = "localhost";
$username = "admin_man";     // ⚠️ ถ้าขึ้น Server จริง ต้องแก้เป็น user ที่โฮสต์ให้มา
$password = "66010914015";         // ⚠️ ถ้าขึ้น Server จริง ต้องแก้เป็นรหัสผ่านที่โฮสต์ให้มา
$dbname = "2m3wm";      // ชื่อฐานข้อมูล

$conn = mysqli_connect($servername, $username, $password, $dbname);

// เช็คการเชื่อมต่อ
if (!$conn) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");
// ----------------------------------------------------


// --- ส่วนการลบสินค้า ---
if(isset($_GET['delete_id'])){
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $sql = "DELETE FROM products WHERE p_id = '$id'";
    if(mysqli_query($conn, $sql)){
        echo "<script>alert('ลบสินค้าเรียบร้อย'); window.location='admin_product.php';</script>";
    } else {
        echo "<script>alert('ลบสินค้าล้มเหลว: " . mysqli_error($conn) . "');</script>";
    }
}

// --- ดึงข้อมูลสินค้า + ชื่อหมวดหมู่ ---
// ใช้ LEFT JOIN เพื่อให้สินค้ายังโชว์อยู่ แม้จะหาหมวดหมู่ไม่เจอ
$sql = "SELECT p.*, c.c_name 
        FROM products p 
        LEFT JOIN category c ON p.c_id = c.c_id 
        ORDER BY p.p_id DESC";
$result = mysqli_query($conn, $sql);

// เช็คว่า Query ผ่านไหม
if (!$result) {
    die("Error fetching data: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>📦 จัดการสินค้า</h3>
            <a href="admin_add.php" class="btn btn-success">+ เพิ่มสินค้าใหม่</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">ID</th>
                            <th width="100">รูปภาพ</th>
                            <th>ชื่อสินค้า</th>
                            <th>ราคา</th>
                            <th>หมวดหมู่</th>
                            <th width="150">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // เช็คว่ามีข้อมูลสินค้าหรือไม่
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)){ 
                        ?>
                        <tr>
                            <td><?= $row['p_id']; ?></td>
                            <td>
                                <?php if(!empty($row['p_img'])): ?>
                                    <img src="<?= $row['p_img']; ?>" alt="img" style="width: 60px; height: 60px; object-fit: cover;" class="rounded">
                                <?php else: ?>
                                    <span class="text-muted">ไม่มีรูป</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= $row['p_name']; ?></strong><br>
                                <small class="text-muted"><?= mb_substr($row['p_detail'], 0, 30); ?>...</small>
                            </td>
                            <td class="text-primary fw-bold">฿<?= number_format($row['p_price']); ?></td>
                            
                            <td><span class="badge bg-secondary"><?= $row['c_name'] ?? '-'; ?></span></td>
                            
                            <td>
                                <a href="admin_edit.php?id=<?= $row['p_id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                <a href="?delete_id=<?= $row['p_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันที่จะลบสินค้านี้?');">ลบ</a>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                        ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    ยังไม่มีสินค้าในระบบ <br>
                                    <a href="admin_add.php">เพิ่มสินค้าแรกเลย!</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>