<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <title>Orders</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
<div class="d-flex">

  <!-- Sidebar -->
  <?php include __DIR__ . '/sidebar.php'; ?>

 <main class="flex-fill p-4">

  <!-- หัวข้อ -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">จัดการออเดอร์</h4>
    <span class="text-muted">Order Management</span>
  </div>

  <!-- Card Filter -->
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <div class="row g-3">

        <div class="col-md-3">
          <label class="form-label">สถานะ</label>
          <select class="form-select">
            <option>ทั้งหมด</option>
            <option>รอแพ็ค</option>
            <option>รอจัดส่ง</option>
            <option>จัดส่งแล้ว</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">วันที่สร้างออเดอร์</label>
          <input type="date" class="form-control">
        </div>

        <div class="col-md-4">
          <label class="form-label">ค้นหา</label>
          <input type="text" class="form-control" placeholder="Order ID / ชื่อลูกค้า">
        </div>

        <div class="col-md-2 d-flex align-items-end">
          <button class="btn btn-primary w-100">
            <i class="bi bi-search"></i> ค้นหา
          </button>
        </div>

      </div>
    </div>
  </div>

  <!-- ตารางออเดอร์ -->
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Order ID</th>
            <th>วันที่</th>
            <th>ลูกค้า</th>
            <th class="text-center">จำนวน</th>
            <th>ยอดรวม</th>
            <th>ชำระเงิน</th>
            <th>สถานะ</th>
            <th class="text-center">จัดการ</th>
          </tr>
        </thead>
        <tbody>

          <tr>
            <td>#1001</td>
            <td>2026-01-30</td>
            <td>สมชาย ใจดี</td>
            <td class="text-center">3</td>
            <td>2,500 บาท</td>
            <td>โอนเงิน</td>
            <td><span class="badge bg-warning">รอแพ็ค</span></td>
            <td class="text-center">
              <a href="orderdetail.php?id=1001" class="btn btn-sm btn-outline-primary">
                ดูรายละเอียด
              </a>
            </td>
          </tr>

          <tr>
            <td>#1002</td>
            <td>2026-01-31</td>
            <td>สมหญิง รวยมาก</td>
            <td class="text-center">1</td>
            <td>1,200 บาท</td>
            <td>เก็บเงินปลายทาง</td>
            <td><span class="badge bg-success">จัดส่งแล้ว</span></td>
            <td class="text-center">
              <a href="orderdetail.php?id=1002" class="btn btn-sm btn-outline-primary">
                ดูรายละเอียด
              </a>
            </td>
          </tr>

        </tbody>
      </table>
    </div>
  </div>

</main>


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
