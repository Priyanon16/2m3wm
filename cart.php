<?php
session_start();
include "data.php";

$cart = $_SESSION['cart'] ?? [];

if(isset($_GET['remove'])){
  unset($_SESSION['cart'][$_GET['remove']]);
  header("Location: cart.php");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ตะกร้าสินค้า</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
<h3>ตะกร้าสินค้า</h3>

<?php if(empty($cart)){ ?>
<p>ไม่มีสินค้าในตะกร้า</p>
<?php } else { ?>

<table class="table">
<thead>
<tr>
<th>สินค้า</th>
<th>ราคา</th>
<th></th>
</tr>
</thead>
<tbody>

<?php 
$total = 0;
foreach($cart as $id => $qty):
  foreach($products as $p){
    if($p['id'] == $id){
      $total += $p['price'] * $qty;
?>
<tr>
<td><?= $p['name']; ?> (x<?= $qty ?>)</td>
<td>฿<?= number_format($p['price']); ?></td>
<td>
<a href="?remove=<?= $id ?>" class="btn btn-sm btn-danger">ลบ</a>
</td>
</tr>
<?php } } endforeach; ?>

</tbody>
</table>

<h5>รวมทั้งหมด: ฿<?= number_format($total); ?></h5>

<?php } ?>

</div>
</body>
</html>
