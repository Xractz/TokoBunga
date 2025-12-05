<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed. Gunakan GET."
    ]);
    exit;
}

$user_id = intval($_SESSION['user_id'] ?? 0);

if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Anda harus login untuk melihat pesanan."
    ]);
    exit;
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
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Query gagal dipersiapkan."
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);


if (empty($orders)) {
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "count"   => 0,
        "message" => "Belum ada pesanan untuk akun ini.",
        "data"    => []
    ]);
    exit;
}

http_response_code(200);
echo json_encode([
    "success" => true,
    "count"   => count($orders),
    "data"    => $orders
]);
