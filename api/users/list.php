<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_login.php"; 

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respondJson(405, false, "Method not allowed. Gunakan GET.");
}

$currentUserId = getUserId();
$targetId = isset($_GET['id']) ? intval($_GET['id']) : null;
$isAdmin = isAdmin();

if ($targetId) {
    if (!$isAdmin && $targetId !== $currentUserId) {
        respondJson(403, false, "Forbidden. Anda tidak boleh melihat data user lain.");
    }
    
    $sql = "SELECT 
                id,
                name,
                email,
                username,
                phone,
                address,
                profile_photo,
                role,
                is_active,
                created_at,
                updated_at
            FROM users
            WHERE id = ? LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $targetId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$user) {
        respondJson(404, false, "User tidak ditemukan.");
    }

    respondJson(200, true, "Data user ditemukan.", [$user]);
} 
else {
    if (!$isAdmin) {
        respondJson(403, false, "Forbidden. Hanya admin yang bisa melihat semua user.");
    }

    $sql = "SELECT 
                id,
                name,
                email,
                username,
                phone,
                address,
                profile_photo,
                role,
                is_active,
                activation_token,
                created_at,
                updated_at
            FROM users
            ORDER BY created_at DESC";

    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        respondJson(500, false, "Gagal mengambil data users: " . mysqli_error($conn));
    }
    
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    respondJson(200, true, "Daftar user berhasil diambil.", [
        "count" => count($users),
        "data"  => $users
    ]);
}
