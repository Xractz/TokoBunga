<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

$user_id  = intval($_POST['user_id'] ?? 0);
$id       = intval($_POST['id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

if ($user_id <= 0) {
  http_response_code(401); 
  echo json_encode([
    "success" => false,
    "message" => "Anda harus login untuk mengubah keranjang."
  ]);
  exit;
}

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
  "SELECT id 
   FROM cart_items 
   WHERE id = ? AND user_id = ?
   LIMIT 1"
);

if (!$stmt) {
  http_response_code(500);
  echo json_encode([
    "success" => false,
    "message" => "Gagal mempersiapkan query."
  ]);
  exit;
}

mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$result   = mysqli_stmt_get_result($stmt);
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

if ($quantity <= 0) {
  http_response_code(400); 
  echo json_encode([
    "success" => false,
    "message" => "Jumlah produk minimal 1."
  ]);
  exit;
}

$stmt2 = mysqli_prepare(
  $conn,
  "UPDATE cart_items 
   SET quantity = ?, updated_at = NOW()
   WHERE id = ? AND user_id = ?"
);

if (!$stmt2) {
  http_response_code(500);
  echo json_encode([
    "success" => false,
    "message" => "Gagal mempersiapkan query update."
  ]);
  exit;
}

mysqli_stmt_bind_param($stmt2, "iii", $quantity, $id, $user_id);
$success = mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

if (!$success) {
  http_response_code(500);
  echo json_encode([
    "success" => false,
    "message" => "Gagal mengubah jumlah item di keranjang."
  ]);
  exit;
}

http_response_code(200);
echo json_encode([
  "success"      => true,
  "message"      => "Item keranjang berhasil diperbarui.",
  "cart_item_id" => $id,
  "quantity"     => $quantity
]);
exit;
