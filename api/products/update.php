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
  respondJson(400, false, 'ID produk tidak valid.');
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
  respondJson(404, false, 'Produk tidak ditemukan.');
}

$category_id = intval($_POST['category_id'] ?? $old['category_id']);
$name        = trim($_POST['name'] ?? $old['name']);
$description = trim($_POST['description'] ?? $old['description']);
$price       = $_POST['price'] ?? floatval($old['price']);
$stock       = $_POST['stock'] ?? intval($old['stock']);
$image       = trim($_POST['image'] ?? $old['image']);
$is_active   = intval($_POST['is_active'] ?? $old['is_active']);

if ($name === "") {
  respondJson(400, false, 'Nama produk tidak boleh kosong.');
}

if ($category_id <= 0) {
  respondJson(400, false, 'Kategori produk tidak valid.');
}

if ($price < 0) {
  respondJson(400, false, 'Harga produk tidak boleh negatif.');
}

if ($stock < 0) {
  respondJson(400, false, 'Stok produk tidak boleh negatif.');
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
  respondJson(409, false, 'Slug produk sudah digunakan produk lain.');
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
  respondJson(500, false, 'Gagal memperbarui produk: ' . mysqli_error($conn));
}

respondJson(200, true, 'Produk berhasil diperbarui.', [
  'product_id' => $id,
  'slug' => $slug
]);
