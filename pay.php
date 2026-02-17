<?php
session_start();
include 'connectdb.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

/* ===============================
   เมื่อกดชำระสินค้า
================================ */
if(isset($_POST['confirm_order'])){

    if(empty($_POST['payment_method'])){
        die("<script>alert('กรุณาเลือกช่องทางการชำระเงิน'); window.history.back();</script>");
    }

    $payment_method = $_POST['payment_method'];
    $address_id     = intval($_POST['address_id'] ?? 0);

    /* 1️⃣ ดึงสินค้าจาก cart */
    $stmt_cart = $conn->prepare("
        SELECT c.product_id, c.quantity, c.size,
               p.p_price
        FROM cart c
        JOIN products p ON c.product_id = p.p_id
        WHERE c.user_id = ?
    ");
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $cart = $stmt_cart->get_result();

    if($cart->num_rows == 0){
        die("ไม่มีสินค้าในตะกร้า");
    }

    $total_price = 0;
    $items = [];

    while($row = $cart->fetch_assoc()){
        $subtotal = $row['p_price'] * $row['quantity'];
        $total_price += $subtotal;
        $items[] = $row;
    }

    /* 2️⃣ สร้าง order */
    $stmt_order = $conn->prepare("
        INSERT INTO orders
        (u_id, address_id, total_price, payment_method, status, o_date)
        VALUES (?, ?, ?, ?, 'รอชำระเงิน', NOW())
    ");

    if(!$stmt_order){
        die("Order Prepare Failed: " . $conn->error);
    }

    $stmt_order->bind_param("iids",
        $user_id,
        $address_id,
        $total_price,
        $payment_method
    );

    $stmt_order->execute();
    $order_id = $stmt_order->insert_id;

    /* 3️⃣ เพิ่มรายการสินค้าใน order_details */
    foreach($items as $item){

        $stmt_detail = $conn->prepare("
            INSERT INTO order_details
            (o_id, p_id, size, qty, price)
            VALUES (?, ?, ?, ?, ?)
        ");

        if(!$stmt_detail){
            die("Detail Prepare Failed: " . $conn->error);
        }

        $stmt_detail->bind_param("iisid",
            $order_id,
            $item['product_id'],
            $item['size'],
            $item['quantity'],
            $item['p_price']
        );

        $stmt_detail->execute();

        /* 4️⃣ ตัดสต็อกสินค้า */
        $stmt_stock = $conn->prepare("
            UPDATE products
            SET p_qty = p_qty - ?
            WHERE p_id = ?
        ");
        $stmt_stock->bind_param("ii",
            $item['quantity'],
            $item['product_id']
        );
        $stmt_stock->execute();
    }

    /* 5️⃣ ลบ cart */
    $stmt_clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt_clear->bind_param("i", $user_id);
    $stmt_clear->execute();

    header("Location: orderdetail.php?id=".$order_id."&success=1");
    exit();
}
?>
