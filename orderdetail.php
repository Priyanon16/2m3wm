<?php
include "data.php";

$order = null;

if(isset($_GET['order_id'])){
    $order_id = $_GET['order_id'];

    $sql = "SELECT * FROM orders WHERE order_id='$order_id'";
    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) > 0){
        $order = mysqli_fetch_assoc($result);
    }
}

/* กำหนดข้อความ + สีสถานะ */
function getStatus($status){
    switch($status){
        case "pending_payment":
            return ["ที่ต้องชำระเงิน","secondary"];
        case "to_ship":
            return ["ที่ต้องจัดส่ง","warning"];
        case "shipping":
            return ["ที่ต้องได้รับ","info"];
        case "completed":
            return ["สำเร็จแล้ว","success"];
        case "cancelled":
            return ["ยกเลิก","danger"];
        default:
            return ["ไม่ทราบสถานะ","dark"];
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>รายละเอียดคำสั่งซื้อ</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f5f5f5;
    margin:0;
}
.header{
    background:#000;
    padding:15px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.logo{
    color:#ff6600;
    font-weight:bold;
    font-size:22px;
}
.container-box{
    max-width:850px;
    margin:50px auto;
}
.order-card{
    background:#fff;
    padding:30px;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}
.product-img{
    width:120px;
    border-radius:10px;
}
</style>
</head>

<body>

<div class="header">
    <div class="logo">2M3WM SNEAKER</div>
    <a href="orderhistory.php" class="text-white text-decoration-none">← กลับ</a>
</div>

<div class="container-box">

<?php if($order){ 
    $statusInfo = getStatus($order['order_status']);
?>

<div class="order-card">

    <!-- ข้อมูลออเดอร์ -->
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>เลขที่คำสั่งซื้อ: <?php echo $order['order_id']; ?></h5>
            <p class="text-muted">วันที่สั่งซื้อ: <?php echo $order['order_date']; ?></p>
            <p><strong>ร้านค้า:</strong> 2M3WM SNEAKER</p>
        </div>

        <span class="badge bg-<?php echo $statusInfo[1]; ?> fs-6">
            <?php echo $statusInfo[0]; ?>
        </span>
    </div>

    <hr>

    <!-- รายการสินค้า -->
    <div class="d-flex align-items-center">
        <img src="<?php echo $order['product_image']; ?>" class="product-img me-4">

        <div>
            <h6><?php echo $order['product_name']; ?></h6>
            <p class="mb-1">จำนวน: <?php echo $order['quantity']; ?></p>
            <p class="mb-0 text-muted">ราคา: ฿<?php echo number_format($order['total_price']); ?></p>
        </div>
    </div>

    <hr>

    <!-- ยอดรวม -->
    <div class="text-end">
        <h5>ยอดชำระรวม: <strong>฿<?php echo number_format($order['total_price']); ?></strong></h5>
    </div>

</div>

<?php } else { ?>

<div class="alert alert-danger text-center">
    ไม่พบคำสั่งซื้อ
</div>

<?php } ?>

</div>

</body>
</html>
