<?php
session_start();

header('Content-Type: application/json');

session_unset();
session_destroy();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Logout berhasil'
]);
