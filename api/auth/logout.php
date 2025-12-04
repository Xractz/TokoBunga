<?php
require_once __DIR__ . '/../../config/auth.php';

startSession();

// Clear session
session_unset();
session_destroy();

// Clear session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

if (isApiRequest()) {
    respondJson(200, true, 'Logout berhasil');
} else {
    header('Location: /auth/login.php');
    exit;
}
