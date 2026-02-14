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
            /* เปลี่ยนจาก Sarabun เป็น Kanit */
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
            font-weight: 700; /* ปรับความหนาให้ดูโมเดิร์น */
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        h2 { margin-bottom: 5px; font-size: 1.8rem; font-weight: 600; }
        p.subtitle { color: #888; font-size: 0.9rem; margin-bottom: 30px; font-weight: 300; }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        label { display: block; margin-bottom: 8px; font-weight: 400; }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px 12px 12px 45px;
            background: #000;
            border: 1px solid #333;
            border-radius: 8px;
            color: white;
            box-sizing: border-box;
            outline: none;
            /* ต้องระบุ font-family ใน input ด้วยเพื่อให้ฟอนต์ตรงกัน */
            font-family: 'Kanit', sans-serif;
        }

        .forgot-pass {
            text-align: right;
            margin-top: 10px;
        }

        .forgot-pass a { color: #ff5722; text-decoration: none; font-size: 0.85rem; }

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

        .btn-back{
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

.btn-back:hover{
    background: #ff5722;
}

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
            Username <input type="text" name="auser" autofocus required><br>
            Password <input type="password" name="apwd" required><br>

            <div class="form-group">
                <label>รหัสผ่าน</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="********" required>
                </div>
                <div class="forgot-pass">
                    <a href="#">ลืมรหัสผ่าน?</a>
                </div>
            </div>

            <button type="submit" class="btn-login">เข้าสู่ระบบ</button>
        </form>

        <p class="footer-text">
            ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a>
        </p>
    </div>

<?php
if(isset($_POST['Submit'])) {
    include_once("connectdb.php");
    $sql = "SELECT * FROM admin WHERE a_username='{$_POST['auser']}' AND a_password='{$_POST['apwd']}' LIMIT 1 ";
    $rs = mysqli_query($conn,$sql);
    $num = mysqli_num_rows($rs);

    if($num == 1) {
        $data = mysqli_fetch_array($rs);
        $_SESSION['aid'] = $data['a_id'];
        $_SESSION['aname'] = $data['a_name'];
        echo"<script>" ;
        echo "window.location='index.php';";
        echo "</script>";
    } else {
        echo"<script>" ;
        echo "alert('Username หรือ Password ไม่ถูกต้อง');";
        echo "</script>";
    }

}
?>


</body>
</html>