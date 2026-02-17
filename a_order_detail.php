<?php
session_start();
require_once 'connectdb.php';
include 'bootstrap.php';

// เช็คสิทธิ์ Admin (ถ้ามีระบบ Login Admin ให้เช็คตรงนี้)
// if(!isset($_SESSION['is_admin'])) { header("Location: login.php"); exit(); }

if(!isset($_GET['id'])){
    header("Location: a_orderlist.php");
    exit();
}

$order_id = intval($_GET['id']);

/* ===========================================
   1. ดึงข้อมูลออเดอร์ (ไม่เช็ค u_id)
=========================================== */
$sql_order = "
    SELECT o.*, u.fullname, u.email, u.phone, u.address,
           p.slip_image, p.pay_date, p.amount as pay_amount
    FROM orders o
    LEFT JOIN users u ON o.u_id = u.user_id
    LEFT JOIN payments p ON o.o_id = p.order_id
    WHERE o.o_id = $order_id
";
$rs_order = mysqli_query($conn, $sql_order);

if(mysqli_num_rows($rs_order) == 0){
    die("ไม่พบข้อมูลออเดอร์");
}

$order = mysqli_fetch_assoc($rs_order);

/* ===========================================
   2. ดึงรายการสินค้า
=========================================== */
$sql_items = "
    SELECT od.*, p.p_name, p.p_price, pi.img_path
    FROM order_details od
    JOIN products p ON od.p_id = p.p_id
    LEFT JOIN (
        SELECT p_id, img_path FROM product_images GROUP BY p_id
    ) pi ON p.p_id = pi.p_id
    WHERE od.o_id = $order_id
";
$rs_items = mysqli_query($conn, $sql_items);

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายละเอียดออเดอร์ #<?= $order_id ?></title>
    <style>
        body{ background:#f4f6f9; font-family:'Kanit',sans-serif; }
        .wrapper{ padding:30px; }
        .card-custom{ background:#fff; border-radius:10px; padding:25px; box-shadow:0 0 10px rgba(0,0,0,0.05); }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.9rem; color:white; }
        .st-wait-pay { background-color: #ffc107; color: #000; } 
        .st-check { background-color: #17a2b8; } 
        .st-paid { background-color: #28a745; } 
        .st-ship { background-color: #007bff; } 
        .st-cancel { background-color: #dc3545; } 
    </style>
</head>
<body>

<div class="container wrapper">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>รายละเอียดคำสั่งซื้อ #<?= str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></h4>
        <a href="a_orderlist.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> ย้อนกลับ
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card-custom mb-3">
                <h5 class="mb-3">รายการสินค้า</h5>
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>สินค้า</th>
                            <th class="text-center">ราคา</th>
                            <th class="text-center">จำนวน</th>
                            <th class="text-end">รวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = mysqli_fetch_assoc($rs_items)): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= !empty($item['img_path']) ? htmlspecialchars($item['img_path']) : 'https://placehold.co/50x50' ?>" 
                                         style="width:50px; height:50px; object-fit:cover; border-radius:5px; margin-right:10px;">
                                    <div><?= htmlspecialchars($item['p_name']) ?></div>
                                </div>
                            </td>
                            <td class="text-center"><?= number_format($item['price'],0) ?></td>
                            <td class="text-center"><?= $item['q_ty'] ?></td>
                            <td class="text-end fw-bold"><?= number_format($item['price'] * $item['q_ty'],0) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end">รวมยอดสินค้า</td>
                            <td class="text-end fw-bold"><?= number_format($order['total_price'] - 60, 0) ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">ค่าจัดส่ง</td>
                            <td class="text-end">60</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold fs-5">ยอดสุทธิ</td>
                            <td class="text-end fw-bold fs-5 text-primary"><?= number_format($order['total_price'], 0) ?> ฿</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="card-custom">
                <h5 class="mb-3">ข้อมูลลูกค้า & การจัดส่ง</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ชื่อลูกค้า:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
                        <p><strong>เบอร์โทร:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                        <p><strong>อีเมล:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>ที่อยู่จัดส่ง:</strong></p>
                        <div class="alert alert-light border">
                            <?= nl2br(htmlspecialchars($order['address'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-custom mb-3">
                <h5 class="mb-3">สถานะออเดอร์</h5>
                
                <?php 
                    $st = $order['status'];
                    $cls = 'bg-secondary';
                    if($st=='รอชำระเงิน') $cls='st-wait-pay';
                    elseif($st=='รอตรวจสอบ') $cls='st-check';
                    elseif($st=='ชำระแล้ว') $cls='st-paid';
                    elseif($st=='จัดส่งแล้ว') $cls='st-ship';
                    elseif($st=='ยกเลิก') $cls='st-cancel';
                ?>
                <div class="text-center mb-3">
                    <span class="status-badge <?= $cls ?> fs-5 px-4 py-2"><?= $st ?></span>
                </div>
                
                <p class="mb-1 text-muted small">วันที่สั่งซื้อ: <?= date('d/m/Y H:i', strtotime($order['o_date'])) ?></p>
                <p class="mb-1 text-muted small">ช่องทางชำระ: <?= ($order['payment_method']=='cod') ? 'เก็บปลายทาง' : 'โอนเงิน' ?></p>

            </div>

            <?php if(!empty($order['slip_image'])): ?>
            <div class="card-custom">
                <h5 class="mb-3">หลักฐานการโอนเงิน</h5>
                <a href="uploads/slips/<?= $order['slip_image'] ?>" target="_blank">
                    <img src="uploads/slips/<?= $order['slip_image'] ?>" class="img-fluid rounded border">
                </a>
                <div class="mt-2 text-center small text-muted">
                    วันที่โอน: <?= date('d/m/Y H:i', strtotime($order['pay_date'])) ?><br>
                    ยอดเงิน: <?= number_format($order['pay_amount'],2) ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

</div>

</body>
</html>