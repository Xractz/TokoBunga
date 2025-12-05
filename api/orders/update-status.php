<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_admin.php";

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

$order_id = intval($_POST['id'] ?? ($_POST['order_id'] ?? 0));

if ($order_id <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "ID pesanan tidak valid."
    ]);
    exit;
}

$status = trim($_POST['status'] ?? '');

if ($status === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Status baru wajib diisi."
    ]);
    exit;
}

$allowed_status = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

if (!in_array($status, $allowed_status, true)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Status tidak valid. Status yang diperbolehkan: " . implode(", ", $allowed_status)
    ]);
    exit;
}

$sql_check = "SELECT id, status FROM orders WHERE id = ? LIMIT 1";
$stmt_check = mysqli_prepare($conn, $sql_check);

if (!$stmt_check) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Gagal mempersiapkan query cek pesanan."
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt_check, "i", $order_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$order = mysqli_fetch_assoc($result_check);
mysqli_stmt_close($stmt_check);

if (!$order) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Pesanan tidak ditemukan."
    ]);
    exit;
}


$sql_update = "UPDATE orders SET status = ? WHERE id = ?";
$stmt_update = mysqli_prepare($conn, $sql_update);

if (!$stmt_update) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Gagal mempersiapkan query update status."
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt_update, "si", $status, $order_id);
$exec = mysqli_stmt_execute($stmt_update);

if (!$exec) {
    $error = mysqli_error($conn);
    mysqli_stmt_close($stmt_update);

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Gagal mengubah status pesanan: " . $error
    ]);
    exit;
}

mysqli_stmt_close($stmt_update);

http_response_code(200);
echo json_encode([
    "success" => true,
    "message" => "Status pesanan berhasil diubah.",
    "data" => [
        "order_id"      => $order_id,
        "old_status"    => $order['status'],
        "new_status"    => $status
    ]
]);
