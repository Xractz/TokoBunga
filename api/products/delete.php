<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_admin.php";
global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, 'Method not allowed. Use POST.');
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
  respondJson(400, false, 'ID produk tidak valid.');
}

$stmt = mysqli_prepare($conn, "SELECT id FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$product) {
  respondJson(404, false, 'Produk tidak ditemukan.');
}

$stmt3 = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt3, "i", $id);

$success = mysqli_stmt_execute($stmt3);
mysqli_stmt_close($stmt3);

if (!$success) {
  respondJson(500, false, 'Gagal menghapus produk: ' . mysqli_error($conn));
}

respondJson(200, true, 'Produk berhasil dihapus.', ['product_id' => $id]);
