<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_admin.php";
require_once __DIR__ . "/../helpers/slug.php";

global $conn;

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, 'Method not allowed. Use POST.');
}

$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($name === "") {
  respondJson(400, false, 'Nama kategori wajib diisi.');
}

$slug = createSlug($name);

$stmt = mysqli_prepare($conn, "SELECT id FROM product_categories WHERE slug = ?");
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
  mysqli_stmt_close($stmt);
  respondJson(409, false, 'Slug kategori sudah ada. Gunakan nama lain.');
}

$stmt = mysqli_prepare(
  $conn,
  "INSERT INTO product_categories (name, slug, description, is_active)
     VALUES (?, ?, ?, 1)"
);
mysqli_stmt_bind_param($stmt, "sss", $name, $slug, $description);

$success = mysqli_stmt_execute($stmt);

if (!$success) {
  mysqli_stmt_close($stmt);
  respondJson(500, false, 'Gagal menambahkan kategori: ' . mysqli_error($conn));
}

$categoryId = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

respondJson(201, true, 'Kategori berhasil ditambahkan.', [
  'category_id' => $categoryId,
  'slug' => $slug,
  'name' => $name
]);
