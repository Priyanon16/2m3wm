<?php
session_start();
include_once("check_login.php");
include_once("connectdb.php");

mysqli_set_charset($conn, "utf8");

// ดึงหมวดหมู่
$result_category = mysqli_query($conn, "SELECT * FROM category ORDER BY c_name ASC");

// ดึงแบรนด์
$result_brand = mysqli_query($conn, "SELECT * FROM brand ORDER BY brand_name ASC");

// สร้างโฟลเดอร์รูป
$upload_dir = "FileUpload/";
if(!is_dir($upload_dir)){
    mkdir($upload_dir,0777,true);
}

if(isset($_POST['save'])){

    $name   = mysqli_real_escape_string($conn,$_POST['p_name']);
    $price  = $_POST['p_price'];
    $qty    = $_POST['p_qty'];
    $type   = mysqli_real_escape_string($conn,$_POST['p_type']);
    $detail = mysqli_real_escape_string($conn,$_POST['p_detail']);
    $c_id   = $_POST['c_id'];
    $brand_id = $_POST['brand_id'];

    // ===== SIZE =====
    $p_size = "";
    if(isset($_POST['p_size'])){
        $p_size = implode(",",$_POST['p_size']);
    }

    // ===== รูปหลายรูป =====
    $uploaded_files = [];

    if(isset($_FILES['p_img'])){
        $count = count($_FILES['p_img']['name']);

        for($i=0;$i<$count;$i++){

            if($_FILES['p_img']['name'][$i] != ""){

                $ext = strtolower(pathinfo($_FILES['p_img']['name'][$i],PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];

                if(in_array($ext,$allowed)){

                    $new_name = "product_".time()."_".uniqid().".".$ext;
                    $target = $upload_dir.$new_name;

                    if(move_uploaded_file($_FILES['p_img']['tmp_name'][$i],$target)){
                        $uploaded_files[] = $target;
                    }
                }
            }
        }
    }

    $p_img = implode(",",$uploaded_files);

    // ===== INSERT =====
    $sql = "INSERT INTO products 
            (p_name,p_price,p_qty,p_size,p_type,p_img,p_detail,c_id,brand_id)
            VALUES
            ('$name','$price','$qty','$p_size','$type','$p_img','$detail','$c_id','$brand_id')";

    if(mysqli_query($conn,$sql)){
        echo "<script>alert('เพิ่มสินค้าสำเร็จ');window.location='admin_product.php';</script>";
        exit();
    }else{
        echo mysqli_error($conn);
    }
}
?>
