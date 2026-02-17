<?php
session_start();
require_once("connectdb.php");

if(isset($_POST['email'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users 
            WHERE email='$email' 
            AND password='$password' 
            LIMIT 1";

    $rs = mysqli_query($conn,$sql);

    if(mysqli_num_rows($rs) == 1){

        $data = mysqli_fetch_assoc($rs);

        // ตั้งค่า session ให้ตรงกับ header
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['user_name'] = $data['name'];
        $_SESSION['role'] = $data['role'];

        if($data['role'] == 'admin'){
            header("Location: index_admin.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        echo "<script>alert('อีเมลหรือรหัสผ่านไม่ถูกต้อง');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2M3WM - Login</title> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-orange: #ff5722;
            --hover-orange: #e64a19;
            --dark-bg: #1a1a1a;
            --input-bg: #000000;
        }

        body { 
            background-color: #ffffff; 
            font-family: 'Kanit', sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            color: white; 
        }

        .login-card { 
            background-color: var(--dark-bg); 
            padding: 45px; 
            border-radius: 16px; 
            width: 100%; 
            max-width: 400px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.5); 
            text-align: center;
            border: 1px solid #333;
        }

        .brand-name { 
            color: #ffffff; 
            font-size: 2.5rem; 
            font-weight: 700; 
            margin-bottom: 15px; 
            letter-spacing: 3px; 
        }

        h2 { margin: 0 0 8px 0; font-size: 1.8rem; font-weight: 600; }
        p.subtitle { color: #888; font-size: 0.95rem; margin-bottom: 35px; font-weight: 300; }

        .form-group { text-align: left; margin-bottom: 22px; }
        label { display: block; margin-bottom: 10px; font-weight: 400; color: #bbb; font-size: 0.9rem; }

        .input-wrapper { position: relative; }
        .input-wrapper i { 
            position: absolute; 
            left: 18px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: #666;
            font-size: 1.1rem;
        }

        input[type="email"], input[type="password"] { 
            width: 100%; 
            padding: 14px 14px 14px 50px; 
            background: var(--input-bg); 
            border: 1px solid #333; 
            border-radius: 10px; 
            color: white; 
            box-sizing: border-box; 
            outline: none; 
            font-family: 'Kanit', sans-serif;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 2px rgba(255, 87, 34, 0.2);
        }

        .btn-login { 
            width: 100%; 
            padding: 15px; 
            background-color: var(--primary-orange); 
            border: none; 
            border-radius: 10px; 
            color: white; 
            font-size: 1.1rem; 
            font-weight: 600; 
            cursor: pointer; 
            margin-top: 15px; 
            transition: all 0.3s ease; 
            font-family: 'Kanit', sans-serif; 
        }

        .btn-login:hover { 
            background-color: var(--hover-orange); 
            transform: translateY(-2px);
        }

        .footer-text { margin-top: 25px; color: #888; font-size: 0.9rem; }
        .footer-text a { color: var(--primary-orange); text-decoration: none; font-weight: 500; }
        .footer-text a:hover { text-decoration: underline; }

        .btn-back { 
            position: fixed; 
            top: 25px; 
            left: 25px; 
            background: var(--dark-bg); 
            color: white; 
            width: 50px; 
            height: 50px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            text-decoration: none; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.3); 
            transition: all 0.3s ease;
            border: 1px solid #333;
        }

        .btn-back:hover { 
            background: var(--primary-orange); 
            border-color: var(--primary-orange);
            color: #fff;
        }
    </style>
</head>
<body>
    <a href="index.php" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div class="login-card">
        <div class="brand-name">2M3WM</div> <h2>เข้าสู่ระบบ</h2>
        <p class="subtitle">ยินดีต้อนรับทุกท่าน เดินทางโดยสวัสดิภาพ</p>

        <form action="" method="POST">
            <div class="form-group">
                <label><i class="fa-regular fa-envelope me-1"></i> อีเมล</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user-tag"></i>
                    <input type="email" name="email" placeholder="example@2m3wm.com" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-key me-1"></i> รหัสผ่าน</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="******" required>
                </div>
            </div>

            <button type="submit" name="Submit" class="btn-login">
                <i class="fa-solid fa-right-to-bracket me-2"></i> เข้าสู่ระบบ
            </button>
        </form>

        <p class="footer-text">
            ยังไม่มีบัญชีใช่ไหม? <a href="register.php">คลิกที่นี่</a>
        </p>
    </div>

   
</body>
</html>