<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_admin.php";

global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respondJson(405, false, "Method not allowed. Gunakan GET.");
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
