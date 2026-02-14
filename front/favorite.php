<?php
session_start();
include "data.php";

$fav = $_SESSION['favorite'] ?? [];

if(isset($_GET['remove'])){
  unset($_SESSION['favorite'][$_GET['remove']]);
  header("Location: favorite.php");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>รายการโปรด</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
<h3>รายการโปรด</h3>

<?php if(empty($fav)){ ?>
<p>ยังไม่มีสินค้าในรายการโปรด</p>
<?php } else { ?>

<div class="row">
<?php foreach($fav as $id):
  foreach($products as $p){
    if($p['id'] == $id){
?>
<div class="col-md-4">
<div class="card mb-3">
<img src="<?= $p['img']; ?>" class="card-img-top">
<div class="card-body">
<h6><?= $p['name']; ?></h6>
<p>฿<?= number_format($p['price']); ?></p>
<a href="?remove=<?= $id ?>" class="btn btn-sm btn-danger">ลบ</a>
</div>
</div>
</div>
<?php } } endforeach; ?>
</div>

<?php } ?>

</div>
</body>
</html>
