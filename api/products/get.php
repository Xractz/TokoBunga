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

// Pagination Params
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = max(1, $page);
$limit = max(1, $limit);
$offset = ($page - 1) * $limit;

// Base Conditions
$whereClauses = ["p.is_active = 1"];
$params = [];
$types = "";

if (!empty($search)) {
    $whereClauses[] = "(p.name LIKE ? OR p.description LIKE ?)";
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
        $whereClauses[] = "p.category_id IN ($placeholders)";
        $types .= str_repeat('i', count($catIds));
        $params = array_merge($params, $catIds);
    }
}
if ($minPrice > 0) {
    $whereClauses[] = "p.price >= ?";
    $types .= "i";
    $params[] = $minPrice;
}
if ($maxPrice > 0) {
    $whereClauses[] = "p.price <= ?";
    $types .= "i";
    $params[] = $maxPrice;
}

$whereBuf = implode(" AND ", $whereClauses);

// 1. Count Total
$sqlCount = "SELECT COUNT(*) as total FROM products p WHERE $whereBuf";
$stmtCount = mysqli_prepare($conn, $sqlCount);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmtCount, $types, ...$params);
}
mysqli_stmt_execute($stmtCount);
$resCount = mysqli_stmt_get_result($stmtCount);
$rowCount = mysqli_fetch_assoc($resCount);
$totalItems = $rowCount['total'];
mysqli_stmt_close($stmtCount);

// 2. Select Data
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN product_categories c ON p.category_id = c.id 
        WHERE $whereBuf";

// Sorting
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'featured':
        $sql .= " ORDER BY p.id DESC"; 
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY p.id DESC";
        break;
}

// Pagination
$sql .= " LIMIT ? OFFSET ?";
$types .= "ii";
$params[] = $limit;
$params[] = $offset;

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$totalPages = ceil($totalItems / $limit);

respondJson(200, true, 'Daftar produk berhasil diambil.', [
    'products' => $products,
    'pagination' => [
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $page,
        'limit' => $limit
    ]
]);
