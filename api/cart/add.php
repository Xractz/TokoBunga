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

mysqli_stmt_close($stmtProduct);

if (!$product) {
  respondJson(404, false, 'Produk tidak ditemukan atau tidak aktif.');
}

if ($product_id <= 0) {
  respondJson(400, false, 'Produk tidak valid.');
}

if ($quantity <= 0) {
  respondJson(400, false, 'Jumlah produk minimal 1.');
}

$stmtCheck = mysqli_prepare(
  $conn,
  "SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmtCheck, "ii", $user_id, $product_id);
mysqli_stmt_execute($stmtCheck);
$resultCheck = mysqli_stmt_get_result($stmtCheck);
$cartItem = mysqli_fetch_assoc($resultCheck);

if ($cartItem) {
  $new_quantity = $cartItem['quantity'] + $quantity;
  
  $stmtUpdate = mysqli_prepare(
    $conn,
    "UPDATE cart_items 
     SET quantity = ?, updated_at = NOW()
     WHERE id = ?"
  );
  mysqli_stmt_bind_param($stmtUpdate, "ii", $new_quantity, $cartItem['id']);
  $success = mysqli_stmt_execute($stmtUpdate);

  mysqli_stmt_close($stmtUpdate);
  
  if (!$success) {
    respondJson(500, false, 'Gagal memperbarui keranjang: ' . mysqli_error($conn));
  }

  respondJson(200, true, 'Keranjang berhasil diperbarui.', [
    'cart_item_id' => $cartItem['id'],
    'quantity' => $quantity
  ]);
}

$stmtInsert = mysqli_prepare(
  $conn,
  "INSERT INTO cart_items (user_id, product_id, quantity)
   VALUES (?, ?, ?)"
);
mysqli_stmt_bind_param($stmtInsert, "iii", $user_id, $product_id, $quantity);
$success = mysqli_stmt_execute($stmtInsert);

mysqli_stmt_close($stmtInsert);

if (!$success) {
  respondJson(500, false, 'Gagal menambahkan ke keranjang: ' . mysqli_error($conn));
}

$cart_item_id = mysqli_insert_id($conn);

respondJson(201, true, 'Produk berhasil ditambahkan ke keranjang.', [
  'cart_item_id' => $cart_item_id,
  'quantity' => $quantity
]);
