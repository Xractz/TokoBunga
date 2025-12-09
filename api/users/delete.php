<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_admin.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, "Method not allowed. Gunakan POST.");
}

$user_id = intval($_POST['id'] ?? ($_POST['user_id'] ?? 0));

if ($user_id <= 0) {
    respondJson(400, false, "ID user tidak valid.");
}

if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) === $user_id) {
    respondJson(400, false, "Anda tidak dapat menghapus akun Anda sendiri.");
}

$sql_check = "SELECT 
                id,
                name,
                email,
                username,
                role,
                is_active
              FROM users
              WHERE id = ?
              LIMIT 1";

$stmt_check = mysqli_prepare($conn, $sql_check);

if (!$stmt_check) {
    respondJson(500, false, "Gagal mempersiapkan query cek user.");
}

mysqli_stmt_bind_param($stmt_check, "i", $user_id);
mysqli_stmt_execute($stmt_check);
$res_check = mysqli_stmt_get_result($stmt_check);
$user = mysqli_fetch_assoc($res_check);
mysqli_stmt_close($stmt_check);

if (!$user) {
    respondJson(404, false, "User tidak ditemukan.");
}

$sql_delete = "DELETE FROM users WHERE id = ?";
$stmt_delete = mysqli_prepare($conn, $sql_delete);

if (!$stmt_delete) {
    respondJson(500, false, "Gagal mempersiapkan query delete user.");
}

mysqli_stmt_bind_param($stmt_delete, "i", $user_id);
$exec = mysqli_stmt_execute($stmt_delete);

if (!$exec) {
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt_delete);
    respondJson(500, false, "Gagal menghapus user: " . $err);
}

mysqli_stmt_close($stmt_delete);

respondJson(200, true, "User berhasil dihapus.", [
    "id"        => $user['id'],
    "name"      => $user['name'],
    "email"     => $user['email'],
    "username"  => $user['username'],
    "role"      => $user['role'],
    "is_active" => $user['is_active']
]);
