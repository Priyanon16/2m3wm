<?php
include("connectdb.php");

/* =========================
   ดึง popup ล่าสุด
========================= */

$sql = "SELECT * FROM popup ORDER BY promo_id DESC LIMIT 1";
$result = mysqli_query($conn,$sql);
$data = mysqli_fetch_assoc($result);


/* =========================
   บันทึกข้อมูล
========================= */

if(isset($_POST['save'])){

$title = $_POST['title'];
$subtitle = $_POST['subtitle'];
$desc = $_POST['description'];
$status = 1;

$image = $_FILES['image']['name'];
$tmp = $_FILES['image']['tmp_name'];

/* ถ้ามี popup อยู่แล้ว → UPDATE */

if($data){

$id = $data['promo_id'];

/* ถ้ามีการอัปโหลดรูปใหม่ */

if(!empty($image)){

move_uploaded_file($tmp,"../images/".$image);

$sql = "UPDATE popup SET
title='$title',
subtitle='$subtitle',
description='$desc',
image='$image',
status='$status'
WHERE promo_id='$id'";

}

/* ถ้าไม่ได้อัปโหลดรูป */

else{

$sql = "UPDATE popup SET
title='$title',
subtitle='$subtitle',
description='$desc',
status='$status'
WHERE promo_id='$id'";

}

mysqli_query($conn,$sql);

}

/* ถ้ายังไม่มี popup → INSERT */

else{

if(!empty($image)){
move_uploaded_file($tmp,"../images/".$image);
}

$sql = "INSERT INTO popup(title,subtitle,description,image,status)
VALUES('$title','$subtitle','$desc','$image','$status')";

mysqli_query($conn,$sql);

}

/* reload หน้า */

echo "<script>
alert('บันทึกสำเร็จ');
window.location.href=window.location.href;
</script>";

exit();

}
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
min-height:100vh;
display:flex;
justify-content:center;
align-items:flex-start;
padding:40px 0;
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
<input type="text" name="title"
value="<?php echo isset($data['title']) ? $data['title'] : ''; ?>" required>

หัวข้อย่อย
<input type="text" name="subtitle"
value="<?php echo isset($data['subtitle']) ? $data['subtitle'] : ''; ?>">

รายละเอียด
<textarea name="description"><?php echo isset($data['description']) ? $data['description'] : ''; ?></textarea>

<?php if(!empty($data['image'])){ ?>

รูปปัจจุบัน : <b><?php echo $data['image']; ?></b><br><br>

<img src="/2m3wm/images/<?php echo $data['image']; ?>" width="250">

<?php } ?>

อัปโหลดรูปใหม่
<input type="file" name="image">

<button type="submit" name="save">บันทึกโปรโมชั่น</button>

</form>

</div>

</body>
</html>