<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

include_once("connectdb.php");

$totalCart = 0;
$totalFav  = 0;

if(isset($_SESSION['user_id'])){

    $uid = intval($_SESSION['user_id']);

    $cartQ = mysqli_query($conn,"
        SELECT SUM(quantity) as total
        FROM cart
        WHERE user_id='$uid'
    ");

    $cartData = mysqli_fetch_assoc($cartQ);
    $totalCart = $cartData['total'] ?? 0;

    $favQ = mysqli_query($conn,"
        SELECT COUNT(*) as total
        FROM favorites
        WHERE user_id='$uid'
    ");

    $favData = mysqli_fetch_assoc($favQ);
    $totalFav = $favData['total'] ?? 0;
}
?>



<style>
/* ===== HEADER STYLE ===== */
.main-header{
  background:#111;
  padding:16px 0;
}

.logo{
  font-weight:700;
  font-size:22px;
  letter-spacing:1px;
  color:#fff;
}

.logo span{
  color:#ff7a00;
}

.nav-menu .nav-link{
  color:#ddd;
  font-weight:500;
  transition:.3s;
  position:relative;
}

.nav-menu .nav-link:hover{
  color:#ff7a00;
}

.nav-menu .nav-link::after{
  content:"";
  position:absolute;
  left:0;
  bottom:-6px;
  width:0%;
  height:2px;
  background:#ff7a00;
  transition:.3s;
}

.nav-menu .nav-link:hover::after{
  width:100%;
}

/* ===== ICONS ===== */
.header-icons{
  display:flex;
  align-items:center;
  gap:20px;
}


.header-icons a{
  position:relative;
  color:#fff;
  font-size:22px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  width:40px;
  height:40px;
}


.header-icons a:hover{
  color:#ff7a00;
  transform:translateY(-2px);
}

/* ===== BADGE ===== */
.icon-badge{
  position:absolute;
  top:-6px;
  right:-6px;
  min-width:18px;
  height:18px;
  padding:0 5px;
  font-size:11px;
  font-weight:600;
  border-radius:50px;
  background:#ff3b3b;
  color:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  border:2px solid #111; /* กันซ้อนกับไอคอน */
}


/* SEARCH */
.search-box{
  position:relative;
  width:250px;
}

.search-box input{
  background:#222;
  border:none;
  color:#fff;
  border-radius:50px;
  padding:8px 40px 8px 15px;
}

.search-box input::placeholder{
  color:#aaa;
}

.search-box i{
  position:absolute;
  right:15px;
  top:50%;
  transform:translateY(-50%);
  color:#aaa;
}
.nav-menu .nav-link::after{
  display:none;
}
/* ปิดสีน้ำเงินหลังคลิก */
.nav-menu .nav-link,
.nav-menu .nav-link:visited,
.nav-menu .nav-link:focus,
.nav-menu .nav-link:active {
    color: #ddd !important;
    outline: none !important;
    box-shadow: none !important;
}
.nav-menu .nav-link:hover {
    color: #ff7a00 !important;
}

</style>

<header class="main-header">
  <div class="container d-flex align-items-center justify-content-between">

    <!-- LOGO -->
    <div class="logo">
      2M<span>3WM</span>
    </div>

    
    <!-- MENU -->
    <ul class="nav nav-menu gap-4 d-none d-lg-flex">
      <li class="nav-item">
        <a class="nav-link" href="index.php">หน้าแรก</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="all_products.php">สินค้าทั้งหมด</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" 
          href="https://lin.ee/ktGP4ZD" 
          target="_blank">
          ติดต่อเรา
        </a>

      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" 
          data-bs-toggle="dropdown" aria-expanded="false">
            การตั้งค่า
        </a>

        <ul class="dropdown-menu shadow border-0 rounded-3">
            <li>
                <a class="dropdown-item" href="setting.php">
                    <i class="bi bi-person me-2 text-warning"></i>โปรไฟล์
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="orderdetail.php">
                    <i class="bi bi-clock-history me-2 text-success"></i>ประวัติการสั่งซื้อ
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="address.php">
                    <i class="bi bi-geo-alt me-2 text-danger"></i>ที่อยู่จัดส่ง
                </a>
            </li>
        </ul>
    </li>

    </ul>

    <!-- RIGHT SIDE -->
    <div class="d-flex align-items-center gap-4">

      <!-- SEARCH -->
      <div class="search-box d-none d-lg-block">
        <input type="text" placeholder="ค้นหาสินค้า...">
        <i class="bi bi-search"></i>
      </div>

      <!-- ICONS -->
      <div class="header-icons">

        <?php if(isset($_SESSION['user_id'])): ?>
          <a href="setting.php" title="โปรไฟล์">
              <i class="bi bi-person-fill text-warning"></i>
          </a>
      <?php else: ?>
          <a href="login.php" title="เข้าสู่ระบบ">
              <i class="bi bi-person"></i>
          </a>
      <?php endif; ?>


        <a href="favorite.php">
          <i class="bi bi-heart"></i>
          <?php if($totalFav>0){ ?>
            <span class="icon-badge">
              <?= $totalFav ?>
            </span>
          <?php } ?>
        </a>

        <a href="cart.php">
          <i class="bi bi-bag"></i>
          <?php if($totalCart>0){ ?>
            <span class="badge bg-warning text-dark icon-badge">
              <?= $totalCart ?>
            </span>
          <?php } ?>
        </a>

        <!-- Logout -->
        <a href="logout.php" title="ออกจากระบบ">
          <i class="bi bi-box-arrow-right"></i>
        </a>

      </div>

    </div>

  </div>
</header>


