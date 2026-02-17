<?php
session_start();
include_once("connectdb.php");
include_once("bootstrap.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = intval($_SESSION['user_id']);

/* ==========================
   Filter สถานะ
========================== */
$filter = $_GET['status'] ?? 'ทั้งหมด';

/* ==========================
   ดึงรายการออเดอร์ (ใช้ Prepared Statement)
========================== */

if($filter == 'ทั้งหมด'){
    $stmt = $conn->prepare("SELECT * FROM orders WHERE u_id = ? ORDER BY o_id DESC");
    $stmt->bind_param("i", $uid);
} else {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE u_id = ? AND status = ? ORDER BY o_id DESC");
    $stmt->bind_param("is", $uid, $filter);
}

$stmt->execute();
$rs = $stmt->get_result();
?>
