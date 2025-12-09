<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/auth.php";
require_once __DIR__ . "/../../config/db.php";

requireCustomer();

global $conn;
$userId = $_SESSION['user_id'];

// Pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 10;
$offset = ($page - 1) * $limit;

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
$countStmt = mysqli_prepare($conn, $countQuery);
if (!$countStmt) {
    respondJson(500, false, "Database error");
}
mysqli_stmt_bind_param($countStmt, "i", $userId);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$totalCount = mysqli_fetch_assoc($countResult)['total'];
mysqli_stmt_close($countStmt);

$totalPages = ceil($totalCount / $limit);

// Get paginated orders
$query = "SELECT id, order_code, created_at, status, payment_status, grand_total FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    respondJson(500, false, "Database error");
}

mysqli_stmt_bind_param($stmt, "iii", $userId, $limit, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}
mysqli_stmt_close($stmt);

respondJson(200, true, "Orders retrieved", [
    'items' => $orders,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'total_count' => $totalCount,
        'per_page' => $limit
    ]
]);
