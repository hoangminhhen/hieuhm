-- =====================================================================
-- SEED DATA MẪU DÀNH CHO SHOP GIÀY BÓNG ĐÁ SPORT KICKS
-- Dữ liệu đầy đủ: Tài khoản Admin, Khách hàng, Hãng, Danh mục, Sản phẩm, Size & Kho
-- =====================================================================

USE `shop_giay_db`;

-- 1. TÀI KHOẢN MẪU (Password bcrypt: admin123 và 123456)
-- admin@sportkicks.vn / admin123
-- khachhang@sportkicks.vn / 123456
INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `phone`, `address`, `role`, `status`) VALUES
(1, 'Quản Trị Viên Sport Kicks', 'admin@sportkicks.vn', '$2y$10$v7m2QpGom8g1hD8kZ.4qI.eK5TzZ6R/oN8cE7mDkQ3ZzIom9vE7ey', '0901234567', '79 Trần Duy Hưng, Cầu Giấy, Hà Nội', 'admin', 'active'),
(2, 'Nguyễn Văn Hải', 'khachhang@sportkicks.vn', '$2y$10$k1z2w3x4y5z6a7b8c9d0e.fGhIjKlMnOpQrStUvWxYzAbCdEfGhIj', '0988776655', '120 Nguyễn Thị Minh Khai, Quận 3, TP.HCM', 'customer', 'active')
ON DUPLICATE KEY UPDATE `fullname` = VALUES(`fullname`);

-- 2. THƯƠNG HIỆU GIÀY BÓNG ĐÁ
INSERT INTO `brands` (`id`, `name`, `slug`, `logo`, `description`, `status`) VALUES
(1, 'Nike Football', 'nike-football', 'https://upload.wikimedia.org/wikipedia/commons/a/a6/Logo_NIKE.svg', 'Thương hiệu hàng đầu thế giới với dòng Mercurial, Phantom, Tiempo', 1),
(2, 'Adidas Football', 'adidas-football', 'https://upload.wikimedia.org/wikipedia/commons/2/20/Adidas_Logo.svg', 'Huyền thoại bóng đá thế giới với Predator, X Crazyfast, Copa Pure', 1),
(3, 'Puma Football', 'puma-football', 'https://upload.wikimedia.org/wikipedia/commons/8/88/Puma-Logo.png', 'Dòng giày bứt tốc & linh hoạt: Future, Ultra, King', 1),
(4, 'Mizuno Football', 'mizuno-football', 'https://upload.wikimedia.org/wikipedia/commons/e/eb/Mizuno_logo.svg', 'Thương hiệu giày da thật Kangaroo cao cấp từ Nhật Bản', 1),
(5, 'Kamito', 'kamito', '', 'Giày đá bóng chính hãng phom chân bè dành riêng cho người Việt', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 3. DANH MỤC SẢN PHẨM (THEO MẶT SÂN THI ĐẤU)
INSERT INTO `categories` (`id`, `name`, `slug`, `ground_code`, `description`, `status`) VALUES
(1, 'Giày Sân Cỏ Nhân Tạo (TF)', 'giay-san-co-nhan-tao-tf', 'TF', 'Đế đinh dăm cao su bám sân cỏ nhân tạo mini 5-7 người tại Việt Nam', 1),
(2, 'Giày Sân Cỏ Tự Nhiên (FG/AG)', 'giay-san-co-tu-nhien-fg-ag', 'FG/AG', 'Đế đinh cao trợ lực, bám sâu dành cho mặt cỏ tự nhiên chuẩn 11 người', 1),
(3, 'Giày Futsal Sân Trong Nhà (IC)', 'giay-futsal-ic', 'IC', 'Đế phẳng cao su không lưu vết, tối ưu kỹ thuật rê dắt bóng trên sàn gỗ/xi măng', 1),
(4, 'Phụ Kiện Bóng Đá', 'phu-kien-bong-da', 'ACCESSORY', 'Vớ chống trượt, băng keo thể thao, túi rút đựng giày, bóng thi đấu', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 4. SẢN PHẨM MẪU (BỔ SUNG ĐẦY ĐỦ THUỘC TÍNH SÂN, CHẤT LIỆU, TONE XANH ĐẠI DƯƠNG CHỦ ĐẠO)
INSERT INTO `products` (`id`, `name`, `slug`, `sku`, `brand_id`, `category_id`, `price`, `old_price`, `sale_price`, `sale_start`, `sale_end`, `image`, `ground_type`, `upper_material`, `description`, `views`, `is_featured`, `status`) VALUES
(1, 'Nike Air Zoom Mercurial Vapor 16 Pro TF - Ocean Blueprint', 'nike-air-zoom-mercurial-vapor-16-pro-tf-ocean-blueprint', 'NK-VP16-TF-BLU', 1, 1, 2390000, 2750000, 2190000, NOW() - INTERVAL 1 DAY, NOW() + INTERVAL 30 DAY, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop&q=80', 'Sân cỏ nhân tạo TF', 'Sợi dệt Flyknit phủ Nikeskin', 'Dòng giày bứt tốc đỉnh cao thế hệ mới nhất của Nike với bộ đệm Air Zoom 3/4 chiều dài siêu êm và đinh dăm TF bám sân chuẩn xác.', 1240, 1, 1),
(2, 'Adidas Predator Elite TF - Lucent Blue Pack', 'adidas-predator-elite-tf-lucent-blue', 'AD-PRED-EL-BLU', 2, 1, 2450000, 2900000, NULL, NULL, NULL, 'https://images.unsplash.com/photo-1575537302964-96cd47c06b1b?w=600&auto=format&fit=crop&q=80', 'Sân cỏ nhân tạo TF', 'HybridTouch 2.0 có vân Strikeskin', 'Predator thế hệ mới trang bị gai cao su Strikeskin hỗ trợ sút xoáy hiểm hóc và kiểm soát đường chuyền vượt trội.', 980, 1, 1),
(3, 'Puma Future 7 Match TT - Ocean Rush', 'puma-future-7-match-tt-ocean-rush', 'PM-FUT7-BLU', 3, 1, 1690000, 1990000, 1490000, NOW() - INTERVAL 2 DAY, NOW() + INTERVAL 15 DAY, 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&auto=format&fit=crop&q=80', 'Sân cỏ nhân tạo TF', 'FUZIONFIT dải băng nén thích ứng', 'Thiết kế dải băng FUZIONFIT ôm sát bàn chân, phù hợp với cầu thủ kiến tạo lối chơi linh hoạt trên sân 7.', 720, 1, 1),
(4, 'Mizuno Morelia Neo IV Pro AS TF - Reflex Blue', 'mizuno-morelia-neo-iv-pro-as-tf-blue', 'MZ-NEO4-TF-BLU', 4, 1, 2690000, 2990000, NULL, NULL, NULL, 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&auto=format&fit=crop&q=80', 'Sân cỏ nhân tạo TF', 'Da Kangaroo thượng hạng', 'Độ êm mềm tuyệt đối từ da Kangaroo nguyên bản, phom giày siêu nhẹ phù hợp hoàn hảo với bàn chân bè của người Việt.', 1580, 1, 1),
(5, 'Nike Phantom GX II Academy TF - Sky Blue / Hyper Turquoise', 'nike-phantom-gx-2-academy-tf-sky-blue', 'NK-PHAN-GX2-A', 1, 1, 1850000, 2100000, NULL, NULL, NULL, 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?w=600&auto=format&fit=crop&q=80', 'Sân cỏ nhân tạo TF', 'Lớp phủ NikeSkin mở rộng', 'Khu vực tiếp xúc bóng NikeSkin mở rộng hỗ trợ những cú sút xa uy lực và chạm bóng chuẩn xác trong mọi điều kiện thời tiết.', 650, 0, 1),
(6, 'Nike Mercurial Superfly 10 Academy FG - Deep Marine', 'nike-mercurial-superfly-10-academy-fg', 'NK-SPFLY-FG-BLU', 1, 2, 2150000, 2450000, 1950000, NOW() - INTERVAL 1 DAY, NOW() + INTERVAL 20 DAY, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop&q=80', 'Sân cỏ tự nhiên FG', 'Cổ cao Dynamic Fit sợi dệt', 'Dòng đinh cao FG hỗ trợ bứt tốc cực đại trên mặt sân cỏ tự nhiên, cổ cao ôm chắc cổ chân hạn chế lật sơ mi.', 890, 1, 1),
(7, 'Adidas F50 League FG/MG - Solar Blue Edition', 'adidas-f50-league-fg-solar-blue', 'AD-F50-FG-BLU', 2, 2, 1990000, 2250000, NULL, NULL, NULL, 'https://images.unsplash.com/photo-1575537302964-96cd47c06b1b?w=600&auto=format&fit=crop&q=80', 'Sân cỏ tự nhiên FG/AG', 'Sợi dệt siêu mỏng Fibertouch', 'Sự trở lại của dòng giày huyền thoại F50 với trọng lượng siêu nhẹ và bộ đế Sprintframe 360 bứt tốc như tia chớp.', 540, 0, 1),
(8, 'Nike Lunar Gato II IC - Coastal Navy Blue', 'nike-lunar-gato-ii-ic-navy', 'NK-GATO-IC-BLU', 1, 3, 2190000, 2500000, NULL, NULL, NULL, 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&auto=format&fit=crop&q=80', 'Sân Futsal / Trong nhà IC', 'Da dê mềm mại kết hợp đệm Lunarlon', 'Mẫu giày Futsal huyền thoại bất tử được các hảo thủ sân trong nhà tin dùng nhờ bộ đệm Lunarlon cực êm ái.', 1120, 1, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 5. BIẾN THỂ SIZE VÀ TỒN KHO CHI TIẾT
INSERT INTO `product_variants` (`product_id`, `size`, `stock`, `sku_variant`) VALUES
-- Nike Vapor 16
(1, '39', 8, 'NK-VP16-39'),
(1, '40', 12, 'NK-VP16-40'),
(1, '41', 15, 'NK-VP16-41'),
(1, '42', 9, 'NK-VP16-42'),
(1, '43', 4, 'NK-VP16-43'),
-- Adidas Predator
(2, '39', 5, 'AD-PRED-39'),
(2, '40', 8, 'AD-PRED-40'),
(2, '41', 10, 'AD-PRED-41'),
(2, '42', 6, 'AD-PRED-42'),
(2, '43', 2, 'AD-PRED-43'),
-- Puma Future 7
(3, '40', 7, 'PM-FUT7-40'),
(3, '41', 11, 'PM-FUT7-41'),
(3, '42', 8, 'PM-FUT7-42'),
-- Mizuno Morelia Neo IV
(4, '39', 6, 'MZ-NEO4-39'),
(4, '40', 14, 'MZ-NEO4-40'),
(4, '41', 16, 'MZ-NEO4-41'),
(4, '42', 10, 'MZ-NEO4-42'),
(4, '43', 5, 'MZ-NEO4-43'),
-- Nike Phantom GX II
(5, '40', 6, 'NK-PHAN-40'),
(5, '41', 9, 'NK-PHAN-41'),
(5, '42', 7, 'NK-PHAN-42'),
-- Nike Superfly FG
(6, '40', 4, 'NK-SPFLY-40'),
(6, '41', 6, 'NK-SPFLY-41'),
(6, '42', 5, 'NK-SPFLY-42'),
-- Adidas F50 FG
(7, '40', 5, 'AD-F50-40'),
(7, '41', 7, 'AD-F50-41'),
(7, '42', 4, 'AD-F50-42'),
-- Nike Lunar Gato IC
(8, '39', 5, 'NK-GATO-39'),
(8, '40', 8, 'NK-GATO-40'),
(8, '41', 12, 'NK-GATO-41'),
(8, '42', 9, 'NK-GATO-42')
ON DUPLICATE KEY UPDATE `stock` = VALUES(`stock`);

