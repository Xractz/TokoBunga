<?php
header("Content-Type: application/json");

require_once "../../config/db.php";
require_once "../../config/mailer.php";

global $conn;

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($name === "" || $email === "" || $password === "") {
  echo json_encode(["success" => false, "message" => "Semua field wajib diisi"]);
  exit;
}

// Cek apakah email sudah terdaftar
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
  echo json_encode(["success" => false, "message" => "Email sudah terdaftar"]);
  exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Generate token aktivasi
$token = bin2hex(random_bytes(32));

// Insert user ke database
$stmt = mysqli_prepare(
  $conn,
  "INSERT INTO users (username, email, password, activation_token, is_active) 
     VALUES (?, ?, ?, ?, 0)"
);
mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashedPassword, $token);
$success = mysqli_stmt_execute($stmt);

if (!$success) {
  echo json_encode(["success" => false, "message" => "Gagal mendaftar"]);
  exit;
}

$send = sendActivationEmail($email, $token);

echo json_encode([
  "success" => true,
  "message" => "Registrasi berhasil! Periksa email Anda untuk aktivasi akun."
]);