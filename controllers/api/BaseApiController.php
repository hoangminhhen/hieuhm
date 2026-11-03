<?php
require_once __DIR__ . '/../../models/UserModel.php';

class BaseApiController
{
    protected $currentUser = null;

    public function __construct()
    {
        // CORS Headers & Content-Type
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * Chuẩn hóa API Response Envelope
     */
    protected function respond($status, $message, $data = null, $meta = null)
    {
        http_response_code($status);
        $isSuccess = ($status >= 200 && $status < 300);

        $response = [
            "status"    => $status,
            "success"   => $isSuccess,
            "message"   => $message
        ];

        if ($data !== null) {
            $response["data"] = $data;
        }

        if ($meta !== null) {
            $response["meta"] = $meta;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Lấy JSON Request Payload
     */
    protected function getJsonPayload()
    {
        $input = file_get_contents("php://input");
        if (!empty($input)) {
            $decoded = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }
        return !empty($_POST) ? $_POST : $_GET;
    }

    /**
     * Xác thực người dùng qua Bearer Token hoặc Session
     */
    protected function authenticateUser()
    {
        // 1. Kiểm tra session trước
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user']['id'])) {
            $this->currentUser = $_SESSION['user'];
            return $this->currentUser;
        }

        // 2. Kiểm tra Bearer Token trong Authorization Header
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (empty($authHeader) && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $userModel = new UserModel();
            $user = $userModel->getUserByToken($token);
            if ($user) {
                $this->currentUser = $user;
                return $this->currentUser;
            }
        }

        return null;
    }

    /**
     * Bắt buộc phải đăng nhập mới được gọi API
     */
    protected function requireAuth()
    {
        $user = $this->authenticateUser();
        if (!$user) {
            $this->respond(401, "Yêu cầu đăng nhập hoặc cung cấp Authorization Bearer token hợp lệ!");
        }
        return $user;
    }
}
?>