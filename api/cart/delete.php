<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

$id = intval($_POST['id'] ?? 0);
$user_id = intval($_SESSION['user_id'] ?? 0);

if ($id <= 0) {
  http_response_code(400); 
  echo json_encode([
    "success" => false,
    "message" => "ID item keranjang tidak valid."
  ]);
  exit;
}

$stmt = mysqli_prepare(
  $conn,
  "SELECT id FROM cart_items WHERE id = ? AND user_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cartItem = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$cartItem) {
  http_response_code(404); 
  echo json_encode([
    "success" => false,
    "message" => "Item keranjang tidak ditemukan."
  ]);
  exit;
}

$stmt2 = mysqli_prepare($conn, "DELETE FROM cart_items WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt2, "ii", $id, $user_id);
$success = mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

if (!$success) {
  http_response_code(500); 
  echo json_encode([
    "success" => false,
    "message" => "Gagal menghapus item dari keranjang."
  ]);
  exit;
}

http_response_code(200); 
echo json_encode([
  "success" => true,
  "message" => "Item keranjang berhasil dihapus.",
  "cart_item_id" => $id
]);
exit;
