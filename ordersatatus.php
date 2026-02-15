<?php
include("connectdb.php");

$orderData = null;

if(isset($_GET['order_id'])){
    $order_id = $_GET['order_id'];

    $sql = "SELECT * FROM orders WHERE order_id='$order_id'";
    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) > 0){
        $orderData = mysqli_fetch_assoc($result);
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>เช็คสถานะออเดอร์ - 2M3WM</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f5f5f5;
    margin:0;
}

/* HEADER */
.header{
    background:#000;
    padding:15px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{
    color:#ff6600;
    font-weight:700;
    font-size:22px;
}

.back-link{
    color:#fff;
    text-decoration:none;
}

/* CARD */
.container-box{
    max-width:700px;
    margin:60px auto;
}

.status-card{
    background:#fff;
    padding:30px;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.status-badge{
    background:#ff6600;
    color:#fff;
    padding:5px 12px;
    border-radius:20px;
    font-size:14px;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">2M3WM SNEAKER</div>
    <a href="orderhistory.php" class="back-link">← กลับหน้าประวัติ</a>
</div>

<!-- CONTENT -->
<div class="container-box">

    <div class="status-card">

        <h4 class="mb-4 text-center">เช็คสถานะออเดอร์</h4>

        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="order_id" class="form-control" placeholder="กรอก Order ID" required>
                <button class="btn btn-warning">ค้นหา</button>
            </div>
        </form>

        <?php if($orderData){ ?>

            <hr>

            <h5>Order ID: <?php echo $orderData['order_id']; ?></h5>
            <p class="text-muted"><?php echo $orderData['order_date']; ?></p>

            <div class="mb-3">
                <span class="status-badge">
                    <?php echo $orderData['shipping_status']; ?>
                </span>
            </div>

            <div class="mt-3">
                <p><strong>สถานะการชำระเงิน:</strong> <?php echo $orderData['payment_status']; ?></p>
                <p><strong>บริษัทขนส่ง:</strong> <?php echo $orderData['carrier']; ?></p>
                <p><strong>เลข Tracking:</strong> <?php echo $orderData['tracking_number']; ?></p>
                <p><strong>พัสดุอยู่ที่:</strong> <?php echo $orderData['current_location']; ?></p>
            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>
