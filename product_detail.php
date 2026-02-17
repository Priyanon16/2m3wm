<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

$id = intval($_GET['id'] ?? 0);

if($id <= 0){
    echo "ไม่พบสินค้า";
    exit;
}

/* ดึงข้อมูลสินค้า */
$sql = "
SELECT p.*, b.brand_name, c.c_name
FROM products p
LEFT JOIN brand b ON p.brand_id = b.brand_id
LEFT JOIN category c ON p.c_id = c.c_id
WHERE p.p_id = $id
LIMIT 1
";

$rs  = mysqli_query($conn,$sql);
$product = mysqli_fetch_assoc($rs);

if(!$product){
    echo "ไม่พบสินค้า";
    exit;
}

/* ดึงรูปทั้งหมด */
$img_rs = mysqli_query($conn,"
SELECT img_path 
FROM product_images 
WHERE p_id = $id
");

$images = [];
while($img = mysqli_fetch_assoc($img_rs)){
    $images[] = $img['img_path'];
}

/* แยกไซส์ */
$sizes = [];
if(!empty($product['p_size'])){
    $sizes = explode(",",$product['p_size']);
}

include("header.php");
?>

<style>
.size-btn{
    border:1px solid #ddd;
    padding:8px 14px;
    margin:5px;
    border-radius:8px;
    cursor:pointer;
    background:#fff;
    transition:.2s;
    min-width:60px;
}

.size-btn:hover{
    border-color:#ff7a00;
}
.size-btn.active{
    background:#111;
    color:#fff;
    border-color:#111;
}
.qty-box{
    display:flex;
    align-items:center;
    border:1px solid #ddd;
    width:140px;
    border-radius:8px;
    overflow:hidden;
}
.qty-box button{
    width:40px;
    border:none;
    background:#f5f5f5;
    font-size:18px;
}
.qty-box input{
    width:60px;
    text-align:center;
    border:none;
}
.buy-btn{
    background:#ff7a00;
    color:#fff;
    border:none;
}
.buy-btn:hover{
    background:#e96b00;
}
</style>

<div class="container py-5">
<div class="row">

<!-- รูปสินค้า -->
<div class="col-md-6">

<?php if(count($images)>0): ?>
<div id="carouselExample" class="carousel slide">
  <div class="carousel-inner">
    <?php foreach($images as $key=>$img): ?>
    <div class="carousel-item <?= $key==0?'active':'' ?>">
      <img src="<?= htmlspecialchars($img) ?>" 
           class="d-block w-100 rounded"
           style="height:450px;object-fit:cover;">
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php else: ?>
<img src="images/no-image.png" class="img-fluid rounded">
<?php endif; ?>

</div>


<!-- รายละเอียด -->
<div class="col-md-6">

<h3 class="fw-bold"><?= htmlspecialchars($product['p_name']) ?></h3>

<p class="text-muted">
<?= htmlspecialchars($product['brand_name']) ?> |
<?= htmlspecialchars($product['c_name']) ?>
</p>

<h4 class="text-warning mb-3">
฿<?= number_format($product['p_price'],0) ?>
</h4>

<hr>

<!-- เลือกไซส์ -->
<h6 class="fw-bold">เลือกไซส์</h6>

<div id="sizeContainer" class="d-flex flex-wrap">
<?php foreach($sizes as $size): ?>
    <button type="button"
        class="size-btn"
        onclick="selectSize(this,'<?= trim($size) ?>')">
        <?= trim($size) ?>
    </button>
<?php endforeach; ?>
</div>


<input type="hidden" id="selectedSize" value="">

<hr>

<!-- เลือกจำนวน -->
<h6 class="fw-bold">จำนวน</h6>

<div class="qty-box mb-3">
    <button type="button" onclick="decrease()">-</button>
    <input type="text" id="qty" value="1" readonly>
    <button type="button" onclick="increase()">+</button>
</div>

<hr>

<p><?= nl2br(htmlspecialchars($product['p_detail'])) ?></p>

<!-- ปุ่มสั่งซื้อ -->
<button class="btn buy-btn btn-lg"
onclick="addToCart(<?= $product['p_id'] ?>)">
เพิ่มลงตะกร้า
</button>

<button class="btn btn-outline-danger btn-lg ms-2"
onclick="addToFav(<?= $product['p_id'] ?>)">
❤ เพิ่มรายการโปรด
</button>


</div>
</div>
</div>


<script>
function selectSize(el,size){
    document.querySelectorAll('.size-btn')
        .forEach(btn=>btn.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('selectedSize').value = size;
}

function increase(){
    let qty = document.getElementById('qty');
    qty.value = parseInt(qty.value)+1;
}

function decrease(){
    let qty = document.getElementById('qty');
    if(parseInt(qty.value)>1){
        qty.value = parseInt(qty.value)-1;
    }
}

function addToCart(id){
    let size = document.getElementById('selectedSize').value;
    let qty  = document.getElementById('qty').value;

    if(size==""){
        alert("กรุณาเลือกไซส์ก่อน");
        return;
    }

    window.location = "cart.php?add="+id+
                      "&size="+size+
                      "&qty="+qty;
}

function addToFav(id){
    window.location = "favorite.php?action=add&id="+id;
}


</script>
