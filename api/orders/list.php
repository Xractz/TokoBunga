<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respondJson(405, false, "Method not allowed. Gunakan GET.");
}

$user_id = intval($_SESSION['user_id'] ?? 0);

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
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    respondJson(500, false, "Query gagal dipersiapkan.");
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);

if (empty($orders)) {
    respondJson(200, true, "Belum ada pesanan untuk akun ini.", [
        "count" => 0,
        "data"  => []
    ]);
}

respondJson(200, true, "Daftar pesanan berhasil diambil.", [
    "count" => count($orders),
    "data"  => $orders
]);
