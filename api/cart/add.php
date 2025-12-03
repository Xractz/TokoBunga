<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

$user_id    = intval($_SESSION['user_id']);
$product_id = intval($_POST['product_id'] ?? 0);
$quantity   = intval($_POST['quantity'] ?? 1);

$stmtProduct = mysqli_prepare($conn, "SELECT id FROM products WHERE id = ? AND is_active = 1");
mysqli_stmt_bind_param($stmtProduct, "i", $product_id);
mysqli_stmt_execute($stmtProduct);
$resultProduct = mysqli_stmt_get_result($stmtProduct);
$product = mysqli_fetch_assoc($resultProduct);

if (!$product) {
  http_response_code(404);
  echo json_encode([
    "success" => false,
    "message" => "Produk tidak ditemukan atau tidak aktif."
  ]);
  exit;
}

if ($product_id <= 0) {
  http_response_code(400);
  echo json_encode([
    "success" => false,
    "message" => "Produk tidak valid."
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

$stmtCheck = mysqli_prepare(
  $conn,
  "SELECT id FROM cart_items WHERE user_id = ? AND product_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmtCheck, "ii", $user_id, $product_id);
mysqli_stmt_execute($stmtCheck);
$resultCheck = mysqli_stmt_get_result($stmtCheck);
$cartItem = mysqli_fetch_assoc($resultCheck);

if ($cartItem) {
  $stmtUpdate = mysqli_prepare(
    $conn,
    "UPDATE cart_items 
     SET quantity = ?, updated_at = NOW()
     WHERE id = ?"
  );
  mysqli_stmt_bind_param($stmtUpdate, "ii", $quantity, $cartItem['id']);
  $success = mysqli_stmt_execute($stmtUpdate);

  if (!$success) {
    http_response_code(500);
    echo json_encode([
      "success" => false,
      "message" => "Gagal memperbarui keranjang."
    ]);
    exit;
  }

  http_response_code(200);
  echo json_encode([
    "success"      => true,
    "message"      => "Keranjang berhasil diperbarui.",
    "cart_item_id" => $cartItem['id'],
    "quantity"     => $quantity
  ]);
  exit;
}

$stmtInsert = mysqli_prepare(
  $conn,
  "INSERT INTO cart_items (user_id, product_id, quantity)
   VALUES (?, ?, ?)"
);
mysqli_stmt_bind_param($stmtInsert, "iii", $user_id, $product_id, $quantity);
$success = mysqli_stmt_execute($stmtInsert);

if (!$success) {
  http_response_code(500);
  echo json_encode([
    "success" => false,
    "message" => "Gagal menambahkan ke keranjang."
  ]);
  exit;
}

$cart_item_id = mysqli_insert_id($conn);

http_response_code(201);
echo json_encode([
  "success"      => true,
  "message"      => "Produk berhasil ditambahkan ke keranjang.",
  "cart_item_id" => $cart_item_id,
  "quantity"     => $quantity
]);
exit;
