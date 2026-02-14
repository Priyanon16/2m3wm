<?php
$products = [
  ["id"=>1,"name"=>"Nike Shox NZ","type"=>"รองเท้าผู้ชาย","price"=>5500,"img"=>"images/1.jpg"],
  ["id"=>2,"name"=>"Adidas Adizero","type"=>"รองเท้าผู้หญิง","price"=>6200,"img"=>"images/2.jpg"],
  ["id"=>3,"name"=>"Puma Run","type"=>"รองเท้าวิ่ง","price"=>4900,"img"=>"images/1.jpg"],
  ["id"=>4,"name"=>"Nike Air Zoom","type"=>"รองเท้าผู้ชาย","price"=>5800,"img"=>"images/2.jpg"],
  ["id"=>5,"name"=>"Adidas Ultraboost","type"=>"รองเท้าวิ่ง","price"=>7200,"img"=>"images/1.jpg"],
  ["id"=>6,"name"=>"Nike Pegasus","type"=>"รองเท้าวิ่ง","price"=>6500,"img"=>"images/2.jpg"]
];

$id = $_GET['id'] ?? 0;
$product = null;

foreach($products as $p){
  if($p['id'] == $id){
    $product = $p;
    break;
  }
}

if(!$product){
  echo "ไม่พบสินค้า";
  exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title><?= $product['name']; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
<style>
body{font-family:'Kanit',sans-serif;background:#f8f9fa;}
</style>
</head>

<body>
<div class="container py-5">
  <a href="index.php" class="text-decoration-none">&larr; กลับหน้าร้าน</a>

  <div class="row mt-4">
    <div class="col-md-6">
      <img src="<?= $product['img']; ?>" class="img-fluid rounded">
    </div>
    <div class="col-md-6">
      <h2><?= $product['name']; ?></h2>
      <p class="text-muted"><?= $product['type']; ?></p>
      <h4 class="text-warning">฿<?= number_format($product['price']); ?></h4>

      <button class="btn btn-dark mt-3">เพิ่มลงตะกร้า</button>
    </div>
  </div>
</div>
</body>
</html>
