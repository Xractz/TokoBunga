<?php
header("Content-Type: application/json");

require_once "../../config/db.php";
require_once "../middleware/is_admin.php";
global $conn;

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
  echo json_encode([
    "success" => false,
    "message" => "ID kategori tidak valid."
  ]);
  exit;
}

$stmt = mysqli_prepare($conn, "SELECT id FROM product_categories WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$category = mysqli_fetch_assoc($result);

if (!$category) {
  echo json_encode([
    "success" => false,
    "message" => "Kategori tidak ditemukan."
  ]);
  exit;
}

$stmt2 = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM products WHERE category_id = ?");
mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);
$used = mysqli_stmt_get_result($stmt2)->fetch_assoc();

if ($used['total'] > 0) {
  echo json_encode([
    "success" => false,
    "message" => "Kategori tidak bisa dihapus karena sedang digunakan oleh produk."
  ]);
  exit;
}

$stmt3 = mysqli_prepare($conn, "DELETE FROM product_categories WHERE id = ?");
mysqli_stmt_bind_param($stmt3, "i", $id);

$success = mysqli_stmt_execute($stmt3);

if (!$success) {
  echo json_encode([
    "success" => false,
    "message" => "Gagal menghapus kategori."
  ]);
  exit;
}

echo json_encode([
  "success" => true,
  "message" => "Kategori berhasil dihapus.",
  "category_id" => $id
]);
exit;