<?php
include_once("connectdb.php");

if (isset($_POST['Submit'])) {
    // รับค่าจากฟอร์ม โดยใช้ชื่อ name ให้ตรงกับ input (c_name, c_details)
    $name = $_POST['c_name'];
    $details = $_POST['c_details'];

    // SQL สำหรับเพิ่มข้อมูล (ใช้ชื่อคอลัมน์ตามที่คุณแคปมา)
    $sql = "INSERT INTO category (c_name, c_details) VALUES ('$name', '$details')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('บันทึกสำเร็จ'); window.location='category_products.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>