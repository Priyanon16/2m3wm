<?php
session_start();
include_once("connectdb.php"); // เชื่อมต่อฐานข้อมูลโดยใช้ค่าที่ได้จาก ReadyIDC

$error = "";

if (isset($_POST['Submit'])) {
    // รับค่าจากฟอร์มและป้องกัน SQL Injection
    $name     = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm  = $_POST['confirm_password'];
    $role     = 'member'; // กำหนดสิทธิ์เริ่มต้นเป็น member

    // 1. ตรวจสอบว่ารหัสผ่านตรงกันหรือไม่
    if ($password !== $confirm) {
        $error = "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน";
    } else {
        // 2. ตรวจสอบอีเมลซ้ำในฐานข้อมูล
        $check_sql = "SELECT email FROM users WHERE email = '$email' LIMIT 1";
        $rs_check  = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($rs_check) > 0) {
            $error = "อีเมลนี้ถูกใช้งานแล้ว กรุณาใช้อีเมลอื่น";
        } else {
            // 3. บันทึกข้อมูลลงตาราง users
            $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>
                    alert('สมัครสมาชิกสำเร็จ!');
                    window.location='login.php';
                </script>";
                exit;
            } else {
                $error = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SNEAKERHUB - Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #ffffff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: 'Kanit', sans-serif; }
        .register-container { background-color: #000000; padding: 40px; border-radius: 10px; width: 100%; max-width: 400px; text-align: center; border: 1px solid #333; }
        .logo { color: #ffffff; font-size: 28px; font-weight: bold; margin-bottom: 20px; }
        h2 { color: #fff; margin-bottom: 5px; }
        p.subtitle { color: #888; margin-bottom: 30px; font-size: 14px; }
        .input-group { text-align: left; margin-bottom: 15px; }
        .input-group label { display: block; color: #fff; margin-bottom: 8px; font-size: 14px; }
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #888; }
        .input-wrapper input { width: 100%; padding: 12px 15px 12px 45px; background-color: #000; border: 1px solid #333; border-radius: 5px; color: #fff; box-sizing: border-box; outline: none; font-family: 'Kanit', sans-serif; }
        .input-wrapper input:focus { border-color: #FF5722; }
        .btn-submit { width: 100%; padding: 12px; background-color: #FF5722; border: none; border-radius: 5px; color: #fff; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 20px; font-family: 'Kanit', sans-serif; }
        .footer-text { margin-top: 20px; color: #888; font-size: 14px; }
        .footer-text a { color: #FF5722; text-decoration: none; }
        .btn-back { position: fixed; top: 25px; left: 25px; background: #1a1a1a; color: white; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 5px 15px rgba(0,0,0,0.4); transition: .3s; }
        .btn-back:hover { background: #ff5722; }
        .alert-error { background-color: #f44336; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>

<a href="login.php" class="btn-back">
    <i class="fa-solid fa-arrow-left"></i>
</a>

<div class="register-container">
    <div class="logo">SNEAKERHUB</div>
    <h2>สมัครสมาชิก</h2>
    <p class="subtitle">สร้างบัญชีใหม่เพื่อเริ่มช้อปปิ้ง</p>

    <?php if ($error != ""): ?>
        <div class="alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="input-group">
            <label>ชื่อ-นามสกุล</label>
            <div class="input-wrapper">
                <i class="fa-regular fa-user"></i>
                <input type="text" name="fullname" placeholder="ชื่อ นามสกุล" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>
            </div>
        </div>

        <div class="input-group">
            <label>อีเมล</label>
            <div class="input-wrapper">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" name="email" placeholder="your@email.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
        </div>

        <div class="input-group">
            <label>รหัสผ่าน</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="อย่างน้อย 6 ตัวอักษร" minlength="6" required>
            </div>
        </div>

        <div class="input-group">
            <label>ยืนยันรหัสผ่าน</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="ยืนยันรหัสผ่าน" required>
            </div>
        </div>

        <button type="submit" name="Submit" class="btn-submit">สมัครสมาชิก</button>
    </form>

    <div class="footer-text">
        มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a>
    </div>
</div>

</body>
</html>