-- =====================================================================
-- SPORT KICKS - SOCCER CLEATS STORE DATABASE SCHEMA (CHUẨN HÓA 3NF)
-- Designed by Database Engineer
-- Tương thích: MySQL 5.7+ / 8.0+ / MariaDB 10.3+
-- Charset: utf8mb4_unicode_ci (Hỗ trợ tiếng Việt đầy đủ và tối ưu)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `shop_giay_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `shop_giay_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. BẢNG NGƯỜI DÙNG (USERS)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `fullname` VARCHAR(150) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `role` ENUM('customer', 'admin', 'staff') NOT NULL DEFAULT 'customer',
    `status` ENUM('active', 'locked') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_role_status` (`role`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. BẢNG QUÊN MẬT KHẨU (PASSWORD_RESETS)
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(191) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_pw_email_token` (`email`, `token`),
    INDEX `idx_pw_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. BẢNG API ACCESS TOKENS (Cho REST API Authentication)
DROP TABLE IF EXISTS `user_tokens`;
CREATE TABLE `user_tokens` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `token` VARCHAR(128) NOT NULL UNIQUE,
    `device_info` VARCHAR(255) DEFAULT NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_tokens_user` (`user_id`, `token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. BẢNG THƯƠNG HIỆU (BRANDS)
DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `slug` VARCHAR(120) DEFAULT NULL,
    `logo` VARCHAR(255) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. BẢNG PHÂN LOẠI / DANH MỤC (CATEGORIES)
-- Ví dụ: Giày sân cỏ nhân tạo (TF), Cỏ tự nhiên (FG/AG), Giày Futsal (IC), Phụ kiện
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(120) NOT NULL UNIQUE,
    `slug` VARCHAR(140) DEFAULT NULL,
    `ground_code` VARCHAR(20) DEFAULT NULL COMMENT 'TF, FG, AG, IC...',
    `description` TEXT DEFAULT NULL,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. BẢNG SẢN PHẨM (PRODUCTS)
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(280) DEFAULT NULL,
    `sku` VARCHAR(50) DEFAULT NULL UNIQUE,
    `brand_id` INT UNSIGNED DEFAULT NULL,
    `category_id` INT UNSIGNED DEFAULT NULL,
    `price` DECIMAL(12, 0) NOT NULL DEFAULT 0 COMMENT 'Giá bán niêm yết hiện hành',
    `old_price` DECIMAL(12, 0) DEFAULT 0 COMMENT 'Giá gốc trước khi giảm',
    `sale_price` DECIMAL(12, 0) DEFAULT NULL COMMENT 'Giá flash sale',
    `sale_start` DATETIME DEFAULT NULL,
    `sale_end` DATETIME DEFAULT NULL,
    `image` VARCHAR(255) NOT NULL COMMENT 'Ảnh đại diện chính',
    `ground_type` VARCHAR(50) DEFAULT NULL COMMENT 'Sân cỏ nhân tạo TF, Cỏ tự nhiên FG/AG, Futsal IC',
    `upper_material` VARCHAR(100) DEFAULT NULL COMMENT 'Da Kangaroo, Sợi dệt Flyknit, Da tổng hợp...',
    `description` LONGTEXT DEFAULT NULL,
    `views` INT UNSIGNED NOT NULL DEFAULT 0,
    `is_featured` TINYINT(1) DEFAULT 0,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_products_cat_price` (`category_id`, `price`),
    INDEX `idx_products_brand_price` (`brand_id`, `price`),
    INDEX `idx_products_created` (`created_at` DESC),
    INDEX `idx_products_views` (`views` DESC),
    FULLTEXT INDEX `ft_products_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. BẢNG BIẾN THỂ SIZE & TỒN KHO (PRODUCT_VARIANTS)
DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE `product_variants` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT UNSIGNED NOT NULL,
    `size` VARCHAR(10) NOT NULL COMMENT '38, 39, 40, 40.5, 41, 42, 42.5, 43, 44, 45',
    `stock` INT NOT NULL DEFAULT 0,
    `sku_variant` VARCHAR(80) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_product_size` (`product_id`, `size`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_variants_stock` (`product_id`, `stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. BẢNG THƯ VIỆN HÌNH ẢNH SẢN PHẨM (PRODUCT_IMAGES)
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT UNSIGNED NOT NULL,
    `image_url` VARCHAR(255) NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_images_product` (`product_id`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. BẢNG GIỎ HÀNG (CART_ITEMS)
DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE `cart_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `size` VARCHAR(10) NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_cart_item` (`user_id`, `product_id`, `size`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. BẢNG ĐƠN HÀNG (ORDERS)
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_code` VARCHAR(32) DEFAULT NULL UNIQUE,
    `user_id` INT UNSIGNED NOT NULL,
    `fullname` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `address` TEXT NOT NULL,
    `note` TEXT DEFAULT NULL,
    `subtotal` DECIMAL(12, 0) NOT NULL DEFAULT 0,
    `discount_amount` DECIMAL(12, 0) NOT NULL DEFAULT 0,
    `shipping_fee` DECIMAL(12, 0) NOT NULL DEFAULT 0,
    `total_money` DECIMAL(12, 0) NOT NULL DEFAULT 0,
    `payment_method` ENUM('COD', 'BANKING', 'VNPAY', 'MOMO') NOT NULL DEFAULT 'COD',
    `payment_status` ENUM('unpaid', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid',
    `status` ENUM('pending', 'processing', 'shipping', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
    INDEX `idx_orders_user_created` (`user_id`, `created_at` DESC),
    INDEX `idx_orders_status` (`status`, `created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. BẢNG CHI TIẾT ĐƠN HÀNG (ORDER_DETAILS)
DROP TABLE IF EXISTS `order_details`;
CREATE TABLE `order_details` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `size` VARCHAR(10) NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `price` DECIMAL(12, 0) NOT NULL DEFAULT 0,
    `subtotal` DECIMAL(12, 0) NOT NULL DEFAULT 0,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT,
    INDEX `idx_od_order` (`order_id`),
    INDEX `idx_od_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. BẢNG LỊCH SỬ NHẬP KHO (STOCK_IMPORTS)
DROP TABLE IF EXISTS `stock_imports`;
CREATE TABLE `stock_imports` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `import_code` VARCHAR(50) DEFAULT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `size` VARCHAR(10) NOT NULL,
    `quantity` INT NOT NULL,
    `import_price` DECIMAL(12, 0) NOT NULL DEFAULT 0,
    `supplier` VARCHAR(150) DEFAULT 'Nhà phân phối chính hãng',
    `note` VARCHAR(255) DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_stock_prod_size` (`product_id`, `size`),
    INDEX `idx_stock_created` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

