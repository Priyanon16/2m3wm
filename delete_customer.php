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
    // รับ ID ของลูกค้าและทำความสะอาดข้อมูลป้องกัน SQL Injection
    $u_id = mysqli_real_escape_string($conn, $_GET['id']);

    // คำสั่ง SQL สำหรับลบข้อมูลลูกค้า โดยระบุ role เพื่อความปลอดภัย
    $sql = "DELETE FROM users WHERE id = '$u_id' AND role = 'member'";

    if (mysqli_query($conn, $sql)) {
        // แจ้งเตือนเมื่อลบสำเร็จ
        echo "<script>
            Swal.fire({
                title: 'ลบข้อมูลสำเร็จ!',
                text: 'ข้อมูลลูกค้าถูกลบออกจากระบบเรียบร้อยแล้ว',
                icon: 'success',
                confirmButtonColor: '#ff7a00',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href='customer_data.php';
                }
            });
        </script>";
    } else {
        // แจ้งเตือนเมื่อเกิดข้อผิดพลาด
        $error_msg = mysqli_error($conn);
        echo "<script>
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถลบข้อมูลได้: $error_msg',
                icon: 'error',
                confirmButtonColor: '#212529',
                confirmButtonText: 'กลับไปตรวจสอบ'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href='customer_data.php';
                }
            });
        </script>";
    }
} else {
    // หากไม่มีการส่ง ID มา ให้กลับไปหน้าจัดการลูกค้า
    header("Location: customer_data.php");
}

mysqli_close($conn);
echo '</body></html>';
?>