<?php
/**
 * Database Migration & Seeder Runner
 * Dùng để khởi tạo lược đồ chuẩn hóa và nạp dữ liệu mẫu shop giày bóng đá.
 * Có thể chạy qua CLI: php database/migrate.php
 * Hoặc chạy qua Trình duyệt: http://localhost/shop_ban_hang/database/migrate.php
 */

require_once __DIR__ . '/../config/db.php';

header('Content-Type: text/html; charset=utf-8');
echo "<h2>--- KHỞI TẠO HỆ THỐNG CƠ SỞ DỮ LIỆU SHOP GIÀY BÓNG ĐÁ ---</h2>";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        die("<p style='color:red;'>[LỖI] Không thể kết nối tới cơ sở dữ liệu MySQL!</p>");
    }

    echo "<p style='color:green;'>[OK] Kết nối Database thành công.</p>";

    // 1. Chạy Schema
    $schemaFile = __DIR__ . '/schema_normalized.sql';
    if (file_exists($schemaFile)) {
        echo "<p>Đang thực thi <code>schema_normalized.sql</code>...</p>";
        $sql = file_get_contents($schemaFile);
        $conn->exec($sql);
        echo "<p style='color:green;'>[THÀNH CÔNG] Đã tạo toàn bộ bảng dữ liệu chuẩn hóa 3NF!</p>";
    }

    // 2. Chạy Seeder
    $seedFile = __DIR__ . '/seed_shoes.sql';
    if (file_exists($seedFile)) {
        echo "<p>Đang nạp dữ liệu mẫu giày bóng đá (Nike, Adidas, Puma, Mizuno)...</p>";
        $sqlSeed = file_get_contents($seedFile);
        $conn->exec($sqlSeed);
        echo "<p style='color:green;'>[THÀNH CÔNG] Đã nạp thành công dữ liệu mẫu sản phẩm, size và tài khoản Admin/Khách hàng!</p>";
    }

    echo "<hr><div style='padding:15px; background:#e0f2fe; border-left:4px solid #0284c7;'>";
    echo "<h4>Thông tin tài khoản kiểm thử:</h4>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@sportkicks.vn / Mật khẩu: <code>admin123</code></li>";
    echo "<li><strong>Khách hàng:</strong> khachhang@sportkicks.vn / Mật khẩu: <code>123456</code></li>";
    echo "</ul>";
    echo "<p><a href='../index.php' style='color:#0284c7; font-weight:bold;'>Quay lại Trang Chủ &rarr;</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<p style='color:red;'>[LỖI PDO]: " . htmlspecialchars($e->getMessage()) . "</p>";
}

