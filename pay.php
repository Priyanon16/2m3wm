<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connectdb.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

if(isset($_POST['confirm_order'])){

    /* ======================
       1️⃣ ดึงสินค้าใน cart
    =======================*/
    $stmt_cart = $conn->prepare("
        SELECT c.product_id, c.quantity, p.p_price
        FROM cart c
        JOIN products p ON c.product_id = p.p_id
        WHERE c.user_id = ?
    ");

    if(!$stmt_cart){
        die("Cart Error: ".$conn->error);
    }

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

    /* ======================
       2️⃣ สร้าง order
    =======================*/
    $stmt_order = $conn->prepare("
        INSERT INTO orders
        (u_id, total_price, status, o_date)
        VALUES (?, ?, 'รอชำระเงิน', NOW())
    ");

    if(!$stmt_order){
        die("Order Error: ".$conn->error);
    }

    $stmt_order->bind_param("id", $user_id, $total_price);
    $stmt_order->execute();

    $order_id = $stmt_order->insert_id;

    /* ======================
       3️⃣ เพิ่ม order_details
    =======================*/
    foreach($items as $item){

        $stmt_detail = $conn->prepare("
            INSERT INTO order_details
            (o_id, p_id, qty, price)
            VALUES (?, ?, ?, ?)
        ");

        if(!$stmt_detail){
            die("Detail Error: ".$conn->error);
        }

        $stmt_detail->bind_param(
            "iiid",
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['p_price']
        );

        $stmt_detail->execute();
    }

    /* ======================
       4️⃣ ลบ cart
    =======================*/
    $stmt_clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt_clear->bind_param("i", $user_id);
    $stmt_clear->execute();

    header("Location: orderdetail.php?id=".$order_id);
    exit();
}
?>
