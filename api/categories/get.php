<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

global $conn;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {

  $stmt = mysqli_prepare($conn, "SELECT * FROM product_categories WHERE id = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  $category = mysqli_fetch_assoc($result);

  if (!$category) {
    respondJson(404, false, 'Kategori tidak ditemukan.');
  }

  respondJson(200, true, 'Kategori ditemukan.', $category);
}

$sql = "SELECT * FROM product_categories ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

respondJson(200, true, 'Daftar kategori berhasil diambil.', $categories);