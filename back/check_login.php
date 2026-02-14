<?php
    session_start();
    if (empty($_SESSION['aid'])) {
        echo "Access Denied" ;
        echo "<meta http-http-equiv='refresh' content='4; url=index.php'>";  /**สำคัญต้องใส่เผื่อปฎิเสธ */
        exit;
    }
?>