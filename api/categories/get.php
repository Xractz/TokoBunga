<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

global $conn;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {

  $stmt = mysqli_prepare($conn, "SELECT * FROM product_categories WHERE id = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  $category = mysqli_fetch_assoc($result);

  if (!$category) {
    respondJson(404, false, 'Kategori tidak ditemukan.');
  }

  respondJson(200, true, 'Kategori ditemukan.', $category);
}

$status = $_GET['status'] ?? 1;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = max(1, $page);
$limit = max(1, $limit);
$offset = ($page - 1) * $limit;

// filter
$whereClauses = [];
$params = [];
$types = "";

if ($status !== 'all') {
    $whereClauses[] = "is_active = ?";
    $params[] = intval($status);
    $types .= "i";
}

$whereBuf = "";
if (!empty($whereClauses)) {
    $whereBuf = "WHERE " . implode(" AND ", $whereClauses);
}

// get Total Items
$sqlCount = "SELECT COUNT(*) as total FROM product_categories $whereBuf";
$stmtCount = mysqli_prepare($conn, $sqlCount);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmtCount, $types, ...$params);
}
mysqli_stmt_execute($stmtCount);
$resCount = mysqli_stmt_get_result($stmtCount);
$rowCount = mysqli_fetch_assoc($resCount);
$totalItems = $rowCount['total'];
mysqli_stmt_close($stmtCount);

// get Data
$sql = "SELECT * FROM product_categories $whereBuf ORDER BY id DESC LIMIT ? OFFSET ?";
$types .= "ii";
$params[] = $limit;
$params[] = $offset;

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$totalPages = ceil($totalItems / $limit);

respondJson(200, true, 'Daftar kategori berhasil diambil.', [
    'categories' => $categories,
    'pagination' => [
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $page,
        'limit' => $limit
    ]
]);