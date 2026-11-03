<?php
/**
 * Role-based Authentication & Authorization Helper
 */
class AuthHelper {
    public static function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check() {
        self::initSession();
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    public static function user() {
        self::initSession();
        return $_SESSION['user'] ?? null;
    }

    public static function id() {
        $user = self::user();
        return $user['id'] ?? null;
    }

    public static function isAdmin() {
        $user = self::user();
        return $user && isset($user['role']) && $user['role'] === 'admin';
    }

    public static function requireLogin($redirectUrl = 'index.php?controller=user&action=login') {
        if (!self::check()) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục!';
            header("Location: $redirectUrl");
            exit;
        }
    }

    public static function requireAdmin($redirectUrl = 'index.php') {
        if (!self::isAdmin()) {
            http_response_code(403);
            die("Truy cập bị từ chối: Bạn không có quyền quản trị để thực hiện thao tác này.");
        }
    }

    /**
     * Xác thực API Token (Bearer Token)
     */
    public static function getBearerToken() {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }
        return $_GET['api_token'] ?? null;
    }
}

