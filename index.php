<?php
// 1. Hiển thị lỗi để dễ debug (Tắt khi chạy thật)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Khởi động Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Nạp cấu hình, Helper bảo mật và Kết nối Database
require_once 'config/constants.php';
require_once 'config/db.php';
require_once 'helpers/SecurityHelper.php';
require_once 'helpers/CsrfHelper.php';
require_once 'helpers/AuthHelper.php';

// Khởi tạo CSRF token
CsrfHelper::init();

// 4. Lấy Controller và Action từ URL
$controllerInput = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$actionName = isset($_GET['action']) ? $_GET['action'] : 'index';

// 5. Chuẩn hóa tên Controller admin -> AdminController
$controllerName = ucfirst($controllerInput) . 'Controller';

// 6. Đường dẫn file Controller (Phân tách logic API)
if (strpos(strtolower($controllerInput), 'api') === 0) {
    // API request -> load from controllers/api/
    $controllerPath = "controllers/api/$controllerName.php";

    // Tự động nạp file BaseApiController
    require_once "controllers/api/BaseApiController.php";
} else {
    // Normal web request -> load from controllers/
    $controllerPath = "controllers/$controllerName.php";
}

// 7. KIỂM TRA & GỌI FILE
if (!file_exists($controllerPath)) {
    http_response_code(404);
    die("LỖI 404: Không tìm thấy file Controller tại: <strong>" . htmlspecialchars($controllerPath) . "</strong>.");
}

require_once $controllerPath;

// 8. KIỂM TRA CLASS
if (!class_exists($controllerName)) {
    http_response_code(404);
    die("LỖI 404: File tồn tại nhưng không thấy Class tên là <strong>" . htmlspecialchars($controllerName) . "</strong>.");
}

$controller = new $controllerName();

// 9. KIỂM TRA HÀM (ACTION)
if (!method_exists($controller, $actionName)) {
    http_response_code(404);
    die("LỖI 404: Không tìm thấy hàm (action) tên là <strong>" . htmlspecialchars($actionName) . "</strong> trong " . htmlspecialchars($controllerName) . ".");
}

// 10. Chạy hàm
$controller->$actionName();
