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
    $qty = $_POST['p_qty']; // --- เพิ่มส่วนรับค่าจำนวนสินค้า ---
    $type = mysqli_real_escape_string($conn, $_POST['p_type']);
    $detail = mysqli_real_escape_string($conn, $_POST['p_detail']);
    $c_id = $_POST['c_id'];

    // 1. จัดการเรื่องไซส์ (SIZE)
    $p_size = "";
    if (isset($_POST['p_size'])) {
        $p_size = implode(",", $_POST['p_size']);
    }

    // 2. จัดการรูปภาพ (หลายรูป)
    $p_img = "";
    $uploaded_files = array(); 
    $error_messages = array(); 

    if (isset($_FILES['p_img'])) {
        $countfiles = count($_FILES['p_img']['name']); 

        for ($i = 0; $i < $countfiles; $i++) {
            if ($_FILES['p_img']['name'][$i] != "") {
                
                $file_name = $_FILES['p_img']['name'][$i];
                $file_size = $_FILES['p_img']['size'][$i];
                $file_tmp = $_FILES['p_img']['tmp_name'][$i];
                
                if ($file_size > 10485760) {
                    $error_messages[] = "ไฟล์ '$file_name' มีขนาดใหญ่เกิน 10MB";
                    continue; 
                }

                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_name = "product_" . time() . "_" . $i . "_" . uniqid() . "." . $ext;
                $upload_path = "FileUpload/" . $new_name;
                
                $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                if (in_array(strtolower($ext), $allowed)) {
                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        $uploaded_files[] = $upload_path; 
                    }
                } else {
                    $error_messages[] = "ไฟล์ '$file_name' นามสกุลไม่ถูกต้อง";
                }
            }
        }
    }

    if (!empty($uploaded_files)) {
        $p_img = implode(",", $uploaded_files);
    }

    if (!empty($error_messages)) {
        $err_str = implode("\\n", $error_messages);
        echo "<script>alert('แจ้งเตือนการอัปโหลด:\\n$err_str');</script>";
    }

    // 3. บันทึกข้อมูลลงฐานข้อมูล (เพิ่ม p_qty เข้าไปใน SQL)
    $sql = "INSERT INTO products (p_name, p_price, p_qty, p_size, p_type, p_img, p_detail, c_id) 
            VALUES ('$name', '$price', '$qty', '$p_size', '$type', '$p_img', '$detail', '$c_id')";

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้าใหม่ - 2M3WM Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --theme-orange: #ff5722;
            --theme-orange-hover: #e64a19;
            --theme-dark: #1a1a1a;
            --theme-bg: linear-gradient(135deg, #f8f9fa, #eef1f4);
        }

        body {
            font-family: 'Kanit', sans-serif;
            background: var(--theme-bg);
            min-height: 100vh;
            color: #333;
        }

        header {
            background: linear-gradient(90deg, #111, var(--theme-dark));
            padding: 1rem 0;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            margin-bottom: 2rem;
        }
        
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 2px;
            color: #fff !important;
        }

        .btn-theme {
            background: var(--theme-orange);
            color: white !important;
            border: none;
            border-radius: 50px;
            padding: 10px 30px;
            transition: .3s;
            font-weight: 500;
        }

        .btn-theme:hover {
            background: var(--theme-orange-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 87, 34, 0.3);
        }

        .content-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-top: 5px solid var(--theme-orange);
            margin-bottom: 2rem;
            max-width: 850px;
            margin-left: auto;
            margin-right: auto;
        }

        .card-title-custom {
            color: var(--theme-dark);
            font-weight: 700;
        }

        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 1px solid #ddd;
            padding: 0.75rem;
            border-radius: 10px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--theme-orange);
            box-shadow: 0 0 0 0.25rem rgba(255, 87, 34, 0.25);
        }

        .size-box {
            background: #f8f9fa;
            border: 1px dashed #ccc;
            border-radius: 12px;
            padding: 20px;
        }
        
        .form-check-input:checked {
            background-color: var(--theme-orange);
            border-color: var(--theme-orange);
        }
    </style>
</head>
<body>

    <header>
        <div class="container d-flex align-items-center justify-content-between">
            <a class="navbar-brand" href="index_admin.php">
                <i class="bi bi-shield-check me-2"></i>2M3WM ADMIN
            </a>
            <div class="d-flex align-items-center gap-4">
                <a href="logout.php" class="btn btn-theme text-decoration-none" style="padding: 8px 22px;">
                    <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="mb-4 text-center">
            <a href="admin_product.php" class="text-secondary text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> กลับไปหน้ารายการสินค้า
            </a>
        </div>

        <div class="content-card">
            <div class="text-center mb-5">
                <h2 class="card-title-custom">เพิ่มสินค้าใหม่</h2>
                <p class="text-muted">กรอกข้อมูลรองเท้าให้ครบถ้วนเพื่อวางจำหน่าย</p>
            </div>

            <form method="post" enctype="multipart/form-data">
                
                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-tag me-2"></i>ชื่อสินค้า / รุ่นรองเท้า</label>
                    <input type="text" name="p_name" class="form-control" placeholder="เช่น Nike Dunk Low Retro" required>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label"><i class="bi bi-currency-dollar me-2"></i>ราคา (บาท)</label>
                        <input type="number" name="p_price" class="form-control" placeholder="0" required>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label"><i class="bi bi-box-seam me-2"></i>จำนวนคงเหลือ (คู่)</label>
                        <input type="number" name="p_qty" class="form-control" placeholder="0" min="0" required>
                    </div>

                    <div class="col-md-4 mb-4">
                        <label class="form-label"><i class="bi bi-folder me-2"></i>หมวดหมู่</label>
                        <select name="c_id" class="form-select" required>
                            <option value="" selected disabled>-- เลือกหมวด --</option>
                            <?php 
                            if(mysqli_num_rows($result_category) > 0){
                                mysqli_data_seek($result_category, 0);
                                while ($row_c = mysqli_fetch_assoc($result_category)) { ?>
                                    <option value="<?= $row_c['c_id']; ?>"><?= $row_c['c_name']; ?></option>
                            <?php 
                                } 
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-gender-ambiguous me-2"></i>ประเภทสินค้า</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="p_type" id="m" value="male" required>
                            <label class="form-check-label" for="m">ชาย</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="p_type" id="f" value="female">
                            <label class="form-check-label" for="f">หญิง</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="p_type" id="u" value="unisex">
                            <label class="bi bi-gender-ambiguous me-2">Unisex</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-rulers me-2"></i>ไซส์ที่เปิดให้สั่งซื้อ (EU)</label>
                    <div class="size-box">
                        <div class="row g-2">
                            <?php for ($i = 36; $i <= 46; $i++) { ?>
                            <div class="col-3 col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="p_size[]" value="<?= $i; ?>" id="z<?= $i; ?>">
                                    <label class="form-check-label" for="z<?= $i; ?>"><?= $i; ?></label>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-image me-2"></i>รูปภาพสินค้า (เลือกได้หลายรูป)</label>
                    <input type="file" name="p_img[]" class="form-control" accept="image/*" multiple required>
                </div>

                <div class="mb-5">
                    <label class="form-label"><i class="bi bi-file-text me-2"></i>รายละเอียด/สเปครองเท้า</label>
                    <textarea name="p_detail" class="form-control" rows="4"></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" name="save" class="btn btn-theme btn-lg">
                        <i class="bi bi-save me-2"></i>บันทึกและลงขาย
                    </button>
                    <a href="admin_product.php" class="btn btn-outline-secondary">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>