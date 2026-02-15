<?php
session_start();

/* ถ้ากดปุ่มยืนยัน logout */
if(isset($_GET['confirm'])){

    $_SESSION = [];
    session_destroy();

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>2M3WM - ออกจากระบบ</title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --primary-orange: #ff5f1f; 
    --bg-white: #ffffff;      
    --card-bg: #111;       
    --text-white: #ffffff;
    --text-gray: #a0a0a0;
}

body {
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: var(--bg-white);
    font-family: 'Kanit', sans-serif;
}

.logout-card {
    background-color: var(--card-bg);
    padding: 50px 40px;
    border-radius: 24px; 
    text-align: center;
    max-width: 450px;
    width: 90%;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    border: 1px solid #222;
    color: var(--text-white);
}

.brand-name {
    color: var(--primary-orange);
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 20px;
}

h1 {
    font-size: 26px;
    margin-bottom: 10px;
}

p {
    color: var(--text-gray);
    margin-bottom: 35px;
}

.button-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.btn {
    padding: 14px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.3s;
    text-decoration: none;
    font-family: 'Kanit', sans-serif;
}

.btn-logout {
    background-color: var(--primary-orange);
    color: white;
    border: none;
}

.btn-logout:hover {
    background-color: #e5551b;
    transform: scale(1.02);
}

.btn-back {
    background-color: transparent;
    color: white;
    border: 1px solid #444;
}

.btn-back:hover {
    background-color: #333;
}
.status-icon {
    width: 70px;
    height: 70px;
    background: rgba(255, 95, 31, 0.1);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto 25px;
    color: var(--primary-orange);
    font-size: 30px;
}
</style>
</head>

<body>

<div class="logout-card">

    <div class="brand-name">2M3WM</div>

    <div class="status-icon">
        ⎋
    </div>

    <h1>ยืนยันการออกจากระบบ?</h1>
    <p>คุณกำลังจะออกจากระบบ 2M3WM<br>อย่าลืมกลับมาเช็คคอลเลคชันใหม่ปี 2026 นะ!</p>

    <div class="button-group">
        <!-- ปุ่มนี้จะลบ session จริง -->
        <a href="logout.php?confirm=1" class="btn btn-logout">
            ออกจากระบบ
        </a>

        <a href="index.php" class="btn btn-back">
            กลับไปเลือกสินค้าต่อ
        </a>
    </div>

</div>

</body>
</html>
