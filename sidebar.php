<div id="sidebar" class="sidebar p-3 d-flex flex-column">

    <!-- Logo -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="fs-4 fw-bold">
            <span class="brand-accent">2m</span>3wm
        </span>

        <button class="btn btn-sm text-white" id="toggleBtn">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <hr class="text-secondary">

    <!-- Menu -->
    <ul class="nav nav-pills flex-column mb-auto">

        <li>
            <a href="index_admin.php" class="nav-link">
                <i class="bi bi-speedometer2"></i>
                <span>แดชบอร์ด</span>
            </a>
        </li>

        <li>
            <a href="a_orderlist.php" class="nav-link">
                <i class="bi bi-table"></i>
                <span>ออเดอร์</span>
            </a>
        </li>

        <li>
            <a href="admin_product.php" class="nav-link">
                <i class="bi bi-box"></i>
                <span>สินค้า</span>
            </a>
        </li>

        <li>
            <a href="category_products.php" class="nav-link">
                <i class="bi bi-tags"></i>
                <span>หมวดหมู่</span>
            </a>
        </li>

        <li>
            <a href="admin_brand.php" class="nav-link">
                <i class="bi bi-award"></i>
                <span>แบรนด์</span>
            </a>
        </li>

        <li>
            <a href="customer_data.php" class="nav-link">
                <i class="bi bi-people"></i>
                <span>ลูกค้า</span>
            </a>
        </li>

    </ul>

    <hr class="text-secondary">

    <!-- User -->
    <div class="mt-auto">
        <a href="logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right"></i>
            <span>ออกจากระบบ</span>
        </a>
    </div>

</div>

<script>
document.getElementById("toggleBtn").addEventListener("click", function(){
    document.getElementById("sidebar").classList.toggle("collapsed");
});
</script>
