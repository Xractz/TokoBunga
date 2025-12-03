<?php
header("Content-Type: application/json");

require_once "../../config/db.php";
global $conn;

$token = $_GET['token'] ?? '';

if ($token === '') {
  echo json_encode(["success" => false, "message" => "Token tidak valid"]);
  exit;
}

$stmt = mysqli_prepare(
  $conn,
  "SELECT id FROM users WHERE activation_token = ? AND is_active = 0"
);
mysqli_stmt_bind_param($stmt, "s", $token);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

if (!$user) {
  echo json_encode(["success" => false, "message" => "Token tidak valid atau akun sudah aktif"]);
  exit;
}

// Aktivasi akun
$stmt = mysqli_prepare(
  $conn,
  "UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $user['id']);
mysqli_stmt_execute($stmt);

echo json_encode(["success" => true, "message" => "Akun berhasil diaktifkan"]);