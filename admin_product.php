<?php
session_start();
include_once("check_login.php");
include_once("connectdb.php");
include_once("bootstrap.php");
mysqli_set_charset($conn,"utf8");

/* =========================
   Config Pagination
========================= */
$perpage = 10; 
if (isset($_GET['page']) && (int)$_GET['page'] > 0) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}
$start = ($page - 1) * $perpage;

/* =========================
   ลบสินค้า
========================= */
if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);

    $img = mysqli_query($conn,"SELECT p_img FROM products WHERE p_id=$id");
    $imgRow = mysqli_fetch_assoc($img);

    if(mysqli_query($conn,"DELETE FROM products WHERE p_id=$id")){
        if(!empty($imgRow['p_img']) && file_exists($imgRow['p_img'])){
            unlink($imgRow['p_img']);
        }
        
        mysqli_query($conn, "DELETE FROM product_stock WHERE p_id=$id");
        mysqli_query($conn, "DELETE FROM product_images WHERE p_id=$id");

        echo "<script>alert('ลบสินค้าและข้อมูลสต็อกเรียบร้อย');window.location='admin_product.php';</script>";
        exit();
    }
}

/* =========================
   ดึงแบรนด์
========================= */
$brand_rs = mysqli_query($conn,"SELECT * FROM brand ORDER BY brand_name ASC");

/* =========================
   Filter Condition (แก้ไขเพิ่ม Search)
========================= */
$where = "";
$param_url = ""; 
$search_text = ""; // [เพิ่ม] ตัวแปรเก็บคำค้นหา

// 1. เช็ค Brand
if(isset($_GET['brand_id']) && $_GET['brand_id'] != ""){
    $brand_id = intval($_GET['brand_id']);
    $where = "WHERE p.brand_id = $brand_id";
    $param_url .= "&brand_id=".$brand_id;
}

// 2. เช็ค Search [เพิ่มส่วนนี้]
if(isset($_GET['search']) && $_GET['search'] != ""){
    $search_text = mysqli_real_escape_string($conn, $_GET['search']);
    // ถ้ามี WHERE อยู่แล้ว ให้ใช้ AND ต่อท้าย ถ้าไม่มีให้ใช้ WHERE นำหน้า
    if($where == ""){
        $where = "WHERE p.p_name LIKE '%$search_text%'";
    } else {
        $where .= " AND p.p_name LIKE '%$search_text%'";
    }
    $param_url .= "&search=".$search_text;
}

/* =========================
   คำนวณจำนวนหน้า
========================= */
$sql_count = "SELECT p.p_id FROM products p $where";
$query_count = mysqli_query($conn, $sql_count);
$total_record = mysqli_num_rows($query_count);
$total_page = ceil($total_record / $perpage);

/* =========================
   ดึงสินค้า
========================= */
$sql = "SELECT p.*, c.c_name, b.brand_name,
        (SELECT img_path FROM product_images WHERE p_id = p.p_id LIMIT 1) AS thumbnail
        FROM products p
        LEFT JOIN category c ON p.c_id = c.c_id
        LEFT JOIN brand b ON p.brand_id = b.brand_id
        $where
        ORDER BY p.p_id DESC
        LIMIT $start, $perpage";

$result = mysqli_query($conn,$sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการสินค้า</title>

<style>
body{
    font-family:'Kanit',sans-serif;
    background:#f4f6f9;
}
.layout{
    display:flex;
    min-height:100vh;
}
.main-content{
    flex:1;
    padding:30px;
}
.card{
    border:none;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,.05);
}
.table thead{
    background:#111;
    color:#fff;
}
.price{
    color:#ff5722;
    font-weight:700;
}
.btn-theme{
    background:#ff5722;
    color:#fff;
    border:none;
}
.btn-theme:hover{
    background:#e64a19;
}
.img-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #ddd;
}
.size-badge {
    font-size: 0.8rem;
    background-color: #eef2f7;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 2px 6px;
    margin: 2px;
    display: inline-block;
}
.stock-count {
    font-weight: bold;
    color: #ff5722;
}
.page-link {
    color: #333;
}
.page-item.active .page-link {
    background-color: #ff5722;
    border-color: #ff5722;
    color: white;
}

.badge.bg-danger{
    font-size:13px;
    padding:6px 10px;
}
</style>
</head>

<body>

<div class="layout">

    <?php include("sidebar.php"); ?>

    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">
                <i class="bi bi-box-seam text-warning me-2"></i>
                จัดการสินค้า
            </h3>

            <a href="admin_add.php" class="btn btn-theme">
                <i class="bi bi-plus-lg"></i> เพิ่มสินค้า
            </a>
        </div>

        <div class="card p-3 mb-4">
            <form method="GET" class="row align-items-end g-3"> <div class="col-md-3">
                    <label class="fw-semibold">เลือกแบรนด์</label>
                    <select name="brand_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- ทุกแบรนด์ --</option>
                        <?php while($b=mysqli_fetch_assoc($brand_rs)): ?>
                        <option value="<?= $b['brand_id']; ?>"
                            <?= (isset($_GET['brand_id']) && $_GET['brand_id']==$b['brand_id'])?'selected':''; ?>>
                            <?= $b['brand_name']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="fw-semibold">ค้นหาชื่อสินค้า</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="พิมพ์ชื่อสินค้า..." 
                               value="<?= htmlspecialchars($search_text); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i> ค้นหา
                        </button>
                    </div>
                </div>

                <?php if((isset($_GET['brand_id']) && $_GET['brand_id']!="") || (isset($_GET['search']) && $_GET['search']!="")): ?>
                <div class="col-md-2">
                    <a href="admin_product.php" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> รีเซ็ต
                    </a>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">รูป</th> 
                            <th width="20%">ชื่อสินค้า</th>
                            <th width="10%">แบรนด์</th>
                            <th width="10%">เพศ</th>
                            <th width="25%">สต็อก (ไซส์ : จำนวน)</th> 
                            <th width="10%">ราคา</th>
                            <th width="10%">โปร</th>
                            <th width="10%" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if(mysqli_num_rows($result)>0): ?>
                    <?php while($row=mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?= $row['p_id']; ?></td>
                        
                        <td>
                            <?php if(!empty($row['thumbnail'])): ?>
                                <img src="<?= $row['thumbnail']; ?>" class="img-thumb">
                            <?php else: ?>
                                <span class="text-muted small">ไม่มีรูป</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="fw-bold"><?= $row['p_name']; ?></div>
                            <small class="text-muted"><?= $row['c_name'] ?? '-'; ?></small>
                        </td>
                        <td>
                            <span class="badge bg-dark">
                                <?= $row['brand_name'] ?? '-'; ?>
                            </span>
                        </td>
                        <td>
                        <?php
                        if($row['p_type']=='male')
                            echo '<span class="badge bg-primary">Men</span>';
                        elseif($row['p_type']=='female')
                            echo '<span class="badge bg-danger">Women</span>';
                        else
                            echo '<span class="badge bg-success">Unisex</span>';
                        ?>
                        </td>

                        <td>
                            <?php
                            $pid = $row['p_id'];
                            $stock_sql = "SELECT * FROM product_stock WHERE p_id = $pid ORDER BY p_size ASC";
                            $stock_qry = mysqli_query($conn, $stock_sql);
                            $total_in_stock = 0;
                            
                            if(mysqli_num_rows($stock_qry) > 0){
                                echo '<div class="d-flex flex-wrap">';
                                while($st = mysqli_fetch_assoc($stock_qry)){
                                    $total_in_stock += $st['p_qty_stock'];
                                    $bg_style = ($st['p_qty_stock'] > 0) ? '' : 'opacity:0.5; background:#ffebeb;';
                                    echo '<div class="size-badge" style="'.$bg_style.'">';
                                    echo 'เบอร์ ' . $st['p_size'] . ' : <span class="stock-count">' . $st['p_qty_stock'] . '</span>';
                                    echo '</div>';
                                }
                                echo '</div>';
                                echo '<div class="mt-1 small text-secondary">รวมทั้งหมด: <strong>'.number_format($total_in_stock).'</strong> คู่</div>';
                            } else {
                                echo '<span class="badge bg-danger">ไม่มีข้อมูลสต็อก</span>';
                            }
                            ?>
                        </td>

                        <td class="price">
                        <?php
                        if($row['is_promo'] == 1 && $row['discount_percent'] > 0){
                            $old = $row['p_price'];
                            $new = $old - ($old * $row['discount_percent'] / 100);
                            echo '<div style="text-decoration:line-through;color:#999;font-size:13px;">฿'.number_format($old).'</div>';
                            echo '<div style="color:#ff5722;font-weight:700;">฿'.number_format($new).'</div>';
                        } else {
                            echo '฿'.number_format($row['p_price']);
                        }
                        ?>
                        </td>

                        <td>
                        <?php
                        if($row['is_promo'] == 1 && $row['discount_percent'] > 0){
                            echo '<span class="badge bg-danger">ลด '.$row['discount_percent'].'%</span>';
                        } else {
                            echo '<span class="badge bg-secondary">ปกติ</span>';
                        }
                        ?>
                        </td>
                        <td class="text-center">
                            <a href="admin_edit.php?id=<?= $row['p_id']; ?>" 
                               class="btn btn-sm btn-outline-secondary" title="แก้ไข">
                               <i class="bi bi-pencil"></i>
                            </a>

                            <a href="?delete_id=<?= $row['p_id']; ?>" 
                               onclick="return confirm('ยืนยันการลบ? ข้อมูลสต็อกจะถูกลบด้วย')"
                               class="btn btn-sm btn-outline-danger" title="ลบ">
                               <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4"> ไม่พบข้อมูลสินค้า </td>
                    </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>

            <?php if($total_record > 0): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page-1; ?><?= $param_url; ?>">ก่อนหน้า</a>
                    </li>

                    <?php for($i=1; $i<=$total_page; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i; ?><?= $param_url; ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= ($page >= $total_page) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page+1; ?><?= $param_url; ?>">ถัดไป</a>
                    </li>
                </ul>
            </nav>
            <div class="text-center text-muted small">
                แสดงหน้า <?= $page; ?> จาก <?= $total_page; ?> (ทั้งหมด <?= $total_record; ?> รายการ)
            </div>
            <?php endif; ?>

        </div>

    </div>
</div>

</body>
</html>