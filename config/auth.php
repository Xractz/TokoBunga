<?php
require_once __DIR__ . '/../api/helpers/flash.php';

if (!function_exists('startSession')) {
    function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        startSession();
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin() {
        startSession();
        return isLoggedIn() && $_SESSION['role'] === 'admin';
    }
}

if (!function_exists('isCustomer')) {
    function isCustomer() {
        startSession();
        return isLoggedIn() && $_SESSION['role'] === 'customer';
    }
}

if (!function_exists('getUserId')) {
    function getUserId() {
        startSession();
        return $_SESSION['user_id'] ?? null;
    }
}

if (!function_exists('getRole')) {
    function getRole() {
        startSession();
        return $_SESSION['role'] ?? null;
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin($redirectUrl = '/auth/login.php', $message = 'Silakan login terlebih dahulu untuk mengakses halaman ini.') {
        if (!isLoggedIn()) {
            if (isApiRequest()) {
                respondJson(401, false, 'Unauthorized. Please login first.');
            } else {
                flash('error', $message);
                header("Location: $redirectUrl");
                exit;
            }
        }
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin($redirectUrl = '/auth/login.php', $message = 'Akses ditolak. Halaman ini hanya untuk admin.') {
        requireLogin($redirectUrl);
        
        if (!isAdmin()) {
            if (isApiRequest()) {
                respondJson(403, false, 'Access denied. Admin only.');
            } else {
                flash('error', $message);
                header("Location: $redirectUrl");
                exit;
            }
        }
    }
}

if (!function_exists('requireCustomer')) {
    function requireCustomer($redirectUrl = '/auth/login.php', $message = 'Akses ditolak. Halaman ini hanya untuk customer.') {
        requireLogin($redirectUrl);
        
        if (!isCustomer()) {
            if (isApiRequest()) {
                respondJson(403, false, 'Access denied. Customer only.');
            } else {
                flash('error', $message);
                header("Location: $redirectUrl");
                exit;
            }
        }
    }
}

if (!function_exists('isApiRequest')) {
    function isApiRequest() {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (strpos($accept, 'application/json') !== false) {
            return true;
        }
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        
        return false;
    }
}

if (!function_exists('respondJson')) {
    function respondJson($statusCode, $success, $message, $data = null) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
        exit;
    }
}

if (!function_exists('guestOnly')) {
    function guestOnly($redirectUrl = '/index.html') {
        if (isLoggedIn()) {
            $role = getRole();
            if ($role === 'admin') {
                header('Location: /admin/admin.php');
            } else {
                header("Location: $redirectUrl");
            }
            exit;
        }
    }
}
