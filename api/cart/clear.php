<?php
header("Content-Type: application/json");
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');
mysqli_report(MYSQLI_REPORT_OFF);

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, "Method not allowed. Gunakan POST.");
}

$user_id = $_SESSION['user_id']; // Middleware ensures this exists

if (!$conn) {
    respondJson(500, false, "Database connection failed");
}

$sql = "DELETE FROM cart_items WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    $error = mysqli_error($conn);
    error_log("Prepare Failed: " . $error);
    respondJson(500, false, "Database error: Failed to prepare delete statement.");
}

mysqli_stmt_bind_param($stmt, "i", $user_id);

if (mysqli_stmt_execute($stmt)) {
    respondJson(200, true, "Keranjang berhasil dikosongkan.");
} else {
    respondJson(500, false, "Gagal mengosongkan keranjang: " . mysqli_error($conn));
}
