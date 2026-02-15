<?php
session_start();
include_once("connectdb.php");

if (isset($_POST['Submit'])) {
    $uid = $_SESSION['uid']; 
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // --- 1. ตรวจสอบ Email ซ้ำ (ยกเว้น Email ตัวเอง) ---
    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND id != '$uid'");
    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('อีเมลนี้มีผู้ใช้งานอื่นใช้แล้ว กรุณาใช้อีเมลใหม่'); window.history.back();</script>";
        exit();
    }

    // --- 2. อัปเดตข้อมูลตาราง users ---
    $sql_user = "UPDATE users SET name = '$name', email = '$email' WHERE id = '$uid'";
    mysqli_query($conn, $sql_user);

    // --- 3. จัดการรหัสผ่าน ---
    if (!empty($new_password)) {
        if ($new_password === $confirm_password) {
            $sql_pass = "UPDATE users SET password = '$new_password' WHERE id = '$uid'";
            mysqli_query($conn, $sql_pass);
        } else {
            echo "<script>alert('รหัสผ่านยืนยันไม่ตรงกัน'); window.history.back();</script>";
            exit();
        }
    }

    // --- 4. จัดการตาราง address (เช็ค Insert/Update) ---
    $check_addr = mysqli_query($conn, "SELECT address_id FROM address WHERE user_id = '$uid'");
    if (mysqli_num_rows($check_addr) > 0) {
        // อัปเดตเบอร์โทร
        $sql_addr = "UPDATE address SET phone = '$phone' WHERE user_id = '$uid'";
    } else {
        // เพิ่มข้อมูลใหม่
        $sql_addr = "INSERT INTO address (user_id, fullname, phone) VALUES ('$uid', '$name', '$phone')";
    }
    
    if (mysqli_query($conn, $sql_addr)) {
        echo "<script>alert('บันทึกข้อมูลเรียบร้อยแล้ว'); window.location='setting.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>