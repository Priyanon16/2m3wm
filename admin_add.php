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
            background-color: var(--theme-black); /* 60% พื้นหลังดำ */
            color: #ccc;
        }

        /* Card Styles */
        .custom-card {
            background-color: var(--theme-white); /* 30% การ์ดขาว */
            color: #333;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0, 0.5);
            overflow: hidden;
        }

        .custom-card-header {
            background-color: #000; /* หัวการ์ดดำ */
            color: var(--theme-orange); /* ตัวหนังสือส้ม */
            padding: 1.5rem;
            border-bottom: 3px solid var(--theme-orange);
        }

        /* Form Inputs */
        .form-label {
            font-weight: 500;
            color: #444; /* บังคับหัวข้อให้เข้ม */
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

        /* Size Box Area - แก้ไขตรงนี้ */
        .size-selection-area {
            background-color: #f8f9fa;
            border: 1px dashed #ccc;
            border-radius: 10px;
            color: #333 !important; /* !!! เพิ่มบรรทัดนี้: บังคับตัวเลขไซส์เป็นสีดำ !!! */
        }

        .form-check-input:checked {
            background-color: var(--theme-orange);
            border-color: var(--theme-orange);
        }
        
        /* แก้ไข Label ของ checkbox ให้สีเข้มด้วย */
        .form-check-label {
            color: #333;
            cursor: pointer;
        }

        /* Buttons */
        .btn-theme-orange {
            background-color: var(--theme-orange); /* 10% สีปุ่ม */
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
            background-color: #444;
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 10px;
        }
        .btn-theme-cancel:hover {
            background-color: #222;
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
                                <<?php
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
    <title>เพิ่มสินค้าใหม่</title<?php
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        :root {
            --theme-primary: #ff6600; /* สีส้มหลัก */
            --theme-primary-hover: #e65c00;
            --theme-bg: #f4f6f9;      /* สีพื้นหลัง Dashboard */
            --card-bg: #ffffff;
            --text-dark: #333333;
            --text-muted: #6c757d;
            --border-color: #e9ecef;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--theme-bg);
            color: var(--text-dark);
        }

        /* --- Card Design --- */
        .custom-card {
            background-color: var(--card-bg);
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .custom-card-header {
            background-color: #1a1a1a; /* หัวสีดำ */
            color: var(--theme-primary);
            padding: 1.5rem 2rem;
            border-bottom: 4px solid var(--theme-primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .custom-card-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }

        /* --- Form Elements --- */
        .form-label {
            font-weight: 500;
            color: #444;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--theme-primary);
            box-shadow: 0 0 0 4px rgba(255, 102, 0, 0.1);
        }

        /* --- Size Selector Styling (ไฮไลท์ส่วนนี้) --- */
        .size-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
            gap: 10px;
        }

        .size-option input[type="checkbox"] {
            display: none; /* ซ่อน Checkbox เดิม */
        }

        .size-option label {
            display: block;
            background-color: #fff;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 0;
            text-align: center;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-muted);
            transition: all 0.2s;
        }

        .size-option label:hover {
            border-color: var(--theme-primary);
            color: var(--theme-primary);
        }

        /* เมื่อติ๊กถูก ให้เปลี่ยนสี */
        .size-option input[type="checkbox"]:checked + label {
            background-color: var(--theme-primary);
            border-color: var(--theme-primary);
            color: #fff;
            box-shadow: 0 4px 10px rgba(255, 102, 0, 0.3);
            transform: translateY(-2px);
        }

        /* --- Buttons --- */
        .btn-action {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-save {
            background-color: var(--theme-primary);
            color: #fff;
            border: none;
        }
        .btn-save:hover {
            background-color: var(--theme-primary-hover);
            color: #fff;
            box-shadow: 0 5px 15px rgba(255, 102, 0, 0.3);
            transform: translateY(-2px);
        }

        .btn-cancel {
            background-color: #f1f3f5;
            color: #6c757d;
            border: none;
        }
        .btn-cancel:hover {
            background-color: #e9ecef;
            color: #495057;
        }

        /* Upload Box */
        .upload-box {
            background: #fafafa;
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <form method="post" enctype="multipart/form-data">
                    <div class="custom-card">
                        <div class="custom-card-header">
                            <div>
                                <h3><i class="bi bi-box-seam-fill me-2"></i> เพิ่มสินค้าใหม่</h3>
                                <div style="font-size: 0.85rem; opacity: 0.8; font-weight: 300;">Product Management System</div>
                            </div>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            
                            <div class="mb-4">
                                <label class="form-label">ชื่อสินค้า (Product Name)</label>
                                <input type="text" name="p_name" class="form-control form-control-lg" placeholder="เช่น Nike Air Force 1..." required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">ราคา (Price)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">฿</span>
                                        <input type="number" name="p_price" class="form-control border-start-0 ps-0" placeholder="0.00" required>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-4">
                                    <label class="form-label">ประเภท (Gender)</label>
                                    <select name="p_type" class="form-select" required>
                                        <option value="" selected disabled>- เลือก -</option>
                                        <option value="male">ชาย (Men)</option>
                                        <option value="female">หญิง (Women)</option>
                                        <option value="unisex">Unisex</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-4">
                                    <label class="form-label">หมวดหมู่</label>
                                    <select name="c_id" class="form-select" required>
                                        <option value="" selected disabled>- เลือก -</option>
                                        <?php while ($row_c = mysqli_fetch_assoc($result_category)) { ?>
                                            <option value="<?= $row_c['c_id']; ?>"><?= $row_c['c_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    <span>ขนาดไซส์ที่พร้อมส่ง (Size Availability)</span>
                                    <span class="badge bg-light text-secondary fw-normal border">EU Size</span>
                                </label>
                                
                                <div class="size-grid">
                                    <?php for ($i = 36; $i <= 45; $i++) { ?>
                                        <div class="size-option">
                                            <input type="checkbox" name="p_size[]" value="<?= $i; ?>" id="size<?= $i; ?>">
                                            <label for="size<?= $i; ?>"><?= $i; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="form-text mt-2 text-primary"><i class="bi bi-info-circle"></i> คลิกเพื่อเลือกไซส์ที่มีสินค้า</div>
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <div class="row">
                                <div class="col-md-7 mb-4">
                                    <label class="form-label">รายละเอียดสินค้า</label>
                                    <textarea name="p_detail" class="form-control" rows="5" placeholder="ระบุรายละเอียด, คุณสมบัติ, วัสดุ..."></textarea>
                                </div>
                                <div class="col-md-5 mb-4">
                                    <label class="form-label">รูปภาพสินค้า</label>
                                    <div class="upload-box">
                                        <div class="mb-3">
                                            <i class="bi bi-cloud-arrow-up display-4 text-muted"></i>
                                        </div>
                                        <input type="file" name="p_img" class="form-control form-control-sm mb-2" accept="image/*" required>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">รองรับ JPG, PNG, GIF</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="admin_product.php" class="btn btn-action btn-cancel">ยกเลิก</a>
                                <button type="submit" name="save" class="btn btn-action btn-save shadow">
                                    <i class="bi bi-save me-1"></i> บันทึกข้อมูลสินค้า
                                </button>
                            </div>

                        </div> </div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>>
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
</html>/div>

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
                </div> </div>
        </div>
    </div>
</body>
</html>