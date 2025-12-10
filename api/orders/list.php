<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/auth.php";
require_once __DIR__ . "/../../config/db.php";

require_once __DIR__ . "/../middleware/is_login.php";

global $conn;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'customer';
$isAdmin = ($role === 'admin');

// Pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 10;
$offset = ($page - 1) * $limit;

// Get total count
if ($isAdmin) {
    $countQuery = "SELECT COUNT(*) as total FROM orders";
    $countStmt = mysqli_prepare($conn, $countQuery);
    mysqli_stmt_execute($countStmt);
} else {
    $countQuery = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
    $countStmt = mysqli_prepare($conn, $countQuery);
    mysqli_stmt_bind_param($countStmt, "i", $user_id);
    mysqli_stmt_execute($countStmt);
}

if (!$countStmt) {
    respondJson(500, false, "Database error");
}

$countResult = mysqli_stmt_get_result($countStmt);
$totalCount = mysqli_fetch_assoc($countResult)['total'];
mysqli_stmt_close($countStmt);

$totalPages = ceil($totalCount / $limit);

// Get paginated orders
if ($isAdmin) {
    $query = "SELECT o.id, o.order_code, o.created_at, o.status, o.payment_status, o.grand_total, o.recipient_name, u.name as customer_name 
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id 
             ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
} else {
    $query = "SELECT id, order_code, created_at, status, payment_status, grand_total FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iii", $user_id, $limit, $offset);
}

if (!$stmt) {
    respondJson(500, false, "Database error: " . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    if ($isAdmin) {
         if (empty($row['customer_name'])) {
            $row['customer_name'] = $row['recipient_name'] . ' (Guest/Recipient)';
        }
    }
    $orders[] = $row;
}
mysqli_stmt_close($stmt);

respondJson(200, true, "Orders retrieved", [
    'orders' => $orders, // Standardize on 'orders' (old list.php used 'items', but admin-list used 'orders'. 'orders' is clearer)
    'items' => $orders, // Keep 'items' for backward compatibility with customer frontend if it uses it.
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'total_count' => $totalCount,
        'per_page' => $limit
    ]
]);
