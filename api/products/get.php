<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';

global $conn;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
  $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  $product = mysqli_fetch_assoc($result);

  if (!$product) {
    echo json_encode([
      "success" => false,
      "message" => "Produk tidak ditemukan."
    ]);
    exit;
  }

  echo json_encode([
    "success" => true,
    "data" => $product
  ]);
  exit;
}

$sql = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode([
  "success" => true,
  "data" => $products
]);
exit;
