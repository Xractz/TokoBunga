<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_admin.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, "Method not allowed. Gunakan POST.");
}

$order_id = intval($_POST['id'] ?? ($_POST['order_id'] ?? 0));

if ($order_id <= 0) {
    respondJson(400, false, "ID pesanan tidak valid.");
}

$status = trim($_POST['status'] ?? '');

if ($status === '') {
    respondJson(400, false, "Status baru wajib diisi.");
}

$allowed_status = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

if (!in_array($status, $allowed_status, true)) {
    respondJson(
        400,
        false,
        "Status tidak valid. Status yang diperbolehkan: " . implode(", ", $allowed_status)
    );
}

$sql_check = "SELECT id, status FROM orders WHERE id = ? LIMIT 1";
$stmt_check = mysqli_prepare($conn, $sql_check);

if (!$stmt_check) {
    respondJson(500, false, "Gagal mempersiapkan query cek pesanan.");
}

mysqli_stmt_bind_param($stmt_check, "i", $order_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$order = mysqli_fetch_assoc($result_check);
mysqli_stmt_close($stmt_check);

if (!$order) {
    respondJson(404, false, "Pesanan tidak ditemukan.");
}

$sql_update = "UPDATE orders SET status = ? WHERE id = ?";
$stmt_update = mysqli_prepare($conn, $sql_update);

if (!$stmt_update) {
    respondJson(500, false, "Gagal mempersiapkan query update status.");
}

mysqli_stmt_bind_param($stmt_update, "si", $status, $order_id);
$exec = mysqli_stmt_execute($stmt_update);

if (!$exec) {
    $error = mysqli_error($conn);
    mysqli_stmt_close($stmt_update);
    respondJson(500, false, "Gagal mengubah status pesanan: " . $error);
}

mysqli_stmt_close($stmt_update);

respondJson(200, true, "Status pesanan berhasil diubah.", [
    "order_id"   => $order_id,
    "old_status" => $order['status'],
    "new_status" => $status
]);
