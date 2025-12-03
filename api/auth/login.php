<?php
header("Content-Type: application/json");

require_once "../../config/db.php";
global $conn;

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

if (!$user) {
  echo json_encode(["success" => false, "message" => "Email tidak ditemukan"]);
  exit;
}

if ($user['is_active'] == 0) {
  echo json_encode(["success" => false, "message" => "Akun belum diaktivasi"]);
  exit;
}

if (!password_verify($password, $user['password'])) {
  echo json_encode(["success" => false, "message" => "Password salah"]);
  exit;
}

session_start();
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];

echo json_encode(["success" => true, "message" => "Login berhasil"]);