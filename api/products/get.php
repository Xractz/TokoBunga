<?php
header('Content-Type: application/json');

require_once __DIR__ . '../../../config/db.php';

global $conn;

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode([
  "success" => true,
  "data" => $products
]);
