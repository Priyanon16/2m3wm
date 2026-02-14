<?php
    session_start();

    // --- กำหนดค่า Admin จำลองเพื่อใช้ในการพัฒนา ---
    $_SESSION['aid'] = 1; // กำหนด ID เป็น 1 เพื่อให้ผ่านเงื่อนไข empty
    $_SESSION['aname'] = "Preeyanon Admin"; // ชื่อที่จะปรากฏในหน้า Dashboard

    /* // คอมเมนต์ส่วนตรวจสอบจริงไว้ก่อนในช่วงกู้คืนรหัสผ่านฐานข้อมูล
    if (empty($_SESSION['aid'])) {
        echo "Access Denied" ;
        echo "<meta http-equiv='refresh' content='4; url=index.php'>";
        exit;
    }
    */
    
?>