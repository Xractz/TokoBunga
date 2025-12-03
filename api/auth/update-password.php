<?php
header("Content-Type: application/json");

require_once "../../config/db.php";

global $conn;

$token = trim($_POST['token'] ?? '');
$newPassword = trim($_POST['newPassword'] ?? '');

if ($token === "" || $newPassword === "") {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Semua field wajib diisi"]);
  exit;
}

// Security: Validate token format (must be 64 character hexadecimal)
if (!preg_match('/^[a-f0-9]{64}$/i', $token)) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Format token tidak valid", "redirect" => true]);
  exit;
}

$stmt = mysqli_prepare(
  $conn,
  "SELECT id, reset_token, reset_token_expires FROM users WHERE reset_token = ?"
);
mysqli_stmt_bind_param($stmt, "s", $token);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
  http_response_code(404);
  echo json_encode(["success" => false, "message" => "Token tidak ditemukan"]);
  exit;
}

// Check if token is expired
if (strtotime($user['reset_token_expires']) <= time()) {
  http_response_code(404);
  echo json_encode(["success" => false, "message" => "Token sudah kadaluarsa"]);
  exit;
}

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = mysqli_prepare(
  $conn,
  "UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $user['id']);
$success = mysqli_stmt_execute($stmt);

if (!$success) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Gagal mereset password"]);
  exit;
}

http_response_code(200);
echo json_encode([
  "success" => true,
  "message" => "Password berhasil direset. Silakan login dengan password baru."
]);
