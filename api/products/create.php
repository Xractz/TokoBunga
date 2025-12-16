<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_admin.php";
require_once __DIR__ . "/../helpers/slug.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, 'Method not allowed. Use POST.');
}

$name        = trim($_POST['name'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);
$price       = floatval($_POST['price'] ?? 0);
$stock       = intval($_POST['stock'] ?? 0);
$description = trim($_POST['description'] ?? '');
$image = '';

if ($name === '' || $category_id <= 0 || $price <= 0) {
  respondJson(400, false, 'Nama produk, kategori, dan harga wajib diisi.');
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

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName    = $_FILES['image']['name'];
    $fileSize    = $_FILES['image']['size'];
    $fileType    = $_FILES['image']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');
    $allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');

    // Validate
    if (in_array($fileExtension, $allowedfileExtensions) && in_array($fileType, $allowedMimeTypes)) {
        $uploadFileDir = __DIR__ . '/../../public/assets/images/products/';
        
        $newFileName = $slug . '.' . $fileExtension;
        $dest_path = $uploadFileDir . $newFileName;

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $image = $newFileName;
        } else {
            respondJson(500, false, "Gagal mengupload gambar ke server.");
        }
    } else {
        respondJson(400, false, "Format gambar tidak valid. Gunakan JPG, PNG, atau WebP.");
    }
}

$sql = "INSERT INTO products (category_id, name, slug, description, price, stock, image)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
  respondJson(500, false, 'Gagal mempersiapkan query: ' . mysqli_error($conn));
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
  mysqli_stmt_close($stmt);
  respondJson(500, false, 'Gagal menambahkan produk: ' . mysqli_error($conn));
}

$product_id = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

respondJson(201, true, 'Produk berhasil ditambahkan.', [
  'product_id' => $product_id,
  'slug' => $slug,
  'name' => $name
]);
