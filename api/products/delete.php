<?php
header("Content-Type: application/json");

require_once "../../config/db.php";
require_once "../middleware/is_admin.php";
global $conn;

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
  echo json_encode([
    "success" => false,
    "message" => "ID produk tidak valid."
  ]);
  exit;
}

$stmt = mysqli_prepare($conn, "SELECT id FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
  echo json_encode([
    "success" => false,
    "message" => "Produk tidak ditemukan."
  ]);
  exit;
}

$stmt3 = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt3, "i", $id);

$success = mysqli_stmt_execute($stmt3);

if (!$success) {
  echo json_encode([
    "success" => false,
    "message" => "Gagal menghapus produk."
  ]);
  exit;
}

echo json_encode([
  "success" => true,
  "message" => "Produk berhasil dihapus.",
  "product_id" => $id
]);
exit;
