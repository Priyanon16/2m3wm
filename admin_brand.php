<?php
session_start();
include_once("connectdb.php");

/* =========================
   เพิ่มแบรนด์
========================= */
if(isset($_POST['add_brand'])){

    $brand_name = mysqli_real_escape_string($conn,$_POST['brand_name']);

    if(isset($_FILES['brand_img']) && $_FILES['brand_img']['error'] == 0){

        $file_ext = strtolower(pathinfo($_FILES['brand_img']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if(in_array($file_ext,$allowed)){

            $file_name = time()."_".uniqid().".".$file_ext;
            $upload_dir = __DIR__."/uploads/brands/";

            if(!is_dir($upload_dir)){
                mkdir($upload_dir,0777,true);
            }

            move_uploaded_file($_FILES['brand_img']['tmp_name'],$upload_dir.$file_name);

            mysqli_query($conn,"INSERT INTO brand (brand_name,brand_img)
                                VALUES ('$brand_name','$file_name')");

            echo "<script>alert('เพิ่มแบรนด์สำเร็จ');window.location='admin_brand.php';</script>";
        }
    }
}

/* =========================
   แก้ไขแบรนด์
========================= */
if(isset($_POST['update_brand'])){

    $brand_id   = intval($_POST['brand_id']);
    $brand_name = mysqli_real_escape_string($conn,$_POST['brand_name']);

    $getOld = mysqli_query($conn,"SELECT brand_img FROM brand WHERE brand_id=$brand_id");
    $oldRow = mysqli_fetch_assoc($getOld);
    $oldImg = $oldRow['brand_img'];

    /* ถ้ามีอัปโหลดรูปใหม่ */
    if($_FILES['brand_img']['name'] != ""){

        $file_ext = strtolower(pathinfo($_FILES['brand_img']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if(in_array($file_ext,$allowed)){

            $file_name = time()."_".uniqid().".".$file_ext;
            $upload_dir = __DIR__."/uploads/brands/";

            move_uploaded_file($_FILES['brand_img']['tmp_name'],$upload_dir.$file_name);

            /* ลบรูปเก่า */
            if(file_exists("uploads/brands/".$oldImg)){
                unlink("uploads/brands/".$oldImg);
            }

            mysqli_query($conn,"UPDATE brand 
                                SET brand_name='$brand_name',
                                    brand_img='$file_name'
                                WHERE brand_id=$brand_id");
        }

    }else{
        /* เปลี่ยนแค่ชื่อ */
        mysqli_query($conn,"UPDATE brand 
                            SET brand_name='$brand_name'
                            WHERE brand_id=$brand_id");
    }

    echo "<script>alert('แก้ไขสำเร็จ');window.location='admin_brand.php';</script>";
}

/* =========================
   ลบแบรนด์
========================= */
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    $getImg = mysqli_query($conn,"SELECT brand_img FROM brand WHERE brand_id=$id");
    $imgRow = mysqli_fetch_assoc($getImg);

    if(file_exists("uploads/brands/".$imgRow['brand_img'])){
        unlink("uploads/brands/".$imgRow['brand_img']);
    }

    mysqli_query($conn,"DELETE FROM brand WHERE brand_id=$id");

    echo "<script>window.location='admin_brand.php';</script>";
}

/* =========================
   ดึงข้อมูล
========================= */
$rs = mysqli_query($conn,"SELECT * FROM brand ORDER BY brand_id DESC");
?>
