<?php
include_once("connectdb.php");
include_once("check_login.php");

if (isset($_GET['id'])) {
    $cat_id = mysqli_real_escape_string($conn, $_GET['id']);

    // คำสั่งลบข้อมูล
    $sql = "DELETE FROM category WHERE cat_id = '$cat_id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('ลบหมวดหมู่สินค้าเรียบร้อยแล้ว');
                window.location.href='category_products.php';
              </script>";
    } else {
        echo "<script>
                alert('ไม่สามารถลบได้: " . mysqli_error($conn) . "');
                window.location.href='category_products.php';
              </script>";
    }
} else {
    header("Location: category_products.php");
}

mysqli_close($conn);
?>