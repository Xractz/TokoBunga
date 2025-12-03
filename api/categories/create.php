<?php
header("Content-Type: application/json");

require_once "../../config/db.php";
require_once "../middleware/is_admin.php";
require_once "../helpers/slug.php";

global $conn;

$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($name === "") {
  echo json_encode([
    "success" => false,
    "message" => "Nama kategori wajib diisi."
  ]);
  exit;
}

$slug = createSlug($name);

$stmt = mysqli_prepare($conn, "SELECT id FROM product_categories WHERE slug = ?");
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
  echo json_encode([
    "success" => false,
    "message" => "Slug kategori sudah ada. Gunakan nama lain."
  ]);
  exit;
}

$stmt = mysqli_prepare(
  $conn,
  "INSERT INTO product_categories (name, slug, description, is_active)
     VALUES (?, ?, ?, 1)"
);
mysqli_stmt_bind_param($stmt, "sss", $name, $slug, $description);

$success = mysqli_stmt_execute($stmt);

if (!$success) {
  echo json_encode([
    "success" => false,
    "message" => "Gagal menambahkan kategori."
  ]);
  exit;
}

echo json_encode([
  "success" => true,
  "message" => "Kategori berhasil ditambahkan.",
  "category_id" => mysqli_insert_id($conn),
  "slug" => $slug
]);
