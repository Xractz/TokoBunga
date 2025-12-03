<?php
header("Content-Type: application/json");

require_once "../../config/db.php";
require_once "../middleware/is_admin.php";
require_once "../helpers/slug.php";

global $conn;

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
  echo json_encode(["success" => false, "message" => "ID kategori tidak valid."]);
  exit;
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
  echo json_encode(["success" => false, "message" => "Kategori tidak ditemukan."]);
  exit;
}

$name        = trim($_POST['name'] ?? $old['name']);
$description = trim($_POST['description'] ?? $old['description']);
$is_active   = intval($_POST['is_active'] ?? $old['is_active']);

if ($name === "") {
  echo json_encode(["success" => false, "message" => "Nama kategori tidak boleh kosong."]);
  exit;
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
  echo json_encode(["success" => false, "message" => "Slug kategori sudah digunakan kategori lain."]);
  exit;
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
  echo json_encode(["success" => false, "message" => "Gagal memperbarui kategori."]);
  exit;
}

echo json_encode([
  "success" => true,
  "message" => "Kategori berhasil diperbarui.",
  "category_id" => $id,
  "slug" => $slug
]);
