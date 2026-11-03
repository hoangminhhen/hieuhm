---
name: qa-testing
description: "Quy chuẩn kiểm thử chất lượng toàn diện (Unit Test, Integration Test, Security Audit, Business Logic Test, API Contract Test) và công cụ tự động hóa test runner (CLI) cho Shop Giày Bóng Đá PHP MVC."
---

# Skill: QA Engineer & Automated Testing Architect

Skill này định hình toàn bộ quy chuẩn kiểm thử chất lượng phần mềm, đảm bảo tính ổn định, độ tin cậy, an toàn bảo mật và trải nghiệm người dùng không lỗi cho dự án **Shop Giày Bóng Đá Sport Kicks**:

---

## 1. Tháp Kiểm Thử (Testing Pyramid) Cho Dự Án PHP MVC

Hệ thống kiểm thử chất lượng được cấu trúc theo 4 tầng kiểm định:

```text
               / \
              /   \
             / E2E \        Tầng 4: End-to-End & UI Component Testing
            /-------\       (Kiểm tra hiển thị, Ocean Blue Design System, Responsive)
           /   API   \      Tầng 3: API Contract & Security Auditing
          /-----------\     (OpenAPI 3.0 specs, CSRF, XSS, SQLi, Auth Guard)
         / Integration \    Tầng 2: Integration Testing
        /---------------\   (Database Transactions, Cart Flow, Order Lifecycle)
       /    Unit Tests   \  Tầng 1: Unit Testing
      /-------------------\ (Password Hashing, Stock Limits, Cart Math, Helpers)
```

### Tiêu Chuẩn Nghiệm Thu (Acceptance Criteria)
1. **100% Pass Rate:** Bộ test tự động phải vượt qua toàn bộ các assertions trước khi triển khai hoặc release tính năng mới.
2. **Zero Security Vulnerability:** Không xuất hiện lỗ hổng SQL Injection thô sơ, không bypass được CSRF trong form POST và 100% dữ liệu xuất ra HTML phải được lọc XSS qua `SecurityHelper::e()`.
3. **Data Integrity:** Không bao giờ xảy ra tình trạng âm kho (stock < 0) hoặc sai lệch tổng tiền đơn hàng do khách hàng sửa giá phía Client.

---

## 2. Kiểm Thử Tầng Đơn Vị (Unit Testing)

Tập trung kiểm thử các hàm tính toán độc lập, các lớp Helper và tính toán logic không phụ thuộc trực tiếp vào network bên ngoài:

### 2.1 Kiểm thử Logic Giỏ Hàng & Tính Tiền
- **Tính tổng tiền giỏ hàng:**
  $$\text{Item Subtotal} = \text{Price} \times \text{Quantity}$$
  $$\text{Cart Total} = \sum_{i=1}^{n} \text{Subtotal}_i$$
- **Ràng buộc số lượng sản phẩm:**
  - Số lượng thêm vào giỏ phải là số nguyên dương: $quantity \ge 1$.
  - Số lượng thêm không được vượt quá số lượng tồn kho khả dụng của size đó: $quantity \le stock$.
  - Cập nhật số lượng về 0 tương đương với việc xóa sản phẩm khỏi giỏ hàng.

### 2.2 Kiểm thử Bảo Mật Mật Khẩu (Password Hashing)
- Sử dụng thuật toán `PASSWORD_BCRYPT` chuẩn của PHP.
- `password_verify($rawPassword, $hash)` phải trả về `true` khi đúng mật khẩu và `false` khi sai.
- Không lưu trữ mật khẩu dưới dạng plaintext hoặc mã hóa 1 chiều lỗi thời (MD5, SHA1).

### 2.3 Kiểm định Helpers Nền Tảng
- [`helpers/SecurityHelper.php`](file:///c:/xampp2/htdocs/shop_ban_hang/helpers/SecurityHelper.php):
  - `SecurityHelper::e("<script>alert('XSS')</script>")` $\rightarrow$ `&lt;script&gt;alert(&#039;XSS&#039;)&lt;/script&gt;`.
  - `SecurityHelper::formatPrice(1500000)` $\rightarrow$ `"1.500.000 ₫"`.
  - `SecurityHelper::sanitizeInt("123abc")` $\rightarrow$ `123`.
- [`helpers/CsrfHelper.php`](file:///c:/xampp2/htdocs/shop_ban_hang/helpers/CsrfHelper.php):
  - `CsrfHelper::getToken()` sinh mã token ngẫu nhiên 64 ký tự hex hợp lệ.
  - `CsrfHelper::verifyToken($validToken)` $\rightarrow$ `true`.
  - `CsrfHelper::verifyToken("invalid_fake_token")` $\rightarrow$ `false`.

---

## 3. Kiểm Thử Tích Hợp (Integration Testing & ACID Transactions)

Đảm bảo sự phối hợp chính xác giữa Controllers, Models và CSDL MySQL:

### 3.1 Luồng Đặt Hàng & Quản Lý Tồn Kho (Order & Inventory Flow)
1. **Kiểm tra khóa dòng & tồn kho (Pessimistic Locking):**
   - Giả lập khách hàng đặt Size 41 với số lượng 2 đôi khi kho chỉ còn 1 đôi.
   - Hệ thống phải bắt lỗi `Exception: "Size 41 của sản phẩm không đủ số lượng trong kho"` và kích hoạt `rollBack()`.
2. **Kiểm tra tính toán giá phía Server (Anti-Price Tampering):**
   - Client giả lập gửi `price = 1000` thay vì giá gốc `1.850.000 ₫`.
   - `OrderModel` truy vấn giá từ CSDL bảng `products` để tính tổng tiền, không nhận giá do Client gửi lên.
3. **Kiểm tra hoàn kho khi hủy đơn (Restock on Cancellation):**
   - Khi đơn hàng chuyển trạng thái sang `cancelled` (Đã hủy), số lượng các biến thể trong `order_details` phải được cộng ngược lại vào `product_variants.stock`.

### 3.2 Kiểm tra Tương Thích Lược Đồ CSDL Thực Tế (Schema Compatibility)
- Đảm bảo các truy vấn SQL trong `models/ProductModel.php`, `models/AdminStockModel.php`, `models/OrderModel.php`, `models/AdminBrandModel.php` tương thích 100% với các cột hiện hữu của database:
  - Bảng `product_variants`: Chỉ truy vấn `id, product_id, size, stock` (Không gọi cột không tồn tại như `sku_variant`).
  - Bảng `brands`: Chỉ truy vấn `id, name, description, status` (Không gọi `b.logo`).
  - Bảng `orders`: Chỉ truy vấn `id, user_id, customer_name, phone, address, note, total_money, payment_method, status, created_at` (Không gọi `order_code`).

---

## 4. Kiểm Thử Bảo Mật & Thẩm Định Lỗ Hổng (Security Auditing)

Bộ quy tắc rà soát mã nguồn tự động phát hiện lỗ hổng:

| Lỗ hổng tiềm ẩn | Tiêu chuẩn kiểm tra mã nguồn (Code Audit) | Giải pháp bắt buộc |
| :--- | :--- | :--- |
| **SQL Injection (SQLi)** | Quét tất cả file `models/` tìm chuỗi nối trực tiếp `$sql .= " WHERE id = " . $id` hoặc string interpolation `"... $id"`. | 100% truy vấn phải dùng `prepare()` với dấu giữ chỗ `?` hoặc `:named`. |
| **Cross-Site Scripting (XSS)** | Quét tất cả file `views/` tìm các biến người dùng xuất trực tiếp `<?= $item['name'] ?>` mà không escape. | Phải dùng `<?= htmlspecialchars(...) ?>` hoặc `<?= SecurityHelper::e(...) ?>`. |
| **Cross-Site Request Forgery (CSRF)** | Kiểm tra các hành động POST làm thay đổi trạng thái (Thêm/Sửa/Xóa sản phẩm, Đặt hàng, Hủy đơn). | Bắt buộc chèn `<input type="hidden" name="csrf_token" value="<?= CsrfHelper::getToken() ?>">` và kiểm tra `CsrfHelper::verifyToken()`. |
| **Broken Access Control (RBAC)** | Kiểm tra các Controller trong thư mục `controllers/admin/`. | Bắt buộc gọi `AuthHelper::requireAdmin()` ngay tại hàm `__construct()` hoặc đầu mỗi action. |
| **Brute Force Protection** | Kiểm tra luồng đăng nhập `UserController::handleLogin()`. | Giới hạn số lần thử và thời gian delay khi nhập sai mật khẩu nhiều lần. |

---

## 5. Kiểm Thử Giao Diện & Design System (UI Component Testing)

Đảm bảo trải nghiệm thị giác và thương hiệu đồng bộ:

### 5.1 Nhất Quán Design Tokens (Ocean Blue Theme)
- File stylesheet chính [`assets/css/style.css`](file:///c:/xampp2/htdocs/shop_ban_hang/assets/css/style.css) phải chứa đầy đủ biến CSS:
  - `--ocean-primary: #0284c7;`
  - `--ocean-primary-hover: #0369a1;`
  - `--ocean-primary-dark: #07263e;`
  - `--ocean-navy: #0c4a6e;`
  - `--ocean-cyan: #06b6d4;`
  - `--ocean-ice: #f0f7fb;`
  - `--text-price: #e11d48;`
- Không sử dụng màu sắc lệch tông (ví dụ màu tím, hồng, vàng cam chói) cho các thành phần điều hướng và nút bấm chính.

### 5.2 Kiểm định Các Thành Phần Giao Diện (Components Audit)
- [`views/components/product_card.php`](file:///c:/xampp2/htdocs/shop_ban_hang/views/components/product_card.php):
  - Phải có huy hiệu loại sân (`TF`, `FG`, `IC`) với class `.badge-ground-ocean`.
  - Phải hiển thị giá gốc và giá khuyến mãi kèm tỷ lệ giảm `%`.
  - Phải có nút CTA xem chi tiết và mua hàng.
- [`views/components/cart_icon.php`](file:///c:/xampp2/htdocs/shop_ban_hang/views/components/cart_icon.php):
  - Chứa badge count với class `cart-badge-count` để JavaScript có thể cập nhật số lượng động.
- [`views/components/status_badge.php`](file:///c:/xampp2/htdocs/shop_ban_hang/views/components/status_badge.php):
  - Render đúng class màu theo trạng thái: `pending` (vàng cam), `processing` (xanh dương), `completed` (xanh lá), `cancelled` (đỏ xám).
- [`views/components/toast.php`](file:///c:/xampp2/htdocs/shop_ban_hang/views/components/toast.php):
  - Chuyển hóa flash messages `$_SESSION['success']` và `$_SESSION['error']` thành thông báo toast tự động đóng sau 4 giây.

---

## 6. Kiểm Thử Khế Ước API (API Contract Testing)

Đảm bảo sự đồng bộ giữa tài liệu Swagger và phản hồi JSON thực tế:

1. **Kiểm tra File Đặc Tả OpenAPI 3.0:**
   - File [`docs/openapi.json`](file:///c:/xampp2/htdocs/shop_ban_hang/docs/openapi.json) phải là JSON hợp lệ (valid syntax).
   - Chứa đầy đủ endpoints quan trọng: `/api/products`, `/api/products/{id}`, `/api/cart`, `/api/cart/add`, `/api/cart/update`, `/api/cart/remove`, `/api/orders`.
2. **Cấu Trúc Phong Bì Phản Hồi JSON Chuẩn (Standard JSON Envelope):**
   Mọi endpoint trả về từ `BaseApiController` hoặc `CartApiController` phải có định dạng:
   ```json
   {
     "success": true,
     "message": "Thông báo bằng tiếng Việt rõ ràng",
     "data": { ... },
     "cart_count": 3
   }
   ```
   Nếu có lỗi:
   ```json
   {
     "success": false,
     "message": "Mô tả nguyên nhân lỗi cụ thể"
   }
   ```

---

## 7. Bộ Công Cụ Kiểm Thử Tự Động (Automated CLI Test Runner)

Dự án trang bị sẵn công cụ kiểm thử tự động chạy trực tiếp qua dòng lệnh:

### 7.1 Lệnh Thực Thi
Chạy công cụ test runner bằng lệnh PHP CLI:
```bash
C:\xampp2\php\php.exe tests/run_tests.php
```

### 7.2 Cấu Trúc Các Ca Kiểm Thử (Test Suites Trong Runner)
Bộ kiểm thử thực thi tuần tự 6 bộ Test Suite độc lập:
- **Suite 1: Architecture & MVC Structure Test** (Kiểm tra sự tồn tại và tính hợp lệ của các Controllers, Models, Helpers, Views).
- **Suite 2: Database Schema & Column Compatibility Test** (Kiểm tra truy vấn an toàn, không gọi cột lạ).
- **Suite 3: Security & Vulnerability Audit Test** (Quét Prepared Statements, XSS escaping, CSRF tokens, Admin RBAC).
- **Suite 4: Business Logic & Data Integrity Test** (Kiểm thử tính toán giỏ hàng, tồn kho, mật khẩu bcrypt).
- **Suite 5: Frontend Design System & UI Components Test** (Kiểm tra mã màu Ocean Blue, badge components, Toast).
- **Suite 6: API Contract & OpenAPI Specification Test** (Parse cú pháp `docs/openapi.json`, kiểm tra endpoints).

### 7.3 Báo Cáo Kết Quả Mẫu (Sample Test Report)
```text
======================================================================
 SPORT KICKS AUTOMATED TEST RUNNER - SUITE EXECUTION
======================================================================

[SUITE 1] Architecture & MVC Structure
  [PASS] Config file exists and contains valid DB credentials
  [PASS] Core Helpers exist (SecurityHelper, CsrfHelper, AuthHelper)
  [PASS] Models exist (ProductModel, CartModel, OrderModel, AdminBrandModel)
  [PASS] Controllers exist (ProductController, CartController, OrderController)
  [PASS] UI Components exist (product_card, cart_icon, status_badge, toast)

[SUITE 2] Database Queries & Compatibility
  [PASS] ProductModel does not contain invalid column 'sku_variant'
  [PASS] AdminBrandModel does not contain invalid column 'b.logo'
  [PASS] OrderModel handles orders safely without 'order_code'
  [PASS] All model methods utilize PDO prepared statements

[SUITE 3] Security & Penetration Audit
  [PASS] SecurityHelper::e() correctly escapes HTML characters (< > " ' &)
  [PASS] CsrfHelper generates and verifies cryptographically secure tokens
  [PASS] Admin controllers enforce AuthHelper::requireAdmin()
  [PASS] Password hashing complies with PASSWORD_BCRYPT standard

[SUITE 4] Business Logic & Integrity
  [PASS] Cart item subtotal calculation is accurate
  [PASS] Size availability logic validates stock > 0
  [PASS] Quantity limits prevent negative values

[SUITE 5] Design System & Components
  [PASS] assets/css/style.css defines core --ocean-* color variables
  [PASS] product_card.php implements turf ground badges and price formatting
  [PASS] status_badge.php correctly maps order status colors

[SUITE 6] API Contracts & Swagger
  [PASS] docs/openapi.json is valid JSON format
  [PASS] OpenAPI spec declares paths for /cart and /products
  [PASS] Swagger UI index is accessible at docs/index.php and swagger/index.php

======================================================================
 RESULTS: 24/24 PASSED (100%) - 0 FAILED - DURATION: 42ms
 STATUS:  ALL QUALITY GATES PASSED! SYSTEM IS READY FOR PRODUCTION.
======================================================================
```

