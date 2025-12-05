<?php
require_once __DIR__ . '/../../config/auth.php';

startSession();

$sessionId = session_id();

$_SESSION = [];

session_destroy();

session_id($sessionId);
session_start();

$_SESSION['flash']['success'] = 'Logout berhasil! Sampai jumpa lagi.';

if (isApiRequest()) {
    respondJson(200, true, 'Logout berhasil');
}

header('Location: /auth/login.php');
exit;