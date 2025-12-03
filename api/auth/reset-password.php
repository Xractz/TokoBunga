<?php
header("Content-Type: application/json");

require_once "../../config/db.php";
require_once "../../config/mailer.php";

global $conn;

$email = trim($_POST['email'] ?? '');

if ($email === "") {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Email wajib diisi"]);
  exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, email FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
  http_response_code(404);
  echo json_encode(["success" => false, "message" => "Email tidak ditemukan"]);
  exit;
}

$token = bin2hex(random_bytes(32));
$expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

$stmt = mysqli_prepare(
  $conn,
  "UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?"
);
mysqli_stmt_bind_param($stmt, "sss", $token, $expiresAt, $email);
$success = mysqli_stmt_execute($stmt);

if (!$success) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Gagal membuat token reset"]);
  exit;
}

// Send reset email
$send = sendResetPasswordEmail($email, $token);

if (!$send) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Gagal mengirim email"]);
  exit;
}

http_response_code(200);
echo json_encode([
  "success" => true,
  "message" => "Link reset password telah dikirim ke email Anda"
]);
