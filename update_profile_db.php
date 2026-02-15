<?php
session_start();
include_once("connectdb.php");

if (isset($_POST['Submit'])) {
    $uid = $_SESSION['uid'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    $province = mysqli_real_escape_string($conn, $_POST['province']);
    $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $new_password = $_POST['new_password'];

    // 1. อัปเดตข้อมูลตาราง users
    $sql_user = "UPDATE users SET name = '$name' WHERE id = '$uid'";
    mysqli_query($conn, $sql_user);

    // 2. ถ้ามีการเปลี่ยนรหัสผ่าน
    if (!empty($new_password) && $new_password === $_POST['confirm_password']) {
        $sql_pass = "UPDATE users SET password = '$new_password' WHERE id = '$uid'";
        mysqli_query($conn, $sql_pass);
    }

    // 3. จัดการตาราง address (ตรวจสอบว่าเคยมีที่อยู่หรือยัง)
    $check_addr = mysqli_query($conn, "SELECT address_id FROM address WHERE user_id = '$uid'");
    
    if (mysqli_num_rows($check_addr) > 0) {
        // มีอยู่แล้วให้ UPDATE
        $sql_addr = "UPDATE address SET phone = '$phone', address = '$address', 
                     district = '$district', province = '$province', postal_code = '$postal_code' 
                     WHERE user_id = '$uid'";
    } else {
        // ยังไม่มีให้ INSERT
        $sql_addr = "INSERT INTO address (user_id, fullname, phone, address, district, province, postal_code) 
                     VALUES ('$uid', '$name', '$phone', '$address', '$district', '$province', '$postal_code')";
    }
    
    if (mysqli_query($conn, $sql_addr)) {
        echo "<script>alert('บันทึกข้อมูลเรียบร้อยแล้ว'); window.location='setting.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>