<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../../config/auth.php";
requireCustomer();

global $conn;

$orderCode = $_GET['order_code'] ?? '';

if (empty($orderCode)) {
    respondJson(400, false, "Order code required");
}

$stmt = mysqli_prepare($conn, "SELECT payment_status, status FROM orders WHERE order_code = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "si", $orderCode, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$order) {
    respondJson(404, false, "Order not found");
}

respondJson(200, true, "Status retrieved", [
    'payment_status' => $order['payment_status'],
    'status' => $order['status']
]);
