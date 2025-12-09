<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_customer.php";

global $conn;

function updateUserProfile(mysqli $conn, int $user_id, array $data): array
{
    $name     = trim($data['name']     ?? '');
    $email    = trim($data['email']    ?? '');
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? ''); 
    $phone    = trim($data['phone']    ?? '');
    $address  = trim($data['address']  ?? '');
    $profile_photo = $data['profile_photo'] ?? null;
    


    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ? LIMIT 1");
    if (!$stmt) {
        return [
            "success" => false,
            "status"  => 500,
            "message" => "Gagal mempersiapkan query cek user: " . mysqli_error($conn)
        ];
    }

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $res  = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$user) {
        return [
            "success" => false,
            "status"  => 404,
            "message" => "User tidak ditemukan."
        ];
    }

    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newFilename = uniqid('profile_', true) . '.' . $ext;
            $uploadDir = __DIR__ . '/../../public/assets/images/profiles/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadDir . $newFilename)) {
                if (!empty($user['profile_photo']) && $user['profile_photo'] !== 'default.png') {
                    $oldPath = $uploadDir . $user['profile_photo'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $profile_photo = $newFilename;
            }
        }
    }

    $name     = $name     !== '' ? $name     : $user['name'];
    $email    = $email    !== '' ? $email    : $user['email'];
    $username = $username !== '' ? $username : $user['username'];
    $phone    = $phone    !== '' ? $phone    : $user['phone'];
    $address  = $address  !== '' ? $address  : $user['address'];
    $profile_photo = $profile_photo ?? $user['profile_photo'];

    $role      = $user['role'];
    $is_active = $user['is_active'];

    if ($email !== $user['email']) {
        $sql = "SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            return [
                "success" => false,
                "status"  => 500,
                "message" => "Gagal mempersiapkan query cek email: " . mysqli_error($conn)
            ];
        }

        mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if (mysqli_fetch_assoc($res)) {
            mysqli_stmt_close($stmt);
            return [
                "success" => false,
                "status"  => 409,
                "message" => "Email sudah digunakan user lain."
            ];
        }
        mysqli_stmt_close($stmt);
    }

    if ($username !== $user['username']) {
        $sql = "SELECT id FROM users WHERE username = ? AND id <> ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            return [
                "success" => false,
                "status"  => 500,
                "message" => "Gagal mempersiapkan query cek username: " . mysqli_error($conn)
            ];
        }

        mysqli_stmt_bind_param($stmt, "si", $username, $user_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if (mysqli_fetch_assoc($res)) {
            mysqli_stmt_close($stmt);
            return [
                "success" => false,
                "status"  => 409,
                "message" => "Username sudah digunakan user lain."
            ];
        }
        mysqli_stmt_close($stmt);
    }

    $hashed_password = $user['password'];
    if ($password !== '') {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }

    $sql = "UPDATE users SET
                name = ?,
                email = ?,
                username = ?,
                password = ?,
                phone = ?,
                address = ?,
                profile_photo = ?,
                role = ?,
                is_active = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return [
            "success" => false,
            "status"  => 500,
            "message" => "Gagal mempersiapkan query update user: " . mysqli_error($conn)
        ];
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssssii",
        $name,
        $email,
        $username,
        $hashed_password,
        $phone,
        $address,
        $profile_photo,
        $role,
        $is_active,
        $user_id
    );

    $exec = mysqli_stmt_execute($stmt);
    if (!$exec) {
        $err = mysqli_error($conn);
        mysqli_stmt_close($stmt);
        return [
            "success" => false,
            "status"  => 500,
            "message" => "Gagal mengupdate user: " . $err
        ];
    }

    mysqli_stmt_close($stmt);

    return [
        "success" => true,
        "status"  => 200,
        "message" => "Profil berhasil diperbarui.",
        "user" => [
            "id"        => $user_id,
            "name"      => $name,
            "email"     => $email,
            "username"  => $username,
            "phone"     => $phone,
            "address"   => $address,
            "profile_photo" => $profile_photo,
            "role"      => $role,
            "is_active" => $is_active
        ]
    ];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, "Method not allowed. Gunakan POST.");
}

$user_id = intval($_SESSION['user_id'] ?? 0);

$result = updateUserProfile($conn, $user_id, $_POST);

respondJson(
    $result["status"],
    $result["success"],
    $result["message"],
    $result["user"] ?? null
);
