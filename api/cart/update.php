<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../config/auth.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, 'Method not allowed. Use POST.');
}

$user_id  = intval($_SESSION['user_id'] ?? 0);
$id       = intval($_POST['id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

if ($user_id <= 0) {
  respondJson(401, false, 'Anda harus login untuk mengubah keranjang.');
}

if ($id <= 0) {
  respondJson(400, false, 'ID item keranjang tidak valid.');
}

$stmt = mysqli_prepare(
  $conn,
  "SELECT id 
   FROM cart_items 
   WHERE id = ? AND user_id = ?
   LIMIT 1"
);

if (!$stmt) {
  respondJson(500, false, 'Gagal mempersiapkan query: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$result   = mysqli_stmt_get_result($stmt);
$cartItem = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$cartItem) {
  respondJson(404, false, 'Item keranjang tidak ditemukan.');
}

if ($quantity <= 0) {
  respondJson(400, false, 'Jumlah produk minimal 1.');
}

$stmt2 = mysqli_prepare(
  $conn,
  "UPDATE cart_items 
   SET quantity = ?, updated_at = NOW()
   WHERE id = ? AND user_id = ?"
);

if (!$stmt2) {
  respondJson(500, false, 'Gagal mempersiapkan query update: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt2, "iii", $quantity, $id, $user_id);
$success = mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

if (!$success) {
  respondJson(500, false, 'Gagal mengubah jumlah item di keranjang: ' . mysqli_error($conn));
}

respondJson(200, true, 'Item keranjang berhasil diperbarui.', [
  'cart_item_id' => $id,
  'quantity' => $quantity
]);
