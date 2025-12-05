<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../config/auth.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

$user_id = intval($_SESSION['user_id'] ?? 0);

if ($user_id <= 0) {
  respondJson(401, false, 'Anda harus login untuk melihat keranjang.');
}

$stmt = mysqli_prepare(
  $conn,
  "SELECT id, user_id, product_id, quantity, created_at, updated_at
   FROM cart_items
   WHERE user_id = ?
   ORDER BY created_at DESC"
);

if (!$stmt) {
  respondJson(500, false, 'Gagal mempersiapkan query keranjang: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

if (empty($items)) {
  respondJson(200, true, 'Keranjang Anda masih kosong.', []);
}

respondJson(200, true, 'Daftar keranjang berhasil diambil.', $items);
