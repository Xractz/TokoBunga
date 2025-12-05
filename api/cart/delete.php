<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../config/auth.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, 'Method not allowed. Use POST.');
}

$id = intval($_POST['id'] ?? 0);
$user_id = intval($_SESSION['user_id'] ?? 0);

if ($id <= 0) {
  respondJson(400, false, 'ID item keranjang tidak valid.');
}

$stmt = mysqli_prepare(
  $conn,
  "SELECT id FROM cart_items WHERE id = ? AND user_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cartItem = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$cartItem) {
  respondJson(404, false, 'Item keranjang tidak ditemukan.');
}

$stmt2 = mysqli_prepare($conn, "DELETE FROM cart_items WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt2, "ii", $id, $user_id);
$success = mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

if (!$success) {
  respondJson(500, false, 'Gagal menghapus item dari keranjang: ' . mysqli_error($conn));
}

respondJson(200, true, 'Item keranjang berhasil dihapus.', ['cart_item_id' => $id]);
