<?php
header("Content-Type: application/json");
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');
mysqli_report(MYSQLI_REPORT_OFF); // Disable exceptions, use return values

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, "Method not allowed. Gunakan POST.");
}

$user_id = intval($_SESSION['user_id'] ?? 0);

$payment_method   = trim($_POST['payment_method']   ?? '');
$recipient_name   = trim($_POST['recipient_name']   ?? '');
$recipient_phone  = trim($_POST['recipient_phone']  ?? '');
$shipping_address = trim($_POST['shipping_address'] ?? '');

$delivery_date = $_POST['delivery_date'] ?? null; 
$delivery_time = $_POST['delivery_time'] ?? null;  
$card_message  = $_POST['card_message']  ?? null;

$subtotal      = floatval($_POST['subtotal']      ?? 0);
$shipping_cost = floatval($_POST['shipping_cost'] ?? 0);
$grand_total   = isset($_POST['grand_total'])
    ? floatval($_POST['grand_total'])
    : $subtotal + $shipping_cost;

$latitude  = ($_POST['latitude']  ?? '') !== '' ? floatval($_POST['latitude'])  : null;
$longitude = ($_POST['longitude'] ?? '') !== '' ? floatval($_POST['longitude']) : null;

$status         = "pending";
$payment_status = "unpaid";


if ($recipient_name === '' || $recipient_phone === '' || $shipping_address === '') {
    respondJson(400, false, "Nama penerima, nomor HP, dan alamat wajib diisi.");
}

if ($subtotal <= 0) {
    respondJson(400, false, "Subtotal pesanan tidak valid.");
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
    respondJson(500, false, "Gagal mempersiapkan query: " . mysqli_error($conn));
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
    respondJson(500, false, "Gagal membuat pesanan: " . $error);
}

$order_id = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

// --- TRANSFER CART TO ORDER_ITEMS ---

// 1. Ambil item dari cart
$sqlCart = "SELECT c.product_id, c.quantity, p.price 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?";
$stmtCart = mysqli_prepare($conn, $sqlCart);
if ($stmtCart) {
    mysqli_stmt_bind_param($stmtCart, "i", $user_id);
    mysqli_stmt_execute($stmtCart);
    $resultCart = mysqli_stmt_get_result($stmtCart);

    $cartItems = [];
    while ($row = mysqli_fetch_assoc($resultCart)) {
        $cartItems[] = $row;
    }
    mysqli_stmt_close($stmtCart);

    if (!empty($cartItems)) {
        // 2. Insert ke order_items
        $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmtItem = mysqli_prepare($conn, $sqlItem);

        if ($stmtItem) {
            foreach ($cartItems as $item) {
                $pId   = $item['product_id'];
                $qty   = $item['quantity'];
                $price = $item['price'];
                $sub   = $qty * $price;

                mysqli_stmt_bind_param($stmtItem, "iiidd", $order_id, $pId, $qty, $price, $sub);
                mysqli_stmt_execute($stmtItem);
            }
            mysqli_stmt_close($stmtItem);
        }
    }
}


respondJson(201, true, "Pesanan berhasil dibuat.", [
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
]);
