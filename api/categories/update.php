<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_admin.php";
require_once __DIR__ . "/../helpers/slug.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, 'Method not allowed. Use POST.');
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
  respondJson(400, false, 'ID kategori tidak valid.');
}

$stmt = mysqli_prepare(
  $conn,
  "SELECT name, description, is_active FROM product_categories WHERE id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$old = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$old) {
  respondJson(404, false, 'Kategori tidak ditemukan.');
}

$name        = trim($_POST['name'] ?? $old['name']);
$description = trim($_POST['description'] ?? $old['description']);
$is_active   = intval($_POST['is_active'] ?? $old['is_active']);

if ($name === "") {
  respondJson(400, false, 'Nama kategori tidak boleh kosong.');
}

$slug = createSlug($name);

$stmt2 = mysqli_prepare(
  $conn,
  "SELECT 1 FROM product_categories WHERE slug = ? AND id != ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt2, "si", $slug, $id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_store_result($stmt2);

if (mysqli_stmt_num_rows($stmt2) > 0) {
  mysqli_stmt_close($stmt2);
  respondJson(409, false, 'Slug kategori sudah digunakan kategori lain.');
}
mysqli_stmt_close($stmt2);

$stmt3 = mysqli_prepare(
  $conn,
  "UPDATE product_categories SET name = ?, slug = ?, description = ?, is_active = ? WHERE id = ?"
);
mysqli_stmt_bind_param($stmt3, "sssii", $name, $slug, $description, $is_active, $id);
$success = mysqli_stmt_execute($stmt3);
mysqli_stmt_close($stmt3);

if (!$success) {
  respondJson(500, false, 'Gagal memperbarui kategori: ' . mysqli_error($conn));
}

respondJson(200, true, 'Kategori berhasil diperbarui.', [
  'category_id' => $id,
  'slug' => $slug
]);
