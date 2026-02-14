<?php
session_start();
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>SNEAKERHUB - Login</title>
</head>
<body>

<h2>เข้าสู่ระบบ SNEAKERHUB</h2>

<form method="post" action="">
    Email :
    <input type="email" name="email" required autofocus><br><br>

    Password :
    <input type="password" name="password" required><br><br>

    <button type="submit" name="Submit">LOGIN</button>
</form>

<?php
if(isset($_POST['Submit'])) {

    include_once("connectdb.php");  // ไฟล์เชื่อมต่อฐานข้อมูล

    $sql = "SELECT * FROM users 
            WHERE email='{$_POST['email']}' 
            AND password='{$_POST['password']}' 
            LIMIT 1";

    $rs = mysqli_query($conn,$sql);
    $num = mysqli_num_rows($rs);

    if($num == 1) {

        $data = mysqli_fetch_array($rs);

        $_SESSION['aid'] = $data['id'];
        $_SESSION['aname'] = $data['name'];

        echo "<script>";
        echo "window.location='index.php';";
        echo "</script>";

    } else {

        echo "<script>";
        echo "alert('Email หรือ Password ไม่ถูกต้อง');";
        echo "</script>";
    }
}
?>

</body>
</html>
