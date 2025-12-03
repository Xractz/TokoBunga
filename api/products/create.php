<?php
header("Content-Type: application/json");

require_once "../../config/db.php";
require_once "../middleware/is_admin.php";
require_once "../helpers/slug.php";

global $conn;

$name        = trim($_POST['name'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);
$price       = floatval($_POST['price'] ?? 0);
$stock       = intval($_POST['stock'] ?? 0);
$description = trim($_POST['description'] ?? '');
$image   = trim($_POST['image'] ?? ''); 

if ($name === '' || $category_id <= 0 || $price <= 0) {
  echo json_encode([
    "success" => false,
    "message" => "Nama produk, kategori, dan harga wajib diisi."
  ]);
  exit;
}

$slug = createSlug($name);

$sqlCheck = "SELECT id FROM products WHERE slug = ?";
$stmtCheck = mysqli_prepare($conn, $sqlCheck);
mysqli_stmt_bind_param($stmtCheck, "s", $slug);
mysqli_stmt_execute($stmtCheck);
mysqli_stmt_store_result($stmtCheck);

if (mysqli_stmt_num_rows($stmtCheck) > 0) {
  $slug = $slug . '-' . time();
}
mysqli_stmt_close($stmtCheck);

$sql = "INSERT INTO products (category_id, name, slug, description, price, stock, image)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
  echo json_encode([
    "success" => false,
    "message" => "Gagal mempersiapkan query: " . mysqli_error($conn)
  ]);
  exit;
}

mysqli_stmt_bind_param(
  $stmt,
  "isssdis", 
  $category_id,
  $name,
  $slug,
  $description,
  $price,
  $stock,
  $image
);

$success = mysqli_stmt_execute($stmt);

if (!$success) {
  echo json_encode([
    "success" => false,
    "message" => "Gagal menambahkan produk: " . mysqli_error($conn)
  ]);
  exit;
}

$product_id = mysqli_insert_id($conn);

echo json_encode([
  "success" => true,
  "message" => "Produk berhasil ditambahkan.",
  "product_id" => $product_id,
  "slug" => $slug
]);
