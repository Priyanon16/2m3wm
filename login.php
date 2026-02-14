<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SNEAKERHUB - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
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
            background-color: #1a1a1a; 
            padding: 40px; 
            border-radius: 12px; 
            width: 100%; 
            max-width: 400px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
            text-align: center; 
        }
        .brand-name { 
            color: #ffffff; 
            font-size: 2.5rem; 
            font-weight: 700; 
            margin-bottom: 20px; 
            letter-spacing: 2px; 
        }
        h2 { margin-bottom: 5px; font-size: 1.8rem; font-weight: 600; }
        p.subtitle { color: #888; font-size: 0.9rem; margin-bottom: 30px; font-weight: 300; }
        .form-group { text-align: left; margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 400; }
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #666; }
        input[type="email"], input[type="password"] { 
            width: 100%; 
            padding: 12px 12px 12px 45px; 
            background: #000; 
            border: 1px solid #333; 
            border-radius: 8px; 
            color: white; 
            box-sizing: border-box; 
            outline: none; 
            font-family: 'Kanit', sans-serif; 
        }
        .btn-login { 
            width: 100%; 
            padding: 14px; 
            background-color: #ff5722; 
            border: none; 
            border-radius: 8px; 
            color: white; 
            font-size: 1.1rem; 
            font-weight: 600; 
            cursor: pointer; 
            margin-top: 25px; 
            transition: 0.3s; 
            font-family: 'Kanit', sans-serif; 
        }
        .btn-login:hover { background-color: #e64a19; }
        .footer-text { margin-top: 20px; color: #888; font-size: 0.9rem; }
        .footer-text a { color: #ff5722; text-decoration: none; }
        .btn-back { 
            position: fixed; 
            top: 25px; 
            left: 25px; 
            background: #1a1a1a; 
            color: white; 
            width: 45px; 
            height: 45px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            text-decoration: none; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.4); 
            transition: .3s; 
        }
        .btn-back:hover { background: #ff5722; }
    </style>
</head>
<body>
    <a href="index.php" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div class="login-card">
        <div class="brand-name">SNEAKERHUB</div>
        <h2>เข้าสู่ระบบ</h2>
        <p class="subtitle">เข้าสู่บัญชีของคุณเพื่อดำเนินการต่อ</p>

        <form action="" method="POST">
            <div class="form-group">
                <label>อีเมล</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" placeholder="your@email.com" required>
                </div>
            </div>

            <div class="form-group">
                <label>รหัสผ่าน</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="********" required>
                </div>
            </div>

            <button type="submit" name="Submit" class="btn-login">เข้าสู่ระบบ</button>
        </form>

        <p class="footer-text">
            ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a>
        </p>
    </div>

    <?php
        if(isset($_POST['Submit'])) {
            include_once("connectdb.php");
            
            // ป้องกัน SQL Injection เบื้องต้น
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);

            // ตรวจสอบข้อมูลจากตาราง users โดยเช็คสิทธิ์ role เป็น admin
            $sql = "SELECT * FROM users WHERE email='{$email}' AND password='{$password}' AND role='admin' LIMIT 1";
            $rs = mysqli_query($conn, $sql);
            
            if($rs) {
                if(mysqli_num_rows($rs) == 1) {
                    $data = mysqli_fetch_array($rs);
                    // บันทึก Session เพื่อใช้ตรวจสอบในหน้าอื่นๆ
                    $_SESSION['aid'] = $data['id']; 
                    $_SESSION['aname'] = $data['name'];
                    
                    echo "<script>window.location='index.php';</script>";
                } else {
                    echo "<script>alert('อีเมลหรือรหัสผ่านไม่ถูกต้อง หรือคุณไม่มีสิทธิ์เข้าถึงส่วนนี้');</script>";
                }
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    ?>
</body>
</html>