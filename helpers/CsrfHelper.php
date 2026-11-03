<?php
/**
 * CSRF Protection Helper
 * Bảo vệ chống tấn công Cross-Site Request Forgery cho toàn bộ Form và API Request
 */
class CsrfHelper {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function getToken() {
        self::init();
        return $_SESSION['csrf_token'];
    }

    public static function csrfField() {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function verifyToken($token = null) {
        self::init();
        if ($token === null) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        }
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function requireValidToken() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!self::verifyToken()) {
                http_response_code(403);
                die("Lỗi bảo mật (CSRF Verification Failed): Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn. Vui lòng tải lại trang.");
            }
        }
    }
}

