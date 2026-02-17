<?php
session_start();
include 'connectdb.php';
include 'bootstrap.php'; 

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ตรวจสอบการ Login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

/* =========================================
   ส่วนที่ 1: ประมวลผลเมื่อกดปุ่ม "ยืนยันการสั่งซื้อ"
========================================= */
if(isset($_POST['confirm_order'])){

    if(empty($_POST['payment_method'])){
        echo "<script>alert('กรุณาเลือกช่องทางการชำระเงิน');window.history.back();</script>";
        exit();
    }

    $payment_method = $_POST['payment_method'];

    $sql_cart = "SELECT c.product_id, c.quantity, p.p_price 
                 FROM cart c 
                 JOIN products p ON c.product_id = p.p_id 
                 WHERE c.user_id = ?";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $cart = $stmt_cart->get_result();

    if($cart->num_rows == 0){
        echo "<script>alert('ไม่มีสินค้าในตะกร้า');window.location='index.php';</script>";
        exit();
    }

    $total_price = 0;
    $items = [];
    while($row = $cart->fetch_assoc()){
        $subtotal = $row['p_price'] * $row['quantity'];
        $total_price += $subtotal;
        $items[] = $row;
    }
    
    $shipping_cost = 60; 
    $final_total = $total_price + $shipping_cost;

    // ดึง address ล่าสุดของ user
    $sql_addr2 = "SELECT address_id FROM addresses WHERE user_id = ? ORDER BY address_id DESC LIMIT 1";
    $stmt_addr2 = $conn->prepare($sql_addr2);
    $stmt_addr2->bind_param("i", $user_id);
    $stmt_addr2->execute();
    $res_addr2 = $stmt_addr2->get_result();
    $addr2 = $res_addr2->fetch_assoc();

    if(!$addr2){
        die("ไม่พบที่อยู่ กรุณาเพิ่มที่อยู่ก่อนสั่งซื้อ");
    }

    $address_id = $addr2['address_id'];

    $stmt_order = $conn->prepare("
        INSERT INTO orders 
        (u_id, address_id, total_price, status, o_date, payment_method) 
        VALUES (?, ?, ?, 'รอชำระเงิน', NOW(), ?)
    ");
    $stmt_order->bind_param("iids", $user_id, $address_id, $final_total, $payment_method);

    if(!$stmt_order->execute()){
        die("Error creating order: " . $conn->error);
    }

    $order_id = $stmt_order->insert_id;

    $stmt_detail = $conn->prepare("INSERT INTO order_details (o_id, p_id, q_ty, price) VALUES (?, ?, ?, ?)");
    foreach($items as $item){
        $stmt_detail->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['p_price']);
        $stmt_detail->execute();
    }

    $stmt_clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt_clear->bind_param("i", $user_id);
    $stmt_clear->execute();

    if ($payment_method == 'cod') {
        header("Location: orderdetail.php?id=".$order_id."&success=1");
    } else {
        header("Location: qrcode.php?id=".$order_id);
    }
    exit();
}

include 'header.php';

/* =========================================
   ส่วนที่ 2: ดึงข้อมูลเพื่อแสดงผลหน้าเว็บ
========================================= */

$sql_addr = "SELECT * FROM addresses WHERE user_id = ? ORDER BY address_id DESC LIMIT 1";
$stmt_addr = $conn->prepare($sql_addr);
$stmt_addr->bind_param("i", $user_id);
$stmt_addr->execute();
$res_addr = $stmt_addr->get_result();
$addr_row = $res_addr->fetch_assoc();

$show_fullname = 'ไม่ระบุชื่อ';
$show_phone = '-';
$show_address = '';
$has_address = false;

if ($addr_row) {
    $show_fullname = $addr_row['fullname'];
    $show_phone = $addr_row['phone'];
    $show_address = $addr_row['address'] . ' ต.' . $addr_row['district'] . ' จ.' . $addr_row['province'] . ' ' . $addr_row['postal_code'];
    $has_address = true;
} else {
    $sql_u = "SELECT fullname FROM users WHERE user_id = ?";
    $stmt_u = $conn->prepare($sql_u);
    $stmt_u->bind_param("i", $user_id);
    $stmt_u->execute();
    $res_u = $stmt_u->get_result();
    if($u_row = $res_u->fetch_assoc()){
        $show_fullname = $u_row['fullname'];
    }
}

$sql_view = "
    SELECT c.quantity, p.p_name, p.p_price,
           (SELECT img_path FROM product_images WHERE p_id = p.p_id LIMIT 1) AS p_img
    FROM cart c
    JOIN products p ON c.product_id = p.p_id
    WHERE c.user_id = ?
";
$stmt_products = $conn->prepare($sql_view);
$stmt_products->bind_param("i", $user_id);
$stmt_products->execute();
$cart_result = $stmt_products->get_result();

$total = 0;
$shipping = 60;
?>

<style>
body{background:#f5f5f5;font-family:'Kanit',sans-serif;}
.container{ max-width: 900px; margin: 0 auto; padding: 20px;}
.card{ background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 20px;}
.summary-item{ display: flex; justify-content: space-between; margin-bottom: 10px; }
.total-price{ font-size: 1.2rem; font-weight: bold; color: #ff7a00; }
.btn-confirm{ background: #ff7a00; color: #fff; width: 100%; padding: 12px; border: none; border-radius: 5px; font-size: 1.1rem; cursor: pointer;}
.btn-confirm:hover{ background: #e66e00; }
.btn-confirm:disabled{ background: #ccc; cursor: not-allowed; }
.payment-option{ margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
.item-row { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
.address-box { border-left: 4px solid #ff7a00; background: #fff8f0; padding: 15px; border-radius: 5px; }
.edit-addr-btn { float: right; font-size: 0.9rem; text-decoration: none; color: #ff7a00; }
</style>

<div class="container py-5">
