<?php
require_once __DIR__ . "/../../../config/db.php";
require_once __DIR__ . "/../../../config/auth.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, "Method Not Allowed");
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    respondJson(400, false, "Invalid JSON payload");
}

$orderCode = $data['order_id'] ?? '';
$status    = $data['status'] ?? '';
$amount    = $data['amount'] ?? 0;

if (empty($orderCode) || empty($status)) {
    respondJson(400, false, "Missing order_id or status");
}

if ($status !== 'completed') {
    respondJson(200, true, "Status not completed (ignored)");
}

$sql = "SELECT id, grand_total, payment_status FROM orders WHERE order_code = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $orderCode);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$order) {
    respondJson(404, false, "Order not found");
}

if ($order['payment_status'] === 'paid') {
    respondJson(200, true, "Order already paid");
}
if (abs(floatval($order['grand_total']) - floatval($amount)) > 0.01) {
    error_log("FAILED: Payment amount mismatch for $orderCode. Expected: {$order['grand_total']}, Received: $amount");
    respondJson(400, false, "Payment amount mismatch");
}

$updateSql = "UPDATE orders SET payment_status = 'paid', status = 'processing', updated_at = NOW() WHERE id = ?";
$updateStmt = mysqli_prepare($conn, $updateSql);
mysqli_stmt_bind_param($updateStmt, "i", $order['id']);

if (mysqli_stmt_execute($updateStmt)) {
    error_log("SUCCESS: Order $orderCode marked as PAID via Webhook.");
    respondJson(200, true, "Payment confirmed");
} else {
    error_log("FAILED: Could not update order $orderCode: " . mysqli_error($conn));
    respondJson(500, false, "Database update failed");
}
mysqli_stmt_close($updateStmt);
