<?php
   include_once("check_login.php")
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการออเดอร์ - ปรียานนท์</title>
</head>
<body>

<h1>จัดการออเดอร์ - ปรียานนท์</h1>

<?php echo "แอดมิน: ".$_SESSION['aname']; ?> <br>

<ul>
    <a href="products.php"><li>จัดการสินค้า</li></a>
    <a href="orders.php"><li>จัดการออเดอร์</li></a>
    <a href="customers.php"><li>จัดการลูกค้า</li></a>
    <a href="logout.php"><li>ออกจากระบบ</li></a>
</ul>

    
    
</body>
</html>