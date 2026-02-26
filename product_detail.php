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

/* ดึง stock ต่อไซส์จาก product_stock */
$sizeStocks = [];

$size_rs = mysqli_query($conn,"
SELECT p_size, p_qty_stock 
FROM product_stock 
WHERE p_id = $id
");

while($row = mysqli_fetch_assoc($size_rs)){
    $sizeStocks[$row['p_size']] = $row['p_qty_stock'];
}

/* =========================
   ดึงสินค้าแนะนำ (หมวดเดียวกัน)
========================= */
$cat_id = intval($product['c_id']);

$rec_sql = "
SELECT p.*, 
       (SELECT img_path FROM product_images 
        WHERE p_id = p.p_id LIMIT 1) AS main_img
FROM products p
WHERE p.c_id = $cat_id
AND p.p_id != $id
ORDER BY RAND()
LIMIT 4
";

$rec_rs = mysqli_query($conn, $rec_sql);


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
    background:#FFC107;   /* สีเหลืองแบบในรูป */
    color:#000;           /* ตัวหนังสือสีดำ */
    border:none;
    border-radius:40px;   /* โค้งมากแบบ pill */
    padding:14px 40px;
    font-weight:700;
    font-size:18px;
    transition:0.2s;
}

.buy-btn:hover{
    background:#e6ac00;   /* เหลืองเข้มขึ้นตอน hover */
}
/* [เพิ่ม CSS] สำหรับปุ่มที่กดไม่ได้ */
.btn-disabled {
    background: #ccc !important;
    cursor: not-allowed;
}

.product-detail {
    white-space: pre-line;
    word-wrap: break-word;
    overflow-wrap: break-word;
    line-height: 1.7;
}
.detail-title{
    margin-bottom: 4px;
}

.product-detail{
    margin-top: 0;
    line-height: 1.6;
    word-break: break-word;
}


</style>

<div class="container py-5">
<div class="row">

<div class="col-md-6">

<?php if(count($images)>0): ?>
<div id="carouselExample" class="carousel slide" data-bs-ride="carousel">

  <!-- จุดเลื่อนด้านล่าง -->
  <div class="carousel-indicators">
    <?php foreach($images as $key=>$img): ?>
      <button type="button"
              data-bs-target="#carouselExample"
              data-bs-slide-to="<?= $key ?>"
              class="<?= $key==0?'active':'' ?>">
      </button>
    <?php endforeach; ?>
  </div>

  <div class="carousel-inner">
    <?php foreach($images as $key=>$img): ?>
    <div class="carousel-item <?= $key==0?'active':'' ?>">
      <img src="<?= htmlspecialchars($img) ?>"
           class="d-block w-100 rounded"
           style="height:450px;object-fit:cover;">
    </div>
    <?php endforeach; ?>
  </div>

  <!-- ปุ่มซ้าย -->
  <button class="carousel-control-prev"
          type="button"
          data-bs-target="#carouselExample"
          data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>

  <!-- ปุ่มขวา -->
  <button class="carousel-control-next"
          type="button"
          data-bs-target="#carouselExample"
          data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>

</div>
<?php else: ?>
<img src="images/no-image.png" class="img-fluid rounded">
<?php endif; ?>


</div>


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

<h6 class="fw-bold">เลือกไซส์</h6>

<div id="sizeContainer" class="d-flex flex-wrap">
<?php foreach($sizes as $size): 
    $size = trim($size);
    $stock = $sizeStocks[$size] ?? 0;
?>
    <button type="button"
        class="size-btn"
        onclick="selectSize(this,'<?= $size ?>', <?= $stock ?>)">
        <?= $size ?>
    </button>
<?php endforeach; ?>
</div>



<input type="hidden" id="selectedSize" value="">

<hr>

<h6 class="fw-bold">
จำนวน (<span id="stock_text">กรุณาเลือกไซส์</span>)
</h6>



<input type="hidden" id="max_stock" value="0">


<?php if($product['p_qty'] > 0): // ถ้ามีสินค้ามากกว่า 0 ?>
    <div class="qty-box mb-3">
        <button type="button" onclick="decrease()">-</button>
        <input type="text" id="qty" value="1" readonly>
        <button type="button" onclick="increase()">+</button>
    </div>
<?php else: // ถ้าสินค้าหมด ?>
    <div class="alert alert-danger py-2 mb-3" style="width: fit-content;">
        ขออภัย สินค้าหมดชั่วคราว
    </div>
<?php endif; ?>

<hr class="my-3">

<h6 class="fw-bold detail-title mb-1">รายละเอียดสินค้า</h6>

<p class="product-detail mb-2 mt-0">
<?= nl2br(htmlspecialchars($product['p_detail'])) ?>
</p>



<?php if($product['p_qty'] > 0): ?>
    <button class="btn buy-btn btn-lg"
    onclick="addToCart(<?= $product['p_id'] ?>)">
    เพิ่มลงตะกร้า
    </button>
<?php else: ?>
    <button class="btn btn-secondary btn-lg btn-disabled" disabled>
    สินค้าหมด
    </button>
<?php endif; ?>

<button class="btn btn-outline-danger btn-lg ms-2"
onclick="addToFav(<?= $product['p_id'] ?>)">
❤ เพิ่มรายการโปรด
</button>


</div>
</div>

<hr class="my-5">

<h4 class="fw-bold mb-4">สินค้าแนะนำสำหรับคุณ</h4>

<div class="row">

<?php while($rec = mysqli_fetch_assoc($rec_rs)): ?>

<?php
$rec_img = !empty($rec['main_img']) 
           ? $rec['main_img'] 
           : 'images/no-image.png';
?>

<div class="col-md-3 mb-4">
<div class="card h-100 shadow-sm">

<a href="product_detail.php?id=<?= $rec['p_id']; ?>" 
   class="text-decoration-none text-dark">

<img src="<?= htmlspecialchars($rec_img); ?>"
     class="card-img-top"
     style="height:220px;object-fit:cover;">

<div class="card-body">
    <h6 class="fw-bold mb-1">
        <?= htmlspecialchars($rec['p_name']); ?>
    </h6>
    <div class="text-warning fw-bold">
        ฿<?= number_format($rec['p_price'],0); ?>
    </div>
</div>

</a>

</div>
</div>

<?php endwhile; ?>

</div>

</div>


<script>
function selectSize(el, size, stock){

    // ลบ active เดิม
    document.querySelectorAll('.size-btn')
        .forEach(btn => btn.classList.remove('active'));

    el.classList.add('active');

    document.getElementById('selectedSize').value = size;

    document.getElementById('max_stock').value = stock;

    document.getElementById('qty').value = 1;

    // แสดง stock ที่หัวข้อจำนวน
    document.getElementById('stock_text').innerText =
        "คงเหลือ " + stock + " คู่";
}


// [แก้ไข] ฟังก์ชันเพิ่มจำนวน ให้เช็คกับ max_stock
function increase(){
    let qty = document.getElementById('qty');
    let max = parseInt(document.getElementById('max_stock').value); // รับค่า stock

    let currentVal = parseInt(qty.value);

    if(currentVal < max){
        qty.value = currentVal + 1;
    } else {
        alert("ขออภัย สินค้ามีเพียง " + max + " ชิ้น");
    }
}

function decrease(){
    let qty = document.getElementById('qty');
    if(parseInt(qty.value)>1){
        qty.value = parseInt(qty.value)-1;
    }
}

function addToCart(id){
    let size = document.getElementById('selectedSize').value;
    // [เพิ่ม] เช็คว่ามี element qty หรือไม่ (กันกรณีสินค้าหมดแล้วไม่มี input)
    let qtyInput = document.getElementById('qty');
    let qty  = qtyInput ? qtyInput.value : 0;

    if(size==""){
        alert("กรุณาเลือกไซส์ก่อน");
        return;
    }
    
    if(qty <= 0){
        alert("สินค้าหมด");
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