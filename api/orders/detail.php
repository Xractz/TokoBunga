<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, "Method not allowed. Gunakan GET atau POST.");
}

$user_id = intval($_SESSION['user_id'] ?? 0);

// Get order_code from request
$order_code = '';
if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $order_code = trim($_GET['order_code'] ?? ($_GET['id'] ?? ''));
} else {
    $order_code = trim($_POST['order_code'] ?? ($_POST['id'] ?? ''));
}

if (empty($order_code)) {
    respondJson(400, false, "Kode pesanan harus disediakan.");
}

// Validate order_code format: ORD-YYYYMMDD-HHMMSS-XXXX
if (!preg_match('/^ORD-\d{8}-\d{6}-[A-Z0-9]{4}$/', $order_code)) {
    respondJson(400, false, "Format kode pesanan tidak valid.");
}

$sql = "SELECT 
            id,
            user_id,
            order_code,
            status,
            payment_status,
            payment_method,
            recipient_name,
            recipient_phone,
            shipping_address,
            delivery_date,
            delivery_time,
            card_message,
            subtotal,
            shipping_cost,
            grand_total,
            latitude,
            longitude,
            created_at,
            updated_at
        FROM orders
        WHERE order_code = ? AND user_id = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    respondJson(500, false, "Query gagal dipersiapkan.");
}

mysqli_stmt_bind_param($stmt, "si", $order_code, $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$order  = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$order) {
    respondJson(404, false, "Pesanan tidak ditemukan.");
}

// Fetch Items
$sqlItems = "SELECT oi.*, p.name AS product_name, p.image 
             FROM order_items oi 
             JOIN products p ON oi.product_id = p.id 
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
