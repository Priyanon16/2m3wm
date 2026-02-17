<?php
session_start();

include_once("connectdb.php");
include_once("functions.php");
include_once("bootstrap.php");

/* =========================================================
   [อัปเดต] ส่วนเพิ่มตะกร้า : รองรับการบันทึก Size ลง Database แล้ว
========================================================= */
if(isset($_GET['add_to_cart'])){
    
    // 1. ตรวจสอบว่า Login หรือยัง
    if(!isset($_SESSION['user_id'])){
        echo "<script>alert('กรุณาล็อกอินก่อนซื้อสินค้า'); window.location='login.php';</script>";
        exit;
    }

    $pid = intval($_GET['add_to_cart']);
    $uid = intval($_SESSION['user_id']);

    // 2. ดึงข้อมูลสินค้าเพื่อเช็คสต็อก และ หาไซส์แรก (Default Size)
    $q_prod = mysqli_query($conn, "SELECT p_qty, p_size FROM products WHERE p_id = $pid");
    $prod   = mysqli_fetch_assoc($q_prod);

    if($prod['p_qty'] > 0){
        
        // แยกไซส์แรกออกมา เพื่อบันทึกเป็นค่าเริ่มต้น
        // (เพราะหน้าแรกไม่มีปุ่มเลือกไซส์ เราจะหยิบไซส์แรกสุดใส่ให้อัตโนมัติ)
        $size_arr = explode(',', $prod['p_size']); 
        $default_size = isset($size_arr[0]) ? trim($size_arr[0]) : '-'; 

        // 3. เช็คว่ามีสินค้านี้ (และไซส์นี้) ในตะกร้าหรือยัง
        $check_cart = mysqli_query($conn, "
            SELECT * FROM cart 
            WHERE user_id=$uid 
            AND product_id=$pid 
            AND size='$default_size'
        ");

        if(mysqli_num_rows($check_cart) > 0){
            // มีแล้ว -> อัปเดตจำนวน +1
            $cart_item = mysqli_fetch_assoc($check_cart);
            if($cart_item['quantity'] < $prod['p_qty']){
                mysqli_query($conn, "
                    UPDATE cart 
                    SET quantity = quantity + 1 
                    WHERE user_id=$uid 
                    AND product_id=$pid 
                    AND size='$default_size'
                ");
            }
        } else {
            // ยังไม่มี -> Insert ใหม่ พร้อมระบุไซส์
            $sql_insert = "
                INSERT INTO cart (user_id, product_id, quantity, size) 
                VALUES ($uid, $pid, 1, '$default_size')
            ";
            mysqli_query($conn, $sql_insert);
        }

        echo "<script>alert('เพิ่มสินค้าลงตะกร้าเรียบร้อย (Size: $default_size)'); window.location='cart.php';</script>";
        
    } else {
        echo "<script>alert('สินค้าหมด'); window.location='index.php';</script>";
    }
    exit;
}

/* =========================
   ส่วน Favorite (เหมือนเดิม)
========================= */
if(isset($_GET['add_to_fav'])){
    addToFavorite((int)$_GET['add_to_fav']);
}

// ... โค้ดส่วนแสดงผลด้านล่าง เหมือนเดิมไม่ต้องแก้ ...
$sql = "
SELECT p.*, 
       c.c_name,
       b.brand_name,
       (SELECT img_path FROM product_images WHERE p_id = p.p_id LIMIT 1) AS main_img
FROM products p
LEFT JOIN category c ON p.c_id = c.c_id
LEFT JOIN brand b ON p.brand_id = b.brand_id
ORDER BY p.p_id DESC
";
$rs = mysqli_query($conn,$sql);
