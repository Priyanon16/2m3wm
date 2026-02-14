<?php
// =======================
// ข้อมูลจำลอง (Mock Data)
// =======================
$orders = [
  1001 => [
    'order_id' => 1001,
    'order_date' => '2026-01-30',
    'total_price' => 2500,
    'payment_method' => 'โอนเงิน',
    'status' => 'รอแพ็ค',
    'customer_name' => 'สมชาย ใจดี',
    'phone' => '0812345678',
    'shipping_address' => "99/9 ต.ในเมือง\nอ.เมือง จ.ขอนแก่น 40000",
    'items' => [
      ['product_name'=>'รองเท้าวิ่ง Nike','qty'=>2,'price'=>2000],
      ['product_name'=>'รองเท้าแตะ Adidas','qty'=>1,'price'=>500],
    ]
  ],
  1002 => [
    'order_id' => 1002,
    'order_date' => '2026-01-31',
    'total_price' => 1200,
    'payment_method' => 'เก็บเงินปลายทาง',
    'status' => 'จัดส่งแล้ว',
    'customer_name' => 'สมหญิง รวยมาก',
    'phone' => '0899999999',
    'shipping_address' => "123 หมู่ 5\nอ.เมือง จ.อุดรธานี 41000",
    'items' => [
      ['product_name'=>'รองเท้าผ้าใบ Converse','qty'=>1,'price'=>1200],
    ]
  ]
];

// รับ id จาก URL


$order_id = $_GET['id'] ?? null;

if (!$order_id || !isset($orders[$order_id])) {
  die("ไม่พบออเดอร์");
}

$order = $orders[$order_id];
$items = $order['items'];
?>
