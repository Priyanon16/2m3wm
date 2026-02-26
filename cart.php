<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$uid = intval($_SESSION['user_id']);

/* =================================================
   [เพิ่มส่วนนี้] รับสินค้าจากหน้า Product Detail
================================================= */
if(isset($_GET['add'])){
    $pid  = intval($_GET['add']);
    $qty  = intval($_GET['qty']);
    $size = mysqli_real_escape_string($conn, $_GET['size']);

    // 1. เช็คสต็อกจริงก่อนว่ามีพอไหม
    $stock_sql = mysqli_query($conn, "SELECT p_qty FROM products WHERE p_id = $pid");
    $stock_data = mysqli_fetch_assoc($stock_sql);
    $max_stock = $stock_data['p_qty'];

    if($max_stock <= 0){
        echo "<script>alert('สินค้าหมด'); window.location='index.php';</script>";
        exit;
    }

    // 2. เช็คว่ามีสินค้านี้ในตะกร้าแล้วหรือยัง
    $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$uid AND product_id=$pid");
    
    if(mysqli_num_rows($check_cart) > 0){
        // มีแล้ว -> อัปเดตจำนวน (ต้องไม่เกินสต็อก)
        $cart_item = mysqli_fetch_assoc($check_cart);
        $new_qty = $cart_item['quantity'] + $qty;

        if($new_qty > $max_stock){
            $new_qty = $max_stock; // ถ้าเกิน ให้ปรับเท่ากับที่มีสูงสุด
            echo "<script>alert('สินค้ามีจำกัด เพิ่มได้สูงสุดเท่าที่มีในสต็อก');</script>";
        }

        mysqli_query($conn, "
            UPDATE cart 
            SET quantity = $new_qty 
            WHERE user_id=$uid AND product_id=$pid
        ");
    } else {
        // ยังไม่มี -> เพิ่มใหม่
        if($qty > $max_stock) $qty = $max_stock; // กันเหนียว

        // (สมมติว่าตาราง cart มีคอลัมน์ size ถ้าไม่มีให้ลบ '$size' ออก)
        mysqli_query($conn, "
            INSERT INTO cart (user_id, product_id, quantity, size)
            VALUES ($uid, $pid, $qty, '$size')
        ");
    }

    header("Location: cart.php");
    exit;
}


/* =========================
   [แก้ไข] เพิ่ม / ลด จำนวน (เช็ค Stock)
========================= */
if(isset($_GET['update'])){
    $pid = intval($_GET['update']);
    $action = $_GET['type'];

    // ดึงสต็อกปัจจุบัน และ จำนวนในตะกร้าปัจจุบัน
    $q_check = mysqli_query($conn, "
        SELECT c.quantity, p.p_qty 
        FROM cart c
        JOIN products p ON c.product_id = p.p_id
        WHERE c.user_id=$uid AND c.product_id=$pid
    ");
    $data = mysqli_fetch_assoc($q_check);
    
    $current_qty = $data['quantity'];
    $max_stock   = $data['p_qty'];

    if($action == "plus"){
        // [แก้ไข] เช็คว่าถ้าบวก 1 แล้วเกินสต็อกไหม
        if( ($current_qty + 1) <= $max_stock ){
            mysqli_query($conn,"
                UPDATE cart 
                SET quantity = quantity + 1
                WHERE user_id=$uid AND product_id=$pid
            ");
        } else {
            // ถ้าเกิน ไม่ทำอะไร หรือแจ้งเตือนก็ได้
        }
    }

    if($action == "minus"){
        mysqli_query($conn,"
            UPDATE cart 
            SET quantity = IF(quantity>1, quantity-1, 1)
            WHERE user_id=$uid AND product_id=$pid
        ");
    }

    header("Location: cart.php");
    exit;
}

/* =========================
   ลบสินค้า
========================= */
if(isset($_GET['remove'])){
    $pid = intval($_GET['remove']);
    mysqli_query($conn,"
        DELETE FROM cart 
        WHERE user_id=$uid AND product_id=$pid
    ");
    header("Location: cart.php");
    exit;
}

/* =========================
   ดึงข้อมูล + รูปหลัก
========================= */
$sql = "
SELECT 
    c.quantity,
    c.size, 
    p.*,
    (
        SELECT img_path 
        FROM product_images 
        WHERE p_id = p.p_id 
        LIMIT 1
    ) AS main_img
FROM cart c
JOIN products p ON c.product_id = p.p_id
WHERE c.user_id = $uid
";

$rs = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ตะกร้าสินค้า - 2M3WM</title>

<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body{background:#f5f5f5;font-family:'Kanit',sans-serif;}

.page-header{
    background:#111;
    color:#fff;
    padding:40px 0;
    text-align:center;
}

.cart-box{
    background:#fff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.cart-item{
    display:flex;
    align-items:center;
    gap:20px;
    padding:20px 0;
    border-bottom:1px solid #eee;
}

.cart-img{
    width:110px;
    height:110px;
    object-fit:cover;
    border-radius:10px;
}

.qty-box{
    display:flex;
    align-items:center;
    gap:10px;
}

.qty-btn{
    width:32px;
    height:32px;
    border:none;
    background:#111;
    color:#fff;
    border-radius:6px;
    text-align:center;
    line-height:32px;
    text-decoration:none;
}
/* สไตล์สำหรับปุ่มที่กดไม่ได้ */
.qty-btn.disabled{
    background: #ccc;
    pointer-events: none;
}

.price{
    color:#ff7a00;
    font-weight:700;
    font-size:18px;
}

.summary-box{
    background:#fff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    position:sticky;
    top:100px;
}

.checkout-btn{
    background:#ff7a00;
    border:none;
    padding:12px;
    border-radius:8px;
    color:#fff;
    font-weight:600;
    width:100%;
}

.checkout-btn:hover{
    background:#e66e00;
}

.select-all{
    font-weight:600;
    margin-bottom:20px;
}
</style>
</head>

<body>

<?php include("header.php"); ?>

<div class="page-header">
<h2>ตะกร้าสินค้า</h2>
<p>ตรวจสอบก่อนชำระเงิน</p>
</div>

<div class="container py-5">
<div class="row">

<div class="col-lg-8">
<div class="cart-box">

<?php if(mysqli_num_rows($rs)==0): ?>

<div class="text-center py-5">
ยังไม่มีสินค้าในตะกร้า
</div>

<?php else: ?>

<div class="select-all">
<input type="checkbox" id="selectAll" checked
style="width:18px;height:18px;accent-color:#ff7a00;">
<label for="selectAll" class="ms-2">
เลือกสินค้าทั้งหมด
</label>
</div>

<?php 
$total = 0;
while($item = mysqli_fetch_assoc($rs)):
    $qty = $item['quantity'];
    $max_stock = $item['p_qty']; // จำนวนสต็อกสูงสุด
    
    // คำนวณราคารวม
    $subtotal = $item['p_price'] * $qty;
    $total += $subtotal;

    $img = !empty($item['main_img']) 
           ? $item['main_img'] 
           : 'images/no-image.png';
    
    // [เพิ่ม] ตรวจสอบว่าปุ่มบวกควรจะกดได้ไหม
    $plus_disabled = ($qty >= $max_stock) ? 'disabled' : '';
?>

<div class="cart-item">

<input type="checkbox"
class="item-check"
data-price="<?= $item['p_price']; ?>"
data-qty="<?= $qty; ?>"
checked
style="width:18px;height:18px;accent-color:#ff7a00;">

<img src="<?= htmlspecialchars($img); ?>" class="cart-img">

<div style="flex:1;">
    <h6><?= htmlspecialchars($item['p_name']); ?></h6>
    
    <?php if(!empty($item['size'])): ?>
        <small class="text-muted">Size: <?= htmlspecialchars($item['size']) ?></small>
    <?php endif; ?>

    <div class="qty-box mt-2">
        <a href="?update=<?= $item['p_id']; ?>&type=minus" class="qty-btn">−</a>
        <span><?= $qty; ?></span>
        <a href="?update=<?= $item['p_id']; ?>&type=plus" 
           class="qty-btn <?= $plus_disabled; ?>">+</a>
    </div>
    
    <?php if($qty >= $max_stock): ?>
        <small class="text-danger" style="font-size:12px;">*เหลือสินค้าแค่นี้</small>
    <?php endif; ?>

</div>

<div>
    <div class="price">
        ฿<?= number_format($subtotal,0); ?>
    </div>

    <a href="?remove=<?= $item['p_id']; ?>" 
       class="text-danger small"
       onclick="return confirm('ลบสินค้านี้?')">
       ลบ
    </a>
</div>

</div>

<?php endwhile; ?>
<?php endif; ?>

</div>
</div>


<div class="col-lg-4">
<div class="summary-box">

<h6 class="fw-bold mb-3">สรุปรายการ</h6>

<div class="d-flex justify-content-between">
<span>ยอดสินค้า</span>
<span id="subtotal">
฿<?= number_format($total ?? 0,0); ?>
</span>
</div>

<div class="d-flex justify-content-between">
<span>ค่าจัดส่ง</span>
<span>฿60</span>
</div>

<hr>

<h5 class="d-flex justify-content-between">
<span>ยอดสุทธิ</span>
<span class="text-warning" id="grandTotal">
฿<?= number_format(($total ?? 0)+50,0); ?>
</span>
</h5>

<form action="pay.php" method="post">
<button class="checkout-btn mt-3">
ดำเนินการสั่งซื้อ
</button>
</form>

</div>
</div>

</div>
</div>

<script>

function calculateTotal(){
    let checkboxes = document.querySelectorAll('.item-check');
    let subtotal = 0;

    checkboxes.forEach(cb=>{
        if(cb.checked){
            let price = parseFloat(cb.dataset.price);
            let qty = parseInt(cb.dataset.qty);
            subtotal += price * qty;
        }
    });

    document.getElementById('subtotal').innerText =
        "฿" + subtotal.toLocaleString();

    document.getElementById('grandTotal').innerText =
        "฿" + (subtotal + 50).toLocaleString();
}

/* Select All */
document.getElementById('selectAll')
.addEventListener('change', function(){

    let items = document.querySelectorAll('.item-check');
    items.forEach(cb => cb.checked = this.checked);

    calculateTotal();
});

/* Individual */
document.querySelectorAll('.item-check')
.forEach(cb=>{
    cb.addEventListener('change', function(){

        let all = document.querySelectorAll('.item-check');
        let checked = document.querySelectorAll('.item-check:checked');

        document.getElementById('selectAll').checked =
            (all.length === checked.length);

        calculateTotal();
    });
});

</script>

</body>
</html>