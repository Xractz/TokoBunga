<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';

global $conn;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {

  $stmt = mysqli_prepare($conn, "SELECT * FROM product_categories WHERE id = ?");
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

  echo json_encode([
    "success" => true,
    "data" => $category
  ]);
  exit;
}

$sql = "SELECT * FROM product_categories ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode([
  "success" => true,
  "data" => $categories
]);
exit;