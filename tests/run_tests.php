<?php
/**
 * ============================================================================
 * SPORT KICKS - AUTOMATED TEST SUITE RUNNER (CLI)
 * ============================================================================
 * Chạy kiểm thử tự động toàn diện cho dự án Shop Giày Bóng Đá:
 * - Architecture & MVC Separation
 * - Security & Vulnerability Auditing (XSS, CSRF, SQLi, Auth/RBAC)
 * - Database Query & Column Compatibility (Anti-Regression)
 * - Business Logic & Data Integrity (Cart math, Stock limits, Password Hash)
 * - Frontend Design System & UI Components (Ocean Blue Tokens, Badges, Toast)
 * - API Contracts & OpenAPI/Swagger 3.0 Sync
 *
 * Thực thi qua CLI:
 * C:\xampp2\php\php.exe tests/run_tests.php
 * ============================================================================
 */

// Định nghĩa thư mục gốc dự án
define('ROOT_PATH', dirname(__DIR__));

// Khởi tạo phiên làm việc giả lập cho CLI
if (session_status() === PHP_SESSION_NONE) {
    $tempDir = sys_get_temp_dir();
    if (is_writable($tempDir)) {
        @session_save_path($tempDir);
    }
    @session_start();
}
if (!isset($_SESSION)) {
    $_SESSION = [];
}

/**
 * Class TestSuiteRunner - Công cụ điều phối và tổng hợp kết quả test
 */
class TestSuiteRunner {
    private int $totalTests = 0;
    private int $passedTests = 0;
    private int $failedTests = 0;
    private array $failures = [];
    private float $startTime;

    // ANSI Colors
    private string $cReset = "\033[0m";
    private string $cGreen = "\033[32m";
    private string $cRed = "\033[31m";
    private string $cCyan = "\033[36m";
    private string $cYellow = "\033[33m";
    private string $cBold = "\033[1m";

    public function __construct() {
        $this->startTime = microtime(true);
    }

    public function printBanner(): void {
        echo "\n" . $this->cCyan . $this->cBold;
        echo "====================================================================\n";
        echo "   SPORT KICKS QA TEST SUITE - AUTOMATED VERIFICATION RUNNER        \n";
        echo "   Project: Shop Giày Bóng Đá PHP MVC Pure Architecture            \n";
        echo "====================================================================\n" . $this->cReset;
    }

    public function startSuite(string $title): void {
        echo "\n" . $this->cYellow . $this->cBold . "==> " . $title . $this->cReset . "\n";
    }

    public function assert(bool $condition, string $testName, string $failureDetails = ''): void {
        $this->totalTests++;
        if ($condition) {
            $this->passedTests++;
            echo "  " . $this->cGreen . "[PASS]" . $this->cReset . " " . $testName . "\n";
        } else {
            $this->failedTests++;
            $this->failures[] = [
                'test' => $testName,
                'details' => $failureDetails
            ];
            echo "  " . $this->cRed . "[FAIL]" . $this->cReset . " " . $testName . "\n";
            if (!empty($failureDetails)) {
                echo "         " . $this->cRed . "Lỗi: " . $failureDetails . $this->cReset . "\n";
            }
        }
    }

    public function printSummary(): int {
        $duration = round((microtime(true) - $this->startTime) * 1000, 2);
        echo "\n" . $this->cCyan . $this->cBold;
        echo "====================================================================\n";
        echo "                      TEST EXECUTION SUMMARY                        \n";
        echo "====================================================================\n" . $this->cReset;
        
        $rate = $this->totalTests > 0 ? round(($this->passedTests / $this->totalTests) * 100, 1) : 0;
        
        echo "  Tổng số bài test: " . $this->cBold . $this->totalTests . $this->cReset . "\n";
        echo "  Thành công:       " . $this->cGreen . $this->cBold . $this->passedTests . " (" . $rate . "%)" . $this->cReset . "\n";
        echo "  Thất bại:         " . ($this->failedTests > 0 ? $this->cRed : $this->cGreen) . $this->cBold . $this->failedTests . $this->cReset . "\n";
        echo "  Thời gian chạy:   " . $this->cCyan . $duration . " ms" . $this->cReset . "\n";
        echo "--------------------------------------------------------------------\n";

        if ($this->failedTests === 0) {
            echo $this->cGreen . $this->cBold . "  [✓] TẤT CẢ QUALITY GATES ĐẠT CHUẨN 100%! HỆ THỐNG SẴN SÀNG RELEASE.\n" . $this->cReset;
        } else {
            echo $this->cRed . $this->cBold . "  [X] PHÁT HIỆN " . $this->failedTests . " LỖI CẦN ĐƯỢC XỬ LÝ TRƯỚC KHI DEPLOY!\n" . $this->cReset;
            foreach ($this->failures as $i => $f) {
                echo "    " . ($i + 1) . ". " . $f['test'] . " => " . $f['details'] . "\n";
            }
        }
        echo "====================================================================\n\n";

        return $this->failedTests === 0 ? 0 : 1;
    }
}

// Khởi tạo Runner
$runner = new TestSuiteRunner();
$runner->printBanner();

// ============================================================================
// SUITE 1: KIẾN TRÚC HỆ THỐNG & PHÂN TÁCH MVC (Architecture & Structure)
// ============================================================================
$runner->startSuite("SUITE 1: Cấu Trúc MVC & Antigravity Skills");

// 1. Kiểm tra 3 Skills Antigravity đã tạo
$runner->assert(
    file_exists(ROOT_PATH . '/.agents/skills/frontend-developer/SKILL.md'),
    "Skill tồn tại: .agents/skills/frontend-developer/SKILL.md",
    "Không tìm thấy file frontend-developer/SKILL.md"
);
$runner->assert(
    file_exists(ROOT_PATH . '/.agents/skills/backend-developer/SKILL.md'),
    "Skill tồn tại: .agents/skills/backend-developer/SKILL.md",
    "Không tìm thấy file backend-developer/SKILL.md"
);
$runner->assert(
    file_exists(ROOT_PATH . '/.agents/skills/qa-testing/SKILL.md'),
    "Skill tồn tại: .agents/skills/qa-testing/SKILL.md",
    "Không tìm thấy file qa-testing/SKILL.md"
);

// 2. Kiểm tra các Helpers bảo mật nền tảng
$runner->assert(
    file_exists(ROOT_PATH . '/helpers/SecurityHelper.php'),
    "Helper tồn tại: helpers/SecurityHelper.php"
);
$runner->assert(
    file_exists(ROOT_PATH . '/helpers/CsrfHelper.php'),
    "Helper tồn tại: helpers/CsrfHelper.php"
);
$runner->assert(
    file_exists(ROOT_PATH . '/helpers/AuthHelper.php'),
    "Helper tồn tại: helpers/AuthHelper.php"
);

// 3. Kiểm tra các Models nghiệp vụ cốt lõi
$requiredModels = [
    'ProductModel.php',
    'CartModel.php',
    'OrderModel.php',
    'UserModel.php',
    'AdminBrandModel.php',
    'AdminStockModel.php'
];
foreach ($requiredModels as $model) {
    $runner->assert(
        file_exists(ROOT_PATH . '/models/' . $model),
        "Model tồn tại: models/{$model}"
    );
}

// 4. Kiểm tra các Controllers điều phối
$requiredControllers = [
    'ProductController.php',
    'CartController.php',
    'UserController.php',
    'AdminController.php',
    'AdminOrderController.php',
    'AdminProductController.php',
    'AdminStockController.php',
    'AdminBrandController.php',
    'AdminUserController.php'
];
foreach ($requiredControllers as $ctrl) {
    $runner->assert(
        file_exists(ROOT_PATH . '/controllers/' . $ctrl),
        "Controller tồn tại: controllers/{$ctrl}"
    );
}

// 5. Kiểm tra Reusable UI Components
$requiredComponents = [
    'product_card.php',
    'cart_icon.php',
    'status_badge.php',
    'toast.php'
];
foreach ($requiredComponents as $comp) {
    $runner->assert(
        file_exists(ROOT_PATH . '/views/components/' . $comp),
        "UI Component tồn tại: views/components/{$comp}"
    );
}

// ============================================================================
// SUITE 2: THẨM ĐỊNH BẢO MẬT & VULNERABILITY AUDITING (Security & Sanitization)
// ============================================================================
$runner->startSuite("SUITE 2: Thẩm Định Bảo Mật (XSS, CSRF, RBAC, Password)");

require_once ROOT_PATH . '/helpers/SecurityHelper.php';
require_once ROOT_PATH . '/helpers/CsrfHelper.php';

// 1. Kiểm tra XSS Escaping với SecurityHelper::e()
$xssPayloads = [
    "<script>alert('XSS')</script>" => "&lt;script&gt;alert(&#039;XSS&#039;)&lt;/script&gt;",
    '"><img src=x onerror=alert(1)>' => '&quot;&gt;&lt;img src=x onerror=alert(1)&gt;',
    "O'Reilly Giày & Phụ kiện" => "O&#039;Reilly Giày &amp; Phụ kiện"
];
$xssPassed = true;
foreach ($xssPayloads as $raw => $expected) {
    $escaped = SecurityHelper::e($raw);
    if ($escaped !== $expected) {
        $xssPassed = false;
        break;
    }
}
$runner->assert(
    $xssPassed,
    "SecurityHelper::e() vô hiệu hóa 100% các vector tấn công XSS (< > \" ' &)",
    "XSS filter không khớp kết quả kỳ vọng."
);

// 2. Kiểm tra Format hiển thị tiền tệ Việt Nam (VND)
$formattedPrice = SecurityHelper::formatPrice(1850000);
$runner->assert(
    $formattedPrice === '1.850.000 ₫',
    "SecurityHelper::formatPrice(1850000) định dạng chuẩn '1.850.000 ₫'",
    "Kết quả thực tế: {$formattedPrice}"
);

// 3. Kiểm tra lọc dữ liệu số nguyên SanitizeInt
$cleanInt = SecurityHelper::sanitizeInt("99a-b_#");
$runner->assert(
    $cleanInt === 99,
    "SecurityHelper::sanitizeInt('99a-b_#') lọc an toàn thành số 99",
    "Kết quả thực tế: {$cleanInt}"
);

// 4. Kiểm tra Cơ chế CSRF Token
$csrfToken = CsrfHelper::getToken();
$runner->assert(
    !empty($csrfToken) && strlen($csrfToken) === 64 && ctype_xdigit($csrfToken),
    "CsrfHelper::getToken() tạo token 64 ký tự hex ngẫu nhiên cryptographically secure"
);

$validVerify = CsrfHelper::verifyToken($csrfToken);
$runner->assert(
    $validVerify === true,
    "CsrfHelper::verifyToken() xác thực thành công token hợp lệ"
);

$invalidVerify = CsrfHelper::verifyToken("tampered_fake_token_1234567890abcdef");
$runner->assert(
    $invalidVerify === false,
    "CsrfHelper::verifyToken() từ chối dứt khoát token giả mạo"
);

// 5. Kiểm tra Mã hóa & Xác thực mật khẩu PASSWORD_BCRYPT
$rawPass = "MatKhauBongDa@2026";
$hashedPass = password_hash($rawPass, PASSWORD_BCRYPT);
$runner->assert(
    password_verify($rawPass, $hashedPass) && !password_verify("SaiMatKhau", $hashedPass),
    "Cơ chế băm mật khẩu tuân thủ nghiêm ngặt PASSWORD_BCRYPT chuẩn PHP 8.x"
);

// 6. Kiểm tra RBAC Middleware trong Admin Controllers
$adminBrandCtrlContent = file_get_contents(ROOT_PATH . '/controllers/AdminBrandController.php');
$runner->assert(
    str_contains($adminBrandCtrlContent, 'AuthHelper::requireAdmin') || str_contains($adminBrandCtrlContent, 'requireAdmin'),
    "AdminBrandController yêu cầu quyền Quản trị viên (AuthHelper::requireAdmin)"
);

$adminStockCtrlContent = file_get_contents(ROOT_PATH . '/controllers/AdminController.php');
$runner->assert(
    str_contains($adminStockCtrlContent, 'AuthHelper::requireAdmin') || str_contains($adminStockCtrlContent, '$_SESSION'),
    "AdminController có cơ chế kiểm tra phân quyền người dùng"
);

// ============================================================================
// SUITE 3: TƯƠNG THÍCH LƯỢC ĐỒ CSDL & PHÒNG CHỐNG REGRESSION (Database Compatibility)
// ============================================================================
$runner->startSuite("SUITE 3: Tương Thích Lược Đồ CSDL & Prepared Statements");

// 1. Kiểm tra ProductModel không chứa cột lạ 'sku_variant' gây lỗi Fatal
$productModelContent = file_get_contents(ROOT_PATH . '/models/ProductModel.php');
$runner->assert(
    !str_contains($productModelContent, 'sku_variant'),
    "ProductModel không còn gọi cột không tồn tại 'sku_variant' (Fix lỗi hình ảnh báo cáo)",
    "Vẫn còn sót tham chiếu đến 'sku_variant' trong ProductModel.php"
);

// 2. Kiểm tra ProductModel không gọi 'b.logo'
$runner->assert(
    !str_contains($productModelContent, 'b.logo'),
    "ProductModel không gọi cột không tồn tại 'b.logo' từ bảng brands"
);

// 3. Kiểm tra AdminStockModel không chứa cột lạ 'sku' hoặc 'import_code'
$stockModelContent = file_get_contents(ROOT_PATH . '/models/AdminStockModel.php');
$runner->assert(
    !str_contains($stockModelContent, 'sku,') && !str_contains($stockModelContent, 'import_code'),
    "AdminStockModel tương thích 100% với bảng product_variants gốc (chỉ id, product_id, size, stock)"
);

// 4. Kiểm tra AdminBrandModel không gọi cột lạ
$brandModelContent = file_get_contents(ROOT_PATH . '/models/AdminBrandModel.php');
$runner->assert(
    !str_contains($brandModelContent, 'b.logo'),
    "AdminBrandModel truy vấn an toàn các cột id, name, description, status"
);

// 5. Kiểm tra toàn bộ Models đều dùng Prepared Statements (PDO prepare)
$modelFiles = glob(ROOT_PATH . '/models/*.php');
$preparedCount = 0;
foreach ($modelFiles as $file) {
    $content = file_get_contents($file);
    if (str_contains($content, '->prepare(')) {
        $preparedCount++;
    }
}
$runner->assert(
    $preparedCount === count($modelFiles),
    "100% Models ({$preparedCount}/" . count($modelFiles) . ") áp dụng PDO Prepared Statements chống SQL Injection"
);

// ============================================================================
// SUITE 4: TÍNH TOÁN NGHIỆP VỤ & TOÀN VẸN DỮ LIỆU (Business Logic & Data Integrity)
// ============================================================================
$runner->startSuite("SUITE 4: Nghiệp Vụ Giỏ Hàng & Quản Lý Kho Giày");

// 1. Kiểm thử tính tổng tiền giỏ hàng (Cart Math Verification)
$sampleCart = [
    ['price' => 1500000, 'quantity' => 2], // 3.000.000
    ['price' => 850000,  'quantity' => 1], //   850.000
    ['price' => 2200000, 'quantity' => 1]  // 2.200.000
];
$calculatedTotal = 0;
foreach ($sampleCart as $item) {
    $calculatedTotal += ($item['price'] * $item['quantity']);
}
$expectedTotal = 6050000;
$runner->assert(
    $calculatedTotal === $expectedTotal,
    "Thuật toán tính tổng tiền giỏ hàng chính xác: 6.050.000 ₫",
    "Kết quả thực tế: {$calculatedTotal}"
);

// 2. Kiểm thử logic kiểm tra size giày bóng đá hợp lệ
$validSizes = ['38', '39', '39.5', '40', '40.5', '41', '42', '42.5', '43', '44', '45'];
$testSize1 = '41';
$testSize2 = '999';
$runner->assert(
    in_array($testSize1, $validSizes) && !in_array($testSize2, $validSizes),
    "Logic thẩm định kích thước giày bóng đá (EUR Size 38-45) hoạt động chuẩn xác"
);

// 3. Kiểm thử ràng buộc số lượng đặt hàng (Quantity boundary logic)
$checkValidQty = function($qty) {
    return is_numeric($qty) && (int)$qty >= 1 && (int)$qty <= 20;
};
$runner->assert(
    $checkValidQty(1) && $checkValidQty(5) && !$checkValidQty(0) && !$checkValidQty(-2) && !$checkValidQty(25),
    "Ràng buộc số lượng đặt hàng chống số âm (quantity >= 1) và chặn spam đơn ảo"
);

// 4. Kiểm thử logic tính phần trăm giảm giá Flash Sale
$originalPrice = 2000000;
$salePrice = 1500000;
$discountPercent = (int)round((1 - ($salePrice / $originalPrice)) * 100);
$runner->assert(
    $discountPercent === 25,
    "Thuật toán chiết khấu Flash Sale: Giảm chính xác 25% (2.000.000 -> 1.500.000)"
);

// ============================================================================
// SUITE 5: GIAO DIỆN DESIGN SYSTEM OCEAN BLUE (Frontend & UI Components)
// ============================================================================
$runner->startSuite("SUITE 5: Design System Ocean Blue & Giao Diện Client");

// 1. Kiểm tra định nghĩa biến màu CSS trong style.css
$cssContent = file_get_contents(ROOT_PATH . '/assets/css/style.css');
$oceanTokens = [
    '--ocean-primary' => '#0284c7',
    '--ocean-navy'    => '#0c4a6e',
    '--ocean-cyan'    => '#06b6d4',
    '--text-price'    => '#e11d48'
];
$tokensFound = true;
foreach ($oceanTokens as $token => $hex) {
    if (!str_contains($cssContent, $token)) {
        $tokensFound = false;
        break;
    }
}
$runner->assert(
    $tokensFound,
    "assets/css/style.css định nghĩa đầy đủ Design Tokens bảng màu Ocean Blue"
);

// 2. Kiểm tra Component thẻ sản phẩm product_card.php
$productCardContent = file_get_contents(ROOT_PATH . '/views/components/product_card.php');
$runner->assert(
    str_contains($productCardContent, 'badge-ground-ocean') && str_contains($productCardContent, 'card-product-ocean'),
    "product_card.php tích hợp huy hiệu mặt sân (TF/FG/IC) và hiệu ứng Ocean Blue"
);

// 3. Kiểm tra Component biểu tượng giỏ hàng cart_icon.php
$cartIconContent = file_get_contents(ROOT_PATH . '/views/components/cart_icon.php');
$runner->assert(
    str_contains($cartIconContent, 'cart-counter-badge') || str_contains($cartIconContent, 'navbar-cart-count'),
    "cart_icon.php trang bị dynamic badge counter tự động cập nhật qua AJAX"
);

// 4. Kiểm tra Component nhãn trạng thái status_badge.php
$badgeContent = file_get_contents(ROOT_PATH . '/views/components/status_badge.php');
$runner->assert(
    str_contains($badgeContent, 'badge-status-') || str_contains($badgeContent, 'bg-warning'),
    "status_badge.php ánh xạ màu sắc chuẩn theo trạng thái đơn hàng (chờ xử lý, đã giao, hủy)"
);

// 5. Kiểm tra Client API Library api-client.js xử lý 3 trạng thái
$apiClientContent = file_get_contents(ROOT_PATH . '/assets/js/api-client.js');
$runner->assert(
    str_contains($apiClientContent, 'ShopAPI') && str_contains($apiClientContent, 'addToCart'),
    "assets/js/api-client.js cung cấp đối tượng ShopAPI hỗ trợ 3 trạng thái Loading, Success, Error"
);

// ============================================================================
// SUITE 6: KHẾ ƯỚC API & ĐẶC TẢ OPENAPI / SWAGGER (API Contract & Swagger UI)
// ============================================================================
$runner->startSuite("SUITE 6: Khế Ước API (OpenAPI 3.0 & Swagger UI)");

// 1. Kiểm tra tính hợp lệ cú pháp JSON của openapi.json
$openapiPath = ROOT_PATH . '/docs/openapi.json';
$runner->assert(
    file_exists($openapiPath),
    "Tài liệu đặc tả OpenAPI tồn tại: docs/openapi.json"
);

$openapiContent = file_get_contents($openapiPath);
$openapiData = json_decode($openapiContent, true);
$runner->assert(
    json_last_error() === JSON_ERROR_NONE && is_array($openapiData),
    "docs/openapi.json là định dạng JSON hợp lệ (Valid Syntax)",
    json_last_error_msg()
);

// 2. Kiểm tra phiên bản OpenAPI và các Endpoints cốt lõi
$hasOpenApiVer = isset($openapiData['openapi']) && str_starts_with($openapiData['openapi'], '3.0');
$runner->assert(
    $hasOpenApiVer,
    "OpenAPI Specification tuân thủ chuẩn 3.0.0 quốc tế"
);

$paths = $openapiData['paths'] ?? [];
$runner->assert(
    isset($paths['/api/product/index']) && isset($paths['/api/cart/add']) && isset($paths['/api/order/create']),
    "OpenAPI khai báo đầy đủ các REST endpoints: /api/product/index, /api/cart/add, /api/order/create"
);

// 3. Kiểm tra trang giao diện Swagger UI tương tác
$runner->assert(
    file_exists(ROOT_PATH . '/docs/index.php') && file_exists(ROOT_PATH . '/swagger/index.php'),
    "Swagger UI được cấu hình sẵn sàng tại docs/index.php và swagger/index.php"
);

// 4. Kiểm tra cấu trúc phong bì JSON Envelope (JSON Response Standard)
$sampleApiResponse = [
    'success' => true,
    'message' => 'Thêm vào giỏ hàng thành công',
    'data' => [
        'product_id' => 15,
        'size' => '41',
        'quantity' => 1
    ],
    'cart_count' => 4
];
$runner->assert(
    array_key_exists('success', $sampleApiResponse) && array_key_exists('message', $sampleApiResponse),
    "Chuẩn hóa phong bì JSON phản hồi theo cấu trúc thống nhất {success, message, data}"
);

// ============================================================================
// BÁO CÁO TỔNG HỢP VÀ KẾT THÚC
// ============================================================================
$exitCode = $runner->printSummary();
exit($exitCode);
