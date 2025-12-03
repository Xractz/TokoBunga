<?php
header("Content-Type: application/json");

require_once "../../config/db.php";
require_once "../middleware/is_admin.php";
require_once "../helpers/slug.php";

global $conn;

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
  echo json_encode(["success" => false, "message" => "ID produk tidak valid."]);
  exit;
}

$stmt = mysqli_prepare(
  $conn,
  "SELECT category_id, name, description, price, stock, image, is_active 
   FROM products 
   WHERE id = ? 
   LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$old = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$old) {
  echo json_encode(["success" => false, "message" => "Produk tidak ditemukan."]);
  exit;
}

$category_id = intval($_POST['category_id'] ?? $old['category_id']);
$name        = trim($_POST['name'] ?? $old['name']);
$description = trim($_POST['description'] ?? $old['description']);
$price       = $_POST['price'] ?? floatval($old['price']);
$stock       = $_POST['stock'] ?? intval($old['stock']);
$image       = trim($_POST['image'] ?? $old['image']);
$is_active   = intval($_POST['is_active'] ?? $old['is_active']);

if ($name === "") {
  echo json_encode(["success" => false, "message" => "Nama produk tidak boleh kosong."]);
  exit;
}

if ($category_id <= 0) {
  echo json_encode(["success" => false, "message" => "Kategori produk tidak valid."]);
  exit;
}

if ($price < 0) {
  echo json_encode(["success" => false, "message" => "Harga produk tidak boleh negatif."]);
  exit;
}

if ($stock < 0) {
  echo json_encode(["success" => false, "message" => "Stok produk tidak boleh negatif."]);
  exit;
}

$slug = createSlug($name);

$stmt2 = mysqli_prepare(
  $conn,
  "SELECT 1 FROM products WHERE slug = ? AND id != ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt2, "si", $slug, $id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_store_result($stmt2);

if (mysqli_stmt_num_rows($stmt2) > 0) {
  mysqli_stmt_close($stmt2);
  echo json_encode(["success" => false, "message" => "Slug produk sudah digunakan produk lain."]);
  exit;
}
mysqli_stmt_close($stmt2);

$stmt3 = mysqli_prepare(
  $conn,
  "UPDATE products 
   SET category_id = ?, name = ?, slug = ?, description = ?, price = ?, stock = ?, image = ?, is_active = ? 
   WHERE id = ?"
);
mysqli_stmt_bind_param(
  $stmt3,
  "isssdisii",
  $category_id,
  $name,
  $slug,
  $description,
  $price,
  $stock,
  $image,
  $is_active,
  $id
);

$success = mysqli_stmt_execute($stmt3);
mysqli_stmt_close($stmt3);

if (!$success) {
  echo json_encode(["success" => false, "message" => "Gagal memperbarui produk."]);
  exit;
}

echo json_encode([
  "success" => true,
  "message" => "Produk berhasil diperbarui.",
  "product_id" => $id,
  "slug" => $slug
]);
