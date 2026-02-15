<?php
session_start();
include_once("check_login.php");
include_once("connectdb.php");

if (!$conn) {
    die("Connect Failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");

// ดึงหมวดหมู่
$result_category = mysqli_query($conn, "SELECT * FROM category");

if (isset($_POST['save'])) {
    $name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = $_POST['p_price'];
    $type = mysqli_real_escape_string($conn, $_POST['p_type']);
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $c_id = $_POST['c_id'];

    // 1. จัดการเรื่องไซส์ (SIZE)
    $p_size = "";
    if (isset($_POST['p_size'])) {
        $p_size = implode(",", $_POST['p_size']);
    }

    // 2. จัดการรูปภาพ
    $p_img = "";
    if (isset($_FILES['p_img']) && $_FILES['p_img']['name'] != "") {
        $ext = pathinfo($_FILES['p_img']['name'], PATHINFO_EXTENSION);
        $new_name = "product_" . uniqid() . "." . $ext;
        $upload_path = "FileUpload/" . $new_name;
        $allowed = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array(strtolower($ext), $allowed)) {
            if (move_uploaded_file($_FILES['p_img']['tmp_name'], $upload_path)) {
                $p_img = $upload_path;
            }
        }
    }

    // 3. บันทึกข้อมูล
    $sql = "INSERT INTO products (p_name, p_price, p_size, p_type, p_img, p_detail, c_id) 
            VALUES ('$name', '$price', '$p_size', '$type', '$p_img', '$detail', '$c_id')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ!'); window.location='admin_product.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสินค้าใหม่</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --theme-black: #121212;
            --theme-white: #ffffff;
            --theme-orange: #ff6600;
            --theme-orange-hover: #e65c00;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f6f9; /* !!! แก้ไข: เปลี่ยนพื้นหลังเป็นสีขาวควันบุหรี่ (ไม่ให้กลืนกับการ์ด) */
            color: #333; /* !!! แก้ไข: เปลี่ยนสีตัวหนังสือหลักเป็นสีเข้ม */
        }

        /* Card Styles */
        .custom-card {
            background-color: var(--theme-white);
            color: #333;
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0, 0.1); /* ปรับเงาให้นุ่มลงสำหรับพื้นหลังขาว */
            overflow: hidden;
        }

        .custom-card-header {
            background-color: #000; /* หัวการ์ดยังคงสีดำเพื่อให้ตัดกับสีส้ม */
            color: var(--theme-orange);
            padding: 1.5rem;
            border-bottom: 3px solid var(--theme-orange);
        }

        /* Form Inputs */
        .form-label {
            font-weight: 500;
            color: #444;
        }
        
        .form-control, .form-select {
            border: 1px solid #ddd;
            padding: 0.7rem;
            border-radius: 8px;
            background-color: #fff;
            color: #333;
        }

        /* Focus State (สีส้ม) */
        .form-control:focus, .form-select:focus {
            border-color: var(--theme-orange);
            box-shadow: 0 0 0 0.25rem rgba(255, 102, 0, 0.25);
        }

        /* Size Box Area */
        .size-selection-area {
            background-color: #f8f9fa;
            border: 1px dashed #ccc;
            border-radius: 10px;
            color: #333 !important;
        }

        .form-check-input:checked {
            background-color: var(--theme-orange);
            border-color: var(--theme-orange);
        }
        
        .form-check-label {
            color: #333;
            cursor: pointer;
        }

        /* Buttons */
        .btn-theme-orange {
            background-color: var(--theme-orange);
            color: #fff;
            border: none;
            font-weight: 500;
            padding: 10px;
            border-radius: 50px;
            transition: all 0.3s;
        }
        
        .btn-theme-orange:hover {
            background-color: var(--theme-orange-hover);
            transform: translateY(-2px);
            color: #fff;
        }

        .btn-theme-cancel {
            background-color: #6c757d; /* ปรับสีปุ่มยกเลิกให้อ่อนลงเล็กน้อยให้เข้ากับธีมขาว */
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 10px;
        }
        .btn-theme-cancel:hover {
            background-color: #5a6268;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                
                <div class="custom-card">
                    <div class="custom-card-header text-center">
                        <h3 class="mb-0 fw-bold">✨ เพิ่มสินค้าใหม่</h3>
                        <small class="opacity-75">กรอกรายละเอียดสินค้าด้านล่าง</small>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        <form method="post" enctype="multipart/form-data">

                            <div class="mb-4">
                                <label class="form-label">ชื่อสินค้า</label>
                                <input type="text" name="p_name" class="form-control" placeholder="เช่น Nike Air Jordan..." required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">ราคา (บาท)</label>
                                    <input type="number" name="p_price" class="form-control" placeholder="0.00" required>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label class="form-label fw-bold text-uppercase" style="color: var(--theme-orange);">
                                        👟 เลือกไซส์ที่พร้อมส่ง
                                    </label>
                                    <div class="size-selection-area p-3">
                                        <div class="row g-2">
                                            <?php
                                            for ($i = 36; $i <= 45; $i++) {
                                            ?>
                                                <div class="col-4 col-sm-3 col-md-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="p_size[]" value="<?= $i; ?>" id="size<?= $i; ?>">
                                                        <label class="form-check-label small fw-bold" for="size<?= $i; ?>">
                                                            EU <?= $i; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="mt-2 text-danger small" style="font-size: 0.75rem;">* ติ๊กถูกหน้าไซส์ที่มีสินค้า</div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">ประเภท (เพศ)</label>
                                    <select name="p_type" class="form-select" required>
                                        <option value="" selected disabled>-- กรุณาเลือก --</option>
                                        <option value="male">ผู้ชาย (Men)</option>
                                        <option value="female">ผู้หญิง (Women)</option>
                                        <option value="unisex">Unisex</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">หมวดหมู่สินค้า</label>
                                    <select name="c_id" class="form-select" required>
                                        <option value="" selected disabled>-- เลือกหมวดหมู่ --</option>
                                        <?php while ($row_c = mysqli_fetch_assoc($result_category)) { ?>
                                            <option value="<?= $row_c['c_id']; ?>"><?= $row_c['c_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">รูปภาพสินค้า</label>
                                <input type="file" name="p_img" class="form-control" accept="image/*" required>
                                <div class="form-text text-muted">รองรับไฟล์ jpg, jpeg, png, gif</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">รายละเอียดสินค้า</label>
                                <textarea name="p_detail" class="form-control" rows="4" placeholder="ระบุรายละเอียดสินค้า..."></textarea>
                            </div>

                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" name="save" class="btn btn-theme-orange btn-lg shadow-sm">
                                    💾 บันทึกข้อมูล
                                </button>
                                <a href="admin_product.php" class="btn btn-theme-cancel">
                                    ยกเลิก
                                </a>
                            </div>
                        </form>

                    </div>
                </div> 
            </div>
        </div>
    </div>
</body>
</html>