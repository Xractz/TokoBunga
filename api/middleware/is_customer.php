<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  echo json_encode([
    "success" => false,
    "message" => "Unauthorized: Please login."
  ]);
  exit;
}

if ($_SESSION['role'] !== 'customer') {
  echo json_encode([
    "success" => false,
    "message" => "Access denied: Customer only."
  ]);
  exit;
}
