<?php
include("connectdb.php");

if(isset($_POST['save'])){

$title = $_POST['title'];
$subtitle = $_POST['subtitle'];
$desc = $_POST['description'];
$link = $_POST['link'];
$status = 1;

$image = $_FILES['image']['name'];
$tmp = $_FILES['image']['tmp_name'];

move_uploaded_file($tmp,"../images/".$image);

$sql = "INSERT INTO popup(title,subtitle,description,image,link,status)
VALUES('$title','$subtitle','$desc','$image','$link','$status')";

mysqli_query($conn,$sql);

echo "<script>alert('เพิ่มโปรโมชั่นสำเร็จ');</script>";

}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>เพิ่ม Popup โปรโมชั่น</title>

<style>

body{
    font-family: Arial;
    background: linear-gradient(135deg,#1e1e1e,#444);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.box{
    background:white;
    padding:35px;
    width:420px;
    border-radius:10px;
    box-shadow:0 10px 30px rgba(0,0,0,0.4);
}

h2{
    text-align:center;
    margin-bottom:20px;
}

input,textarea{
    width:100%;
    padding:10px;
    margin-bottom:15px;
    border:1px solid #ccc;
    border-radius:6px;
}

textarea{
    height:100px;
}

button{
    width:100%;
    padding:12px;
    background:#ff2b2b;
    color:white;
    border:none;
    border-radius:6px;
    font-size:16px;
    cursor:pointer;
}

button:hover{
    background:#cc0000;
}

</style>

</head>

<body>

<div class="box">

<h2>เพิ่ม Popup โปรโมชั่น</h2>

<form method="post" enctype="multipart/form-data">

ชื่อโปรโมชั่น
<input type="text" name="title" required>

หัวข้อย่อย
<input type="text" name="subtitle">

รายละเอียด
<textarea name="description"></textarea>

ลิงก์โปรโมชั่น
<input type="text" name="link">

รูปภาพโปรโมชั่น
<input type="file" name="image" required>

<button name="save">บันทึกโปรโมชั่น</button>

</form>

</div>

</body>
</html>