<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, "Method not allowed. Gunakan GET atau POST.");
}

$user_id = intval($_SESSION['user_id'] ?? 0);

$order_id = 0;

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $order_id = intval($_GET['id'] ?? ($_GET['order_id'] ?? 0));
} else {
    $order_id = intval($_POST['id'] ?? ($_POST['order_id'] ?? 0));
}

if ($order_id <= 0) {
    respondJson(400, false, "ID pesanan tidak valid.");
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
        WHERE id = ? AND user_id = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    respondJson(500, false, "Query gagal dipersiapkan.");
}

mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$order  = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$order) {
    respondJson(404, false, "Pesanan tidak ditemukan.");
}

respondJson(200, true, "Detail pesanan berhasil diambil.", $order);
