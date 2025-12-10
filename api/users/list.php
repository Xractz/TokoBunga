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

    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 10;
    $offset = ($page - 1) * $limit;

    $countQuery = "SELECT COUNT(*) as total FROM users WHERE role != 'admin'";
    $countStmt = mysqli_prepare($conn, $countQuery);
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
    $totalCount = mysqli_fetch_assoc($countResult)['total'];
    mysqli_stmt_close($countStmt);

    $totalPages = ceil($totalCount / $limit);

    $sql = "SELECT 
                u.id,
                u.name,
                u.email,
                u.username,
                u.phone,
                u.address,
                u.profile_photo,
                u.role,
                u.is_active,
                u.created_at,
                COUNT(o.id) as total_transactions
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id
            WHERE u.role != 'admin'
            GROUP BY u.id
            ORDER BY u.created_at DESC
            LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        respondJson(500, false, "Gagal mengambil data users: " . mysqli_error($conn));
    }
    
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    respondJson(200, true, "Daftar user berhasil diambil.", [
        "users" => $users,
        "pagination" => [
            "current_page" => $page,
            "total_pages" => $totalPages,
            "total_count" => $totalCount,
            "per_page" => $limit
        ]
    ]);
}
