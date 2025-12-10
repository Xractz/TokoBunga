<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
// Check login (Admin or Customer)
require_once __DIR__ . "/../middleware/is_login.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, "Method not allowed. Gunakan GET atau POST.");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'customer';
$isAdmin = ($role === 'admin');

// Get order_code
$order_code = '';
if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $order_code = trim($_GET['order_code'] ?? ($_GET['id'] ?? ''));
} else {
    $order_code = trim($_POST['order_code'] ?? ($_POST['id'] ?? ''));
}

if (empty($order_code)) {
    respondJson(400, false, "Kode pesanan harus disediakan.");
}

// Prepare Query
$sql = "SELECT * FROM orders WHERE order_code = ?";
$params = [$order_code];
$types = "s";

if (!$isAdmin) {
    $sql .= " AND user_id = ?";
    $params[] = $user_id;
    $types .= "i";
}

$sql .= " LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    respondJson(500, false, "Query gagal dipersiapkan.");
}

mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order  = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$order) {
    respondJson(404, false, "Pesanan tidak ditemukan.");
}

// Fetch Items with LEFT JOIN
$sqlItems = "SELECT oi.*, p.name AS product_name, p.image 
             FROM order_items oi 
             LEFT JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?";
$stmtItems = mysqli_prepare($conn, $sqlItems);
mysqli_stmt_bind_param($stmtItems, "i", $order['id']);
mysqli_stmt_execute($stmtItems);
$resItems = mysqli_stmt_get_result($stmtItems);

$items = [];
while ($row = mysqli_fetch_assoc($resItems)) {
    $items[] = $row;
}
mysqli_stmt_close($stmtItems);

$order['items'] = $items;

respondJson(200, true, "Detail pesanan berhasil diambil.", $order);
