<?php
include "connectdb.php";

$password = password_hash("123456", PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name,email,password)
        VALUES ('Admin','admin@test.com','$password')";

mysqli_query($conn,$sql);

echo "เพิ่มผู้ใช้เรียบร้อย";

?>
