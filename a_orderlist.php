<?php
$title = "จัดการออเดอร์";
require_once 'connectdb.php';
include 'bootstrap.php';

// ปิด Notice เพื่อความสะอาดของหน้าจอ
error_reporting(E_ALL ^ E_NOTICE); 

/* ===========================================
   ส่วนที่ 1: อัปเดตสถานะ (เพิ่มระบบตัดสต็อก)
=========================================== */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_update_status'])) {
    $oid = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);

    // 1. ตรวจสอบสถานะเดิมก่อน (ป้องกันการตัดสต็อกซ้ำ)
    $q_old = mysqli_query($conn, "SELECT status FROM orders WHERE o_id = $oid");
    $row_old = mysqli_fetch_assoc($q_old);
    $old_status = $row_old['status'];

    // 2. อัปเดตสถานะ
    $sql_update = "UPDATE orders SET status = '$new_status' WHERE o_id = $oid";
    if (mysqli_query($conn, $sql_update)) {

        // --- เริ่มส่วนตัดสต็อก ---
        // ทำงานเมื่อเปลี่ยนเป็น "ชำระแล้ว" และของเดิมต้องยังไม่จ่าย/ยังไม่ส่ง
        if ($new_status == 'ชำระแล้ว' && $old_status != 'ชำระแล้ว' && $old_status != 'จัดส่งแล้ว') {
            
            // ดึงรายการสินค้า (ใช้ชื่อ field: p_id, size, q_ty ตามตาราง order_details ของคุณ)
            $sql_items = "SELECT p_id, size, q_ty FROM order_details WHERE o_id = $oid";
            $rs_items = mysqli_query($conn, $sql_items);

            while ($item = mysqli_fetch_assoc($rs_items)) {
                $pid  = $item['p_id'];
                $size = $item['size']; 
                $qty  = $item['q_ty'];

                // สั่งตัดสต็อก (ถ้ามีข้อมูลไซส์)
                if(!empty($size)){
                    // ตาราง product_stock ใช้ field: p_id, p_size, p_qty_stock
                    $sql_cut = "UPDATE product_stock 
                                SET p_qty_stock = p_qty_stock - $qty 
                                WHERE p_id = $pid AND p_size = '$size'";
                    mysqli_query($conn, $sql_cut);
                }
            }
        }
        // --- จบส่วนตัดสต็อก ---

        echo "<script>alert('อัปเดตสถานะเรียบร้อยแล้ว'); window.location='a_orderlist.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

/* ===========================================
   ส่วนที่ 2: ดึงข้อมูล
=========================================== */
$status   = $_GET['status'] ?? '';
$date     = $_GET['date'] ?? '';
$keyword  = $_GET['keyword'] ?? '';

// SQL: JOIN ให้ถูกต้อง (u.id)
$sql = "
SELECT 
    o.o_id,
    o.o_date,
    o.total_price,
    o.status,
    o.payment_method,
    p.slip_image,
    u.name AS customer_name,
    COALESCE(SUM(od.q_ty), 0) as total_qty
FROM orders o
LEFT JOIN users u ON o.u_id = u.id 
LEFT JOIN order_details od ON od.o_id = o.o_id
LEFT JOIN payments p ON o.o_id = p.order_id
WHERE 1
";

if (!empty($status)) {
    $s = mysqli_real_escape_string($conn, $status);
    $sql .= " AND o.status = '$s' ";
}
if (!empty($date)) {
    $d = mysqli_real_escape_string($conn, $date);
    $sql .= " AND DATE(o.o_date) = '$d' ";
}
if (!empty($keyword)) {
    $kw = mysqli_real_escape_string($conn, $keyword);
    $sql .= " AND (o.o_id LIKE '%$kw%' OR u.name LIKE '%$kw%') ";
}

$sql .= " GROUP BY o.o_id ORDER BY o.o_date DESC ";
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <style>
        body{ margin:0; background:#f4f6f9; font-family: 'Kanit', sans-serif;}
        .wrapper{ display:flex; min-height:100vh; }
        main{ flex:1; padding:20px; }
        
        .page-header{
            background:#fff; padding:20px 25px; border-radius:10px;
            border-left:5px solid #ff7a00; box-shadow:0 5px 15px rgba(0,0,0,0.05); margin-bottom:20px;
        }
        .card-custom{
            background:#fff; border-radius:10px; border:1px solid #eee;
            box-shadow:0 5px 15px rgba(0,0,0,0.03); padding: 20px; margin-bottom: 20px;
        }
        .btn-orange{ background:#ff7a00; color:#fff; border:none; }
        .btn-orange:hover{ background:#e66e00; color:#fff; }
        
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; color: white; display: inline-block; min-width: 80px;}
        .st-wait-pay { background-color: #ffc107; color: #000; } 
        .st-check { background-color: #17a2b8; } 
        .st-paid { background-color: #28a745; } 
        .st-ship { background-color: #007bff; } 
        .st-cancel { background-color: #dc3545; } 
    </style>
</head>
<body>
<div class="wrapper">
    <?php include 'sidebar.php'; ?> 
    <main>
        <div class="page-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-box-seam me-2" style="color:#ff7a00;"></i> จัดการออเดอร์</h4>
        </div>

        <div class="card-custom">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">สถานะ</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">-- ทั้งหมด --</option>
                            <option value="รอชำระเงิน" <?= $status=='รอชำระเงิน'?'selected':''?>>รอชำระเงิน</option>
                            <option value="รอตรวจสอบ" <?= $status=='รอตรวจสอบ'?'selected':''?>>รอตรวจสอบ</option>
                            <option value="ชำระแล้ว" <?= $status=='ชำระแล้ว'?'selected':''?>>ชำระแล้ว</option>
                            <option value="จัดส่งแล้ว" <?= $status=='จัดส่งแล้ว'?'selected':''?>>จัดส่งแล้ว</option>
                            <option value="ยกเลิก" <?= $status=='ยกเลิก'?'selected':''?>>ยกเลิก</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">วันที่</label>
                        <input type="date" name="date" class="form-control form-control-sm" value="<?= $date ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">ค้นหา</label>
                        <input type="text" name="keyword" class="form-control form-control-sm" placeholder="เลข Order / ชื่อ" value="<?= htmlspecialchars($keyword) ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-orange btn-sm w-100"><i class="bi bi-search"></i> ค้นหา</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-custom p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>วันที่</th>
                            <th>ลูกค้า</th>
                            <th>ยอดรวม</th>
                            <th>ช่องทาง</th>
                            <th>หลักฐาน</th>
                            <th>สถานะ</th>
                            <th>เปลี่ยนสถานะ</th>
                            <th>รายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($result && mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="fw-bold">#<?= str_pad($row['o_id'], 6, '0', STR_PAD_LEFT); ?></td>
                            <td><?= date("d/m/Y H:i", strtotime($row['o_date'])) ?></td>
                            <td class="text-start"><?= htmlspecialchars($row['customer_name'] ?? '-') ?></td>
                            <td class="fw-bold text-success"><?= number_format($row['total_price'], 0) ?> ฿</td>
                            <td>
                                <?php if($row['payment_method'] == 'cod'): ?>
                                    <span class="badge bg-dark">เก็บปลายทาง</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">โอนจ่าย</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!empty($row['slip_image'])): ?>
                                    <a href="uploads/slips/<?= $row['slip_image'] ?>" target="_blank" class="btn btn-sm btn-outline-primary py-0">ดูสลิป</a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $st = $row['status'];
                                    $cls = 'bg-secondary';
                                    if($st=='รอชำระเงิน') $cls='st-wait-pay';
                                    elseif($st=='รอตรวจสอบ') $cls='st-check';
                                    elseif($st=='ชำระแล้ว' || $st=='รอรับ') $cls='st-paid';
                                    elseif($st=='จัดส่งสำเร็จ' || $st=='จัดส่งแล้ว') $cls='st-ship';
                                    elseif($st=='ยกเลิก') $cls='st-cancel';
                                ?>
                                <span class="status-badge <?= $cls ?>"><?= $st ?></span>
                            </td>
                            <td>
                                <form method="POST" class="d-flex gap-1 justify-content-center">
                                    <input type="hidden" name="order_id" value="<?= $row['o_id'] ?>">
                                    <select name="new_status" class="form-select form-select-sm" style="width: 110px; font-size:0.8rem;">
                                        <option value="รอชำระเงิน" <?= $st=='รอชำระเงิน'?'selected':''?>>รอชำระเงิน</option>
                                        <option value="รอตรวจสอบ" <?= $st=='รอตรวจสอบ'?'selected':''?>>รอตรวจสอบ</option>
                                        <option value="ชำระแล้ว" <?= ($st=='ชำระแล้ว'||$st=='รอรับ')?'selected':''?>>ชำระแล้ว</option>
                                        <option value="จัดส่งแล้ว" <?= ($st=='จัดส่งแล้ว'||$st=='จัดส่งสำเร็จ')?'selected':''?>>จัดส่งแล้ว</option>
                                        <option value="ยกเลิก" <?= $st=='ยกเลิก'?'selected':''?>>ยกเลิก</option>
                                    </select>
                                    <button type="submit" name="btn_update_status" class="btn btn-sm btn-success" title="บันทึก"><i class="bi bi-check-lg"></i></button>
                                </form>
                            </td>
                            <td>
                                <a href="a_order_detail.php?id=<?= $row['o_id'] ?>" class="btn btn-sm btn-outline-dark"><i class="bi bi-list"></i> ดูรายละเอียด</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="py-4 text-muted">ไม่พบข้อมูลออเดอร์</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>