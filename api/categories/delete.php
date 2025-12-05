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
  respondJson(400, false, 'ID kategori tidak valid.');
}

$stmt = mysqli_prepare($conn, "SELECT id FROM product_categories WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$category = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$category) {
  respondJson(404, false, 'Kategori tidak ditemukan.');
}

$stmt2 = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM products WHERE category_id = ?");
mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);
$used = mysqli_stmt_get_result($stmt2)->fetch_assoc();
mysqli_stmt_close($stmt2);

if ($used['total'] > 0) {
  respondJson(409, false, 'Kategori tidak bisa dihapus karena sedang digunakan oleh produk.');
}

$stmt3 = mysqli_prepare($conn, "DELETE FROM product_categories WHERE id = ?");
mysqli_stmt_bind_param($stmt3, "i", $id);

$success = mysqli_stmt_execute($stmt3);
mysqli_stmt_close($stmt3);

if (!$success) {
  respondJson(500, false, 'Gagal menghapus kategori: ' . mysqli_error($conn));
}

respondJson(200, true, 'Kategori berhasil dihapus.', ['category_id' => $id]);