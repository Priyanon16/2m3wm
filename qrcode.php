<?php
// เริ่มต้น Session ถ้ายังไม่ได้เริ่ม
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'connectdb.php';
in

// ตั้งค่าบัญชี PromptPay ของร้านค้า
$promptpay_id = "08X-XXX-XXXX"; // <--- แก้ไขเบอร์โทร/เลขบัตรที่นี่
$bank_name = "ธนาคารกสิกรไทย";
$account_name = "นายร้านค้า ตัวอย่าง";
$account_number = "123-4-56789-0";

if(!isset($_SESSION['user_id'])){
    // ถ้ายังไม่ login ให้ไปหน้า login หรือแสดงข้อความเตือน
    // header("Location: login.php");
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อน'); window.location='login.php';</script>";
    exit();
}

if(!isset($_GET['id'])){
    // header("Location: index.php");
    echo "<script>window.location='index.php';</script>";
    exit();
}

$order_id = intval($_GET['id']);
$user_id = intval($_SESSION['user_id']);

/* =========================================
   ส่วนที่ 1: ดึงข้อมูลออเดอร์มาตรวจสอบก่อน
========================================= */
$sql = "SELECT * FROM orders WHERE o_id = ? AND u_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    echo "<div class='alert alert-danger text-center mt-5'>ไม่พบคำสั่งซื้อ หรือคุณไม่มีสิทธิ์เข้าถึง</div>";
    exit();
}

$order = $result->fetch_assoc();
$total_price = $order['total_price'];

// สร้าง URL QR Code
$qr_url = "https://promptpay.io/" . str_replace("-", "", $promptpay_id) . "/" . $total_price;


/* =========================================
   ส่วนที่ 2: บันทึกเมื่อกดปุ่ม "แจ้งโอนเงิน"
========================================= */
if(isset($_POST['upload_slip'])){
    
    if(isset($_FILES['slip_img']) && $_FILES['slip_img']['error'] == 0){
        
        $allowed = array('jpg', 'jpeg', 'png');
        $filename = $_FILES['slip_img']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if(in_array(strtolower($ext), $allowed)){
            // ตั้งชื่อไฟล์: payment_ORDERID_TIMESTAMP.jpg
            $new_name = "payment_" . $order_id . "_" . time() . "." . $ext;
            $upload_path = "uploads/slips/" . $new_name;

            if (!file_exists('uploads/slips')) {
                mkdir('uploads/slips', 0777, true);
            }

            if(move_uploaded_file($_FILES['slip_img']['tmp_name'], $upload_path)){
                
                // บันทึกลงตาราง payments
                $sql_pay = "INSERT INTO payments (order_id, user_id, slip_image, amount, pay_date) 
                            VALUES (?, ?, ?, ?, NOW())";
                $stmt_pay = $conn->prepare($sql_pay);
                $stmt_pay->bind_param("iisd", $order_id, $user_id, $new_name, $total_price);
                
                if($stmt_pay->execute()){
                    // อัปเดตสถานะออเดอร์
                    $sql_update = "UPDATE orders SET status = 'รอตรวจสอบ' WHERE o_id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("i", $order_id);
                    $stmt_update->execute();

                    echo "<script>alert('แจ้งโอนเงินเรียบร้อย รอการตรวจสอบ'); window.location='orderdetail.php?id=$order_id';</script>";
                } else {
                    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูลชำระเงิน');</script>";
                }

            } else {
                echo "<script>alert('อัปโหลดรูปภาพล้มเหลว');</script>";
            }
        } else {
            echo "<script>alert('อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG เท่านั้น');</script>";
        }
    } else {
        echo "<script>alert('กรุณาเลือกไฟล์สลิป');</script>";
    }
}
?>

<style>
    .qr-box { 
        background: #fff; border-radius: 15px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        overflow: hidden;
    }
    .header-qr { background: #003d99; color: #fff; padding: 20px; text-align: center; }
    .qr-img { width: 100%; max-width: 250px; margin: 20px auto; display: block; }
    .amount-text { color: #003d99; font-size: 2rem; font-weight: bold; text-align: center; }
    .bank-details { background: #f8f9fa; padding: 15px; border-radius: 10px; margin-top: 20px; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="qr-box">
                <div class="header-qr">
                    <h3 class="m-0">สแกน QR Code เพื่อชำระเงิน</h3>
                    <small>Order ID: #<?= str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></small>
                </div>

                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6 text-center border-end">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c5/PromptPay-logo.png" width="100" class="mb-2">
                            
                            <img src="<?= $qr_url ?>" class="qr-img" alt="PromptPay QR">
                            
                            <div class="amount-text">฿<?= number_format($total_price, 2) ?></div>
                            <p class="text-muted small mt-2">สแกนด้วยแอพธนาคารได้ทุกธนาคาร</p>
                        </div>

                        <div class="col-md-6">
                            <h5 class="fw-bold mt-3 mt-md-0">รายละเอียดบัญชี</h5>
                            <div class="bank-details">
                                <p class="mb-1"><strong>ธนาคาร:</strong> <?= $bank_name ?></p>
                                <p class="mb-1"><strong>ชื่อบัญชี:</strong> <?= $account_name ?></p>
                                <p class="mb-0"><strong>เลขบัญชี:</strong> <span class="text-primary fs-5"><?= $account_number ?></span></p>
                            </div>

                            <hr class="my-4">

                            <h5 class="fw-bold">แจ้งโอนเงิน (อัปโหลดสลิป)</h5>
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="slip_img" class="form-label text-muted">เลือกรูปภาพสลิป</label>
                                    <input type="file" class="form-control" name="slip_img" id="slip_img" required accept="image/*">
                                </div>
                                <button type="submit" name="upload_slip" class="btn btn-primary w-100 py-2">
                                    แจ้งชำระเงิน
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="index.php" class="text-muted text-decoration-none">กลับไปหน้าแรก</a>
            </div>
        </div>
    </div>
</div>