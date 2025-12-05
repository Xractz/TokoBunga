<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $conn;

$user_id = intval($_SESSION['user_id'] ?? 0);

if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Anda harus login untuk melihat detail pesanan."
    ]);
    exit;
}

$order_id = 0;

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $order_id = intval($_GET['id'] ?? ($_GET['order_id'] ?? 0));
} else { 
    $order_id = intval($_POST['id'] ?? ($_POST['order_id'] ?? 0));
}

if ($order_id <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "ID pesanan tidak valid."
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
        WHERE id = ? AND user_id = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Query gagal dipersiapkan."
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$order) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Pesanan tidak ditemukan."
    ]);
    exit;
}

http_response_code(200);
echo json_encode([
    "success" => true,
    "data" => $order
]);
