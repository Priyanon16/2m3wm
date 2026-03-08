<?php
include("connectdb.php");

/* ดึง popup เดิม */

$result = mysqli_query($conn,"SELECT * FROM popup LIMIT 1");
$data = mysqli_fetch_assoc($result);

/* บันทึก */

if(isset($_POST['save'])){

$title = $_POST['title'];
$subtitle = $_POST['subtitle'];
$desc = $_POST['description'];
$status = 1;

$image = $_FILES['image']['name'];
$tmp = $_FILES['image']['tmp_name'];

/* ถ้ามี popup อยู่แล้ว */

if($data){

$id = $data['promo_id'];

if($image != ""){

move_uploaded_file($tmp,"../images/".$image);

$sql = "UPDATE popup SET
title='$title',
subtitle='$subtitle',
description='$desc',
image='$image',
status='$status'
WHERE promo_id='$id'";

}else{

$sql = "UPDATE popup SET
title='$title',
subtitle='$subtitle',
description='$desc',
status='$status'
WHERE promo_id='$id'";

}

mysqli_query($conn,$sql);

echo "<script>alert('แก้ไขโปรโมชั่นสำเร็จ');</script>";

}

/* ถ้ายังไม่มี popup */

else{

move_uploaded_file($tmp,"../images/".$image);

$sql = "INSERT INTO popup(title,subtitle,description,image,status)
VALUES('$title','$subtitle','$desc','$image','$status')";

mysqli_query($conn,$sql);

echo "<script>alert('เพิ่มโปรโมชั่นสำเร็จ');</script>";

}

}

/* โหลดข้อมูลใหม่ */

$result = mysqli_query($conn,"SELECT * FROM popup LIMIT 1");
$data = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>ตั้งค่า Popup โปรโมชั่น</title>

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

img{
width:100%;
margin-bottom:10px;
border-radius:6px;
}

</style>

</head>

<body>

<div class="box">

<h2>ตั้งค่า Popup โปรโมชั่น</h2>

<form method="post" enctype="multipart/form-data">

ชื่อโปรโมชั่น
<input type="text" name="title" value="<?php echo $data['title'] ?? ''; ?>" required>

หัวข้อย่อย
<input type="text" name="subtitle" value="<?php echo $data['subtitle'] ?? ''; ?>">

รายละเอียด
<textarea name="description"><?php echo $data['description'] ?? ''; ?></textarea>

<?php if(!empty($data['image'])){ ?>

รูปปัจจุบัน
<img src="../images/<?php echo $data['image']; ?>">

<?php } ?>

อัปโหลดรูปใหม่
<input type="file" name="image">

<button name="save">บันทึกโปรโมชั่น</button>

</form>

</div>

</body>
</html>