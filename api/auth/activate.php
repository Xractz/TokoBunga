<?php
require_once "../../config/db.php";
global $conn;

$token = $_GET['token'] ?? '';
$status = 'invalid';

if ($token !== '') {

  $stmt = mysqli_prepare(
    $conn,
    "SELECT id, is_active FROM users WHERE activation_token = ?"
  );
  mysqli_stmt_bind_param($stmt, "s", $token);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $user = mysqli_fetch_assoc($result);

  if ($user) {
    if ($user['is_active'] == 1) {
      $status = 'already_active';
    } else {
      $stmt = mysqli_prepare(
        $conn,
        "UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?"
      );
      mysqli_stmt_bind_param($stmt, "i", $user['id']);
      mysqli_stmt_execute($stmt);

      $status = 'success';
    }
  }
}

session_start();
$_SESSION['activation_status'] = $status;

header("Location: ../../aktivasi.php");
exit;
