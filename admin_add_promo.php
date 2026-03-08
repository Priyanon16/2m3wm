<?php
include("connectdb.php");

if(isset($_POST['save'])){

$title = $_POST['title'];
$subtitle = $_POST['subtitle'];
$desc = $_POST['description'];
$link = $_POST['link'];

$image = $_FILES['image']['name'];
$tmp = $_FILES['image']['tmp_name'];

move_uploaded_file($tmp,"../images/".$image);

mysqli_query($conn,"
INSERT INTO promotions(title,subtitle,description,image,link)
VALUES('$title','$subtitle','$desc','$image','$link')
");

echo "เพิ่มโปรโมชั่นสำเร็จ";
}
?>

<form method="post" enctype="multipart/form-data">

ชื่อโปร
<input type="text" name="title"><br>

หัวข้อย่อย
<input type="text" name="subtitle"><br>

รายละเอียด
<textarea name="description"></textarea><br>

ลิงก์
<input type="text" name="link"><br>

รูปภาพ
<input type="file" name="image"><br>

<button name="save">บันทึก</button>

</form>