<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

global $conn;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$slug = $_GET['slug'] ?? '';

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

if (!empty($slug)) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE slug = ?");
    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
  
    $product = mysqli_fetch_assoc($result);
  
    mysqli_stmt_close($stmt);
    
    if (!$product) {
      respondJson(404, false, 'Produk tidak ditemukan.');
    }
  
    respondJson(200, true, 'Produk ditemukan.', $product);
  }

$search = $_GET['search'] ?? '';
$minPrice = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? intval($_GET['max_price']) : 0;
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

$sql = "SELECT * FROM products WHERE is_active = 1";
$types = "";
$params = [];

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $searchTerm = "%" . $search . "%";
    $types .= "ss";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($category) && $category !== 'all') {
    $catIds = explode(',', $category);
    $catIds = array_map('intval', $catIds);
    $catIds = array_filter($catIds);

    if (!empty($catIds)) {
        $placeholders = implode(',', array_fill(0, count($catIds), '?'));
        
        $sql .= " AND category_id IN ($placeholders)";
        
        $types .= str_repeat('i', count($catIds));
        
        $params = array_merge($params, $catIds);
    }
}
if ($minPrice > 0) {
    $sql .= " AND price >= ?";
    $types .= "i";
    $params[] = $minPrice;
}
if ($maxPrice > 0) {
    $sql .= " AND price <= ?";
    $types .= "i";
    $params[] = $maxPrice;
}

// 4. Sorting
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY price DESC";
        break;
    case 'featured':
        $sql .= " ORDER BY id DESC"; 
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY id DESC";
        break;
}

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);

respondJson(200, true, 'Daftar produk berhasil diambil.', $products);
