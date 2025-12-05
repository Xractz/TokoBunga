<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed. Gunakan POST."
    ]);
    exit;
}

$user_id = intval($_SESSION['user_id'] ?? 0);

if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Anda harus login untuk membuat pesanan."
    ]);
    exit;
}

$payment_method  = trim($_POST['payment_method'] ?? '');
$recipient_name  = trim($_POST['recipient_name'] ?? '');
$recipient_phone = trim($_POST['recipient_phone'] ?? '');
$shipping_address = trim($_POST['shipping_address'] ?? '');

$delivery_date   = $_POST['delivery_date'] ?? null; 
$delivery_time   = $_POST['delivery_time'] ?? null;  
$card_message    = $_POST['card_message'] ?? null;

$subtotal       = floatval($_POST['subtotal'] ?? 0);
$shipping_cost  = floatval($_POST['shipping_cost'] ?? 0);
$grand_total    = isset($_POST['grand_total'])
    ? floatval($_POST['grand_total'])
    : $subtotal + $shipping_cost;

$latitude  = ($_POST['latitude']  ?? '') !== '' ? floatval($_POST['latitude'])  : null;
$longitude = ($_POST['longitude'] ?? '') !== '' ? floatval($_POST['longitude']) : null;

$status         = "pending";
$payment_status = "unpaid";

if ($recipient_name === '' || $recipient_phone === '' || $shipping_address === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Nama penerima, nomor HP, dan alamat wajib diisi."
    ]);
    exit;
}

if ($subtotal <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Subtotal pesanan tidak valid."
    ]);
    exit;
}

$order_code = "ORD-" . date("Ymd-His") . "-" . strtoupper(substr(md5(uniqid((string)$user_id, true)), 0, 4));

$sql = "INSERT INTO orders (
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
            longitude
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Gagal mempersiapkan query: " . mysqli_error($conn)
    ]);
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "issssssssssddddd",
    $user_id,
    $order_code,
    $status,
    $payment_status,
    $payment_method,
    $recipient_name,
    $recipient_phone,
    $shipping_address,
    $delivery_date,
    $delivery_time,
    $card_message,
    $subtotal,
    $shipping_cost,
    $grand_total,
    $latitude,
    $longitude
);

$exec = mysqli_stmt_execute($stmt);

if (!$exec) {
    $error = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Gagal membuat pesanan: " . $error
    ]);
    exit;
}

$order_id = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

http_response_code(201);
echo json_encode([
    "success" => true,
    "message" => "Pesanan berhasil dibuat.",
    "data" => [
        "order_id"        => $order_id,
        "order_code"      => $order_code,
        "user_id"         => $user_id,
        "recipient_name"  => $recipient_name,
        "recipient_phone" => $recipient_phone,
        "shipping_address"=> $shipping_address,
        "delivery_date"   => $delivery_date,
        "delivery_time"   => $delivery_time,
        "card_message"    => $card_message,
        "subtotal"        => $subtotal,
        "shipping_cost"   => $shipping_cost,
        "grand_total"     => $grand_total,
        "latitude"        => $latitude,
        "longitude"       => $longitude
    ]
]);
