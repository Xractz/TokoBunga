<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  echo json_encode([
    "success" => false,
    "message" => "Unauthorized: Please login."
  ]);
  exit;
}

if ($_SESSION['role'] !== 'admin') {
  echo json_encode([
    "success" => false,
    "message" => "Access denied: Admin only."
  ]);
  exit;
}
