<?php
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if(!isset($_SESSION['favorite'])) $_SESSION['favorite'] = [];

$totalCart = array_sum($_SESSION['cart']);
$totalFav  = count($_SESSION['favorite']);
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
  gap:18px;
}

.header-icons a{
  position:relative;
  color:#fff;
  font-size:22px;
  display:flex;
  align-items:center;
  justify-content:center;
  transition:.3s;
}

.header-icons a:hover{
  color:#ff7a00;
  transform:translateY(-2px);
}

/* ===== BADGE ===== */
.icon-badge{
  position:absolute;
  top:-5px;
  right:-6px;
  min-width:16px;
  height:16px;
  font-size:10px;
  font-weight:600;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:50%;
  background:#e53935;
  color:#fff;
  box-shadow:0 2px 6px rgba(0,0,0,.4);
  padding:0 4px;
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
        <a class="nav-link" href="#">ติดต่อเรา</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" 
          data-bs-toggle="dropdown" aria-expanded="false">
            การตั้งค่า
        </a>

        <ul class="dropdown-menu shadow border-0 rounded-3">
            <li>
                <a class="dropdown-item" href="profile.php">
                    <i class="bi bi-person me-2 text-warning"></i>โปรไฟล์
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="order_status.php">
                    <i class="bi bi-truck me-2 text-primary"></i>เช็คสถานะออเดอร์
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="order_history.php">
                    <i class="bi bi-clock-history me-2 text-success"></i>ประวัติการสั่งซื้อ
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
      <div class="d-flex gap-3 header-icons">

        <a href="login.php">
          <i class="bi bi-person"></i>
        </a>

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
