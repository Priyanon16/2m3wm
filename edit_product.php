<?php 
    include_once("check_login.php");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>จัดการสินค้า</title>
</head>

<body>
<h1>จัดการออเดอร์</h1>
<?php echo "แอดมิน:".$_SESSION['aname']; ?> <br>  
<ui>
    <a href = "products.php"><li>จัดการสินค้า<li></a>
    <a href = "orders.php"><li>จัดการออเดอร์<li></a>
    <a href = "customers.php"><li>จัดการลูกค้า<li></a>
    <a href = "logout.php"><li>ออกจากระบบ<li></a>
</ui>
</body>
</html>