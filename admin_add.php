<?php
session_start();
include_once("check_login.php");
include_once("connectdb.php");

mysqli_set_charset($conn, "utf8");

if (!isset($_GET['id'])) {
    header("Location: admin_product.php");
    exit();
}

$id = intval($_GET['id']);

/* 1. ดึงข้อมูลสินค้า */
$product_query = mysqli_query($conn, "SELECT * FROM products WHERE p_id=$id");
$row = mysqli_fetch_assoc($product_query);

if (!$row) {
    header("Location: admin_product.php");
    exit();
}

/* 2. ดึงข้อมูลสต็อกรายไซส์ */
$stock_map = [];

$stock_rs = mysqli_query($conn, "SELECT * FROM product_stock WHERE p_id=$id");
while ($s = mysqli_fetch_assoc($stock_rs)) {
    $stock_map[$s['p_size']] = $s['p_qty_stock'];
}

/* 3. ดึงหมวดหมู่/แบรนด์ */
$cat_query   = mysqli_query($conn, "SELECT * FROM category ORDER BY c_name ASC");
$brand_query = mysqli_query($conn, "SELECT * FROM brand ORDER BY brand_name ASC");

$upload_dir = __DIR__ . "/uploads/products/";

/* 4. ลบรูป */
if (isset($_GET['delete_img'])) {

    $img_id = intval($_GET['delete_img']);

    $img_rs = mysqli_query($conn,
        "SELECT img_path FROM product_images WHERE img_id=$img_id"
    );

    $img = mysqli_fetch_assoc($img_rs);

    if ($img) {

        $file_path = __DIR__ . "/" . $img['img_path'];

        if (file_exists($file_path)) {
            unlink($file_path);
        }

        mysqli_query($conn,
            "DELETE FROM product_images WHERE img_id=$img_id"
        );
    }

    header("Location: admin_edit.php?id=" . $id);
    exit();
}

/* 5. UPDATE ข้อมูล */
if (isset($_POST['update'])) {

    $name   = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price  = floatval($_POST['p_price']);
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $c_id   = intval($_POST['c_id']);
    $brand_id = intval($_POST['brand_id']);
    $type   = mysqli_real_escape_string($conn, $_POST['p_type']);

    /* จัดการสต็อก */
    $stocks = $_POST['stock_qty'] ?? [];

    $total_qty = 0;
    $available_sizes = [];

    mysqli_query($conn,
        "DELETE FROM product_stock WHERE p_id=$id"
    );

    foreach ($stocks as $size => $qty) {

        $qty = intval($qty);

        if ($qty > 0) {

            mysqli_query($conn,
                "INSERT INTO product_stock (p_id, p_size, p_qty_stock)
                 VALUES ($id, '$size', $qty)"
            );

            $total_qty += $qty;
            $available_sizes[] = $size;
        }
    }

    $p_size_str = implode(",", $available_sizes);

    $discount = intval($_POST['discount_percent'] ?? 0);
    $is_promo = isset($_POST['is_promo']) ? 1 : 0;

    mysqli_query($conn,
        "UPDATE products SET
            p_name='$name',
            p_price='$price',
            discount_percent='$discount',
            is_promo='$is_promo',
            p_qty='$total_qty',
            p_size='$p_size_str',
            p_type='$type',
            p_detail='$detail',
            c_id='$c_id',
            brand_id='$brand_id'
         WHERE p_id=$id"
    );

    /* เพิ่มรูปใหม่ */
    if (isset($_FILES['p_img']) && !empty($_FILES['p_img']['name'][0])) {

        foreach ($_FILES['p_img']['name'] as $key => $val) {

            if ($_FILES['p_img']['error'][$key] === 0) {

                $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));

                $allowed = ['jpg','jpeg','png','gif','webp'];

                if (in_array($ext, $allowed)) {

                    $new_name = "product_" . time() . "_" . uniqid() . "." . $ext;

                    $target_path = $upload_dir . $new_name;

                    if (move_uploaded_file($_FILES['p_img']['tmp_name'][$key], $target_path)) {

                        $db_path = "uploads/products/" . $new_name;

                        mysqli_query($conn,
                            "INSERT INTO product_images (p_id,img_path)
                             VALUES ($id,'$db_path')"
                        );
                    }
                }
            }
        }
    }

    echo "<script>
            alert('อัปเดตข้อมูลและสต็อกเรียบร้อย');
            window.location='admin_product.php';
          </script>";

    exit();
}
?>