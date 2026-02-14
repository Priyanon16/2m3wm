<div class="dropdown mt-auto">
    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
        data-bs-toggle="dropdown">
        <i class="bi bi-person-circle me-2 fs-4"></i>
        <strong class="user-text"><?= $_SESSION['aname'] ?? 'Admin' ?></strong>
    </a>

    <ul class="dropdown-menu dropdown-menu-dark shadow">
        <li><a class="dropdown-item" href="admin_profile.php"><i class="bi bi-person me-2"></i>โปรไฟล์</a></li>
        <li><a class="dropdown-item" href="admin_setting.php"><i class="bi bi-gear me-2"></i>การตั้งค่า</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</a></li>
    </ul>
</div>