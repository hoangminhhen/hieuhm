<?php
/**
 * Security & Formatting Helper
 */
class SecurityHelper {
    /**
     * Escape HTML output để chống XSS
     */
    public static function e($str) {
        if ($str === null) return '';
        return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Định dạng tiền tệ Việt Nam (VNĐ)
     */
    public static function formatVND($amount) {
        return number_format((float)$amount, 0, ',', '.') . ' ₫';
    }

    /**
     * Alias cho formatVND
     */
    public static function formatPrice($amount) {
        return self::formatVND($amount);
    }

    /**
     * Làm sạch dữ liệu số nguyên an toàn
     */
    public static function sanitizeInt($val, $default = 0) {
        $clean = preg_replace('/[^0-9]/', '', (string)$val);
        return $clean !== '' ? (int)$clean : $default;
    }

    /**
     * Làm sạch dữ liệu đầu vào chuỗi
     */
    public static function cleanInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'cleanInput'], $data);
        }
        return trim(strip_tags((string)$data));
    }

    /**
     * Tạo mã định danh ngẫu nhiên (dùng cho mã đơn hàng, SKU...)
     */
    public static function generateCode($prefix = 'ORD') {
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    }
}

// Global shortcut function cho views
if (!function_exists('e')) {
    function e($str) {
        return SecurityHelper::e($str);
    }
}

if (!function_exists('formatVND')) {
    function formatVND($amount) {
        return SecurityHelper::formatVND($amount);
    }
}

