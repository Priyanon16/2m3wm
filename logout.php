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
            --bg-black: #ffffff;      
            --card-bg: #1a1a1a;       
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
            background-color: var(--bg-black);
            /* ตั้งค่าฟอนต์ Kanit เป็นฟอนต์หลัก */
            font-family: 'Kanit', sans-serif;
            color: var(--text-white);
        }

        .logout-card {
            background-color: var(--card-bg);
            padding: 50px 40px;
            border-radius: 24px; 
            text-align: center;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            border: 1px solid #333;
        }

        .brand-name {
            color: var(--primary-orange);
            font-size: 24px;
            font-weight: 600; /* ใช้ Kanit Semi-Bold */
            letter-spacing: 1px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600; /* ใช้ Kanit Semi-Bold */
        }

        p {
            color: var(--text-gray);
            margin-bottom: 35px;
            font-weight: 300; /* ใช้ Kanit Light */
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
            font-weight: 500; /* ใช้ Kanit Medium */
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
            border-color: #666;
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
            <svg width="35" height="35" fill="currentColor" viewBox="0 0 16 16">
                <path d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                <path d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
            </svg>
        </div>

        <h1>ยืนยันการออกจากระบบ?</h1>
        <p>คุณกำลังจะออกจากระบบ 2M3WM <br>อย่าลืมกลับมาเช็คคอลเลคชันใหม่ปี 2026 นะ!</p>
        
        <div class="button-group">
            <a href="index.php" class="btn btn-logout">ออกจากระบบ</a>
            <a href="login.php" class="btn btn-back">กลับไปเลือกสินค้าต่อ</a>
        </div>
    </div>

</body>
</html>