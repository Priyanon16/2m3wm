<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

$id = intval($_GET['id'] ?? 0);

$sql = "SELECT * FROM products WHERE p_id = '$id' LIMIT 1";
$rs  = mysqli_query($conn,$sql);
$product = mysqli_fetch_assoc($rs);

if(!$product){
    echo "ไม่พบสินค้า";
    exit;
}
?>

<?php include("header.php"); ?>

<div class="container mt-5 mb-5">

<div class="row">

<!-- รูปสินค้า -->
<div class="col-md-6">
    <img src="<?= $product['p_img'] ?>" 
         class="img-fluid rounded shadow">
</div>

<!-- รายละเอียด -->
<div class="col-md-6">

<h3 class="fw-bold">
<?= htmlspecialchars($product['p_name']) ?>
</h3>

<p class="text-muted">
ประเภท: <?= htmlspecialchars($product['p_type']) ?>
</p>

<h4 class="text-warning mb-3">
฿<?= number_format($product['p_price'],2) ?>
</h4>

<p>
ไซซ์: <?= htmlspecialchars($product['p_size']) ?>
</p>

<hr>

<p>
<?= nl2br(htmlspecialchars($product['p_detail'])) ?>
</p>

<div class="mt-4 d-flex gap-3">
    <a href="cart.php?add=<?= $product['p_id'] ?>" 
       class="btn btn-warning">
       เพิ่มลงตะกร้า
    </a>

    <a href="favorite.php?add=<?= $product['p_id'] ?>" 
       class="btn btn-outline-danger">
       เพิ่มรายการโปรด
    </a>
</div>

</div>
</div>
</div>
