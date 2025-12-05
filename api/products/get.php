<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

global $conn;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
  $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  $product = mysqli_fetch_assoc($result);

  mysqli_stmt_close($stmt);
  
  if (!$product) {
    respondJson(404, false, 'Produk tidak ditemukan.');
  }

  respondJson(200, true, 'Produk ditemukan.', $product);
}

$sql = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

respondJson(200, true, 'Daftar produk berhasil diambil.', $products);
