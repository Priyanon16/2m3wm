<?php
include_once("connectdb.php");
include_once("check_login.php");

// ส่วนหัวสำหรับเรียกใช้งาน SweetAlert2 และ Font Kanit
echo '<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: "Kanit", sans-serif; background-color: #f8f9fa; }
    </style>
</head>
<body>';

if (isset($_GET['id'])) {
    // ใช้ตัวแปร $c_id เพื่อให้สอดคล้องกับฐานข้อมูล
    $c_id = mysqli_real_escape_string($conn, $_GET['id']);

    // แก้ไขคำสั่ง SQL: เปลี่ยนจาก cat_id เป็น c_id ตามโครงสร้างจริงของคุณ
    $sql = "DELETE FROM category WHERE c_id = '$c_id'";

    if (mysqli_query($conn, $sql)) {
        // แจ้งเตือนสำเร็จ
        echo "<script>
            Swal.fire({
                title: 'ลบสำเร็จ!',
                text: 'หมวดหมู่สินค้าถูกลบเรียบร้อยแล้ว',
                icon: 'success',
                confirmButtonColor: '#ff5722',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href='category_products.php';
                }
            });
        </script>";
    } else {
        // แจ้งเตือนเมื่อเกิดข้อผิดพลาด (เช่น ติดเงื่อนไข Foreign Key)
        $error_msg = mysqli_error($conn);
        echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถลบได้: $error_msg',
                icon: 'error',
                confirmButtonColor: '#111',
                confirmButtonText: 'กลับไปตรวจสอบ'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href='category_products.php';
                }
            });
        </script>";
    }
} else {
    header("Location: category_products.php");
}

mysqli_close($conn);
echo '</body></html>';
?>