<?php
session_start();

// 1. ตรวจสอบว่ามีการล็อกอินหรือไม่
// 2. ตรวจสอบว่า Role ที่ล็อกอินเข้ามาเป็น 'admin' หรือไม่
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // หากไม่ใช่ Admin ให้เด้งไปหน้า Login และหยุดการทำงานทันที
    echo "<script>
            alert('สิทธิ์การเข้าถึงเฉพาะผู้ดูแลระบบเท่านั้น!');
            window.location.href='login.php';
          </script>";
    exit();
}

include_once("connectdb.php");
// ... โค้ดส่วนที่เหลือของคุณ ...
?>