<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

$user_id = intval($_POST['user_id']);

if ($user_id <= 0) {
  http_response_code(401);
  echo json_encode([
    "success" => false,
    "message" => "Anda harus login untuk melihat keranjang."
  ]);
  exit;
}

$stmt = mysqli_prepare(
  $conn,
  "SELECT id, user_id, product_id, quantity, created_at, updated_at
   FROM cart_items
   WHERE user_id = ?
   ORDER BY created_at DESC"
);

if (!$stmt) {
  http_response_code(500);
  echo json_encode([
    "success" => false,
    "message" => "Gagal mempersiapkan query keranjang."
  ]);
  exit;
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

if (empty($items)) {
  http_response_code(404);
  echo json_encode([
    "success" => false,
    "message" => "Keranjang Anda masih kosong.",
    "data"    => []
  ]);
  exit;
}

http_response_code(200);
echo json_encode([
  "success" => true,
  "user_id" => $user_id,
  "data"    => $items
]);
exit;
