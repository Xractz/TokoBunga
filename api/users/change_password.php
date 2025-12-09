<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_login.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, "Method not allowed. Gunakan POST.");
}

$user_id = getUserId();
$old_password = $_POST['old_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validation
if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
    respondJson(400, false, "Semua field wajib diisi.");
}

if ($new_password !== $confirm_password) {
    respondJson(400, false, "Konfirmasi password tidak cocok.");
}

if (strlen($new_password) < 6) {
    respondJson(400, false, "Password baru minimal 6 karakter.");
}

// Check Old Password
$stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$user) {
    respondJson(404, false, "User tidak ditemukan.");
}

if (!password_verify($old_password, $user['password'])) {
    respondJson(401, false, "Password lama salah.");
}

// Update Password
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);
$update_stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
mysqli_stmt_bind_param($update_stmt, "si", $new_hash, $user_id);

if (mysqli_stmt_execute($update_stmt)) {
    mysqli_stmt_close($update_stmt);
    respondJson(200, true, "Password berhasil diubah.");
} else {
    $err = mysqli_error($conn);
    mysqli_stmt_close($update_stmt);
    respondJson(500, false, "Gagal mengubah password: " . $err);
}
