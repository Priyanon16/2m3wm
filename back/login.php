<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ปรียานนท์ กรุตนิด(มินนี่)</title>
</head>
<body>
    
<h1>เข้าสู่ระบบหลังบ้าน - ปรียานนท์</h1>
<form method="post" action="">
    Username <input type="text" name="auser" autofocus required><br>
    Password <input type="password" name="apwd" required><br>
    <button type="submit" name="Submit">LOGIN</button>
</form>

<?php
if(isset($_POST['Submit'])) {
    include_once("connectdb.php");
    $sql = "SELECT * FROM admin WHERE a_username='{$_POST['auser']}' AND a_password='{$_POST['apwd']}' LIMIT 1 ";
    $rs = mysqli_query($conn,$sql);
    $num = mysqli_num_rows($rs);

    if($num == 1) {
        $data = mysqli_fetch_array($rs);
        $_SESSION['aid'] = $data['a_id'];
        $_SESSION['aname'] = $data['a_name'];
        echo"<script>" ;
        echo "window.location='index.php';";
        echo "</script>";
    } else {
        echo"<script>" ;
        echo "alert('Username หรือ Password ไม่ถูกต้อง');";
        echo "</script>";
    }

}
?>


</body>
</html>