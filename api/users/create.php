<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/db.php";

global $conn;

function createUser($conn, $data)
{
    $name     = trim($data['name']     ?? '');
    $email    = trim($data['email']    ?? '');
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');
    $phone    = trim($data['phone']    ?? '');
    $address  = trim($data['address']  ?? '');

    $role = "customer";

    $is_active = 0;

    $activation_token = bin2hex(random_bytes(16));

    $profile_photo = null;

    if ($email === '' || $username === '' || $password === '') {
        return [
            "success" => false,
            "status"  => 400,
            "message" => "Email, username, dan password wajib diisi."
        ];
    }

    $sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (mysqli_fetch_assoc($res)) {
        return [
            "success" => false,
            "status"  => 409,
            "message" => "Email sudah digunakan."
        ];
    }
    mysqli_stmt_close($stmt);

    $sql = "SELECT id FROM users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (mysqli_fetch_assoc($res)) {
        return [
            "success" => false,
            "status"  => 409,
            "message" => "Username sudah digunakan."
        ];
    }
    mysqli_stmt_close($stmt);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (
                name,
                email,
                username,
                password,
                phone,
                address,
                profile_photo,
                role,
                is_active,
                activation_token
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return [
            "success" => false,
            "status"  => 500,
            "message" => "Gagal mempersiapkan query: " . mysqli_error($conn)
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
        $activation_token
    );

    $exec = mysqli_stmt_execute($stmt);

    if (!$exec) {
        $err = mysqli_error($conn);
        mysqli_stmt_close($stmt);
        return [
            "success" => false,
            "status"  => 500,
            "message" => "Gagal membuat user: " . $err
        ];
    }

    $new_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    return [
        "success" => true,
        "status"  => 201,
        "message" => "Registrasi berhasil. Silakan aktivasi email Anda.",
        "user" => [
            "id"        => $new_id,
            "name"      => $name,
            "email"     => $email,
            "username"  => $username,
            "role"      => $role,
            "is_active" => $is_active,
            "activation_token" => $activation_token
        ]
    ];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, false, "Gunakan POST.");
}

$result = createUser($conn, $_POST);

respondJson(
    $result["status"],
    $result["success"],
    $result["message"],
    $result["user"] ?? null
);
