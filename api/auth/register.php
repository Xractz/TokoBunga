<?php
header("Content-Type: application/json");

require_once "../../config/db.php";
require_once "../../config/mailer.php";

global $conn;

$name     = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($name === "" || $username === "" || $email === "" || $phone === "" || $password === "") {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Semua field wajib diisi"]);
  exit;
}

if (strlen($password) < 6) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Password minimal 6 karakter"]);
  exit;
}

// Check if email already exists
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
  http_response_code(409);
  echo json_encode(["success" => false, "message" => "Email sudah terdaftar"]);
  exit;
}

// Check if username already exists
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
  http_response_code(409);
  echo json_encode(["success" => false, "message" => "Username sudah digunakan"]);
  exit;
}

// Check if phone already exists
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE phone = ?");
mysqli_stmt_bind_param($stmt, "s", $phone);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
  http_response_code(409);
  echo json_encode(["success" => false, "message" => "Nomor telepon sudah terdaftar"]);
  exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$token = bin2hex(random_bytes(32));

$stmt = mysqli_prepare(
  $conn,
  "INSERT INTO users (name, username, email, phone, password, activation_token, is_active) 
     VALUES (?, ?, ?, ?, ?, ?, 0)"
);
mysqli_stmt_bind_param($stmt, "ssssss", $name, $username, $email, $phone, $hashedPassword, $token);
$success = mysqli_stmt_execute($stmt);

if (!$success) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Gagal mendaftar"]);
  exit;
}

$send = sendActivationEmail($email, $token);

http_response_code(201);
echo json_encode([
  "success" => true,
  "message" => "Registrasi berhasil! Periksa email Anda untuk aktivasi akun."
]);
