git remote set-url origin https://github.com/TEN_TAI_KHOAN/TEN_REPO.gitgi---
name: backend-developer
description: "Quy chuẩn kiến trúc PHP MVC thuần, bảo mật cơ sở dữ liệu PDO Prepared Statements, ACID Transactions chống âm kho, quản lý Authentication/RBAC, CSRF Protection và chuẩn hóa OpenAPI/Swagger 3.0 Contract."
---

# Skill: Backend Developer & Database Architect

Skill này định hình toàn bộ chuẩn mực phát triển nghiệp vụ phía Server (PHP 8.x, MVC Architecture, MySQL Database, Security & API Endpoints) cho dự án **Shop Giày Bóng Đá Sport Kicks**:

---

## 1. Phân Tầng Kiến Trúc MVC Chuẩn (Separation of Concerns)

Cấu trúc dự án phân tách rõ ràng trách nhiệm của từng tầng:

```text
c:/xampp2/htdocs/shop_ban_hang/
├── config/        --> Cấu hình URL, Database Credentials, PDO Options
├── helpers/       --> SecurityHelper, CsrfHelper, AuthHelper (Middleware logic)
├── models/        --> Tương tác CSDL qua PDO, thực thi logic nghiệp vụ và Transactions
├── controllers/   --> Điều phối luồng Web thông thường (Render View)
│   └── api/       --> Xử lý REST API endpoints (Trả JSON theo BaseApiController)
├── views/         --> Giao diện người dùng & Reusable UI Components
├── database/      --> Lược đồ 3NF, seed data và migration runner
└── docs/          --> OpenAPI 3.0 JSON specification & Interactive Swagger UI
```

### 1.1 Controller Layer (`controllers/` & `controllers/api/`)
- Nhận dữ liệu đầu vào từ `$_POST`, `$_GET` hoặc `php://input` (JSON payload).
- Validate định dạng dữ liệu (bắt buộc, email hợp lệ, độ dài mật khẩu >= 6 ký tự).
- Xác thực CSRF Token đối với tất cả form POST nhạy cảm qua `CsrfHelper::verifyToken()`.
- Phân quyền người dùng và quản trị viên qua `AuthHelper::requireAdmin()` hoặc `AuthHelper::requireLogin()`.
- **CẤM:** Tuyệt đối không viết câu lệnh SQL trực tiếp trong Controller; toàn bộ truy vấn phải thông qua Model.

### 1.2 Model Layer (`models/`)
- Kết nối thông qua class `Database` với các tùy chọn PDO bảo mật cao:
  - `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`
  - `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`
  - `PDO::ATTR_EMULATE_PREPARES => false` (Ngăn chặn SQL Injection qua cơ chế native prepare).
- 100% câu lệnh SQL bắt buộc sử dụng **Prepared Statements** với dấu giữ chỗ `?`.

---

## 2. Giao Dịch ACID & Quản Lý Kho Giày An Toàn (Transactions & Stock)

### 2.1 Luồng Đặt Hàng Chống Âm Kho (Atomic Checkout Flow)
Triển khai trong [`models/OrderModel.php`](file:///c:/xampp2/htdocs/shop_ban_hang/models/OrderModel.php):

1. **Khởi tạo Transaction:** `$this->conn->beginTransaction()`.
2. **Kiểm tra tồn kho & Khóa dòng (Pessimistic Locking):**
   ```sql
   SELECT stock FROM product_variants WHERE product_id = ? AND size = ? FOR UPDATE
   ```
   Nếu `stock < quantity` yêu cầu, văng ngoại lệ `Exception` và hủy toàn bộ thao tác (`rollBack`).
3. **Tính toán giá trị đơn hàng từ CSDL (Chống Price Tampering):**
   Tuyệt đối không sử dụng giá tiền `$_POST['total_money']` do Client gửi lên. Server truy vấn trực tiếp giá gốc hoặc giá Flash Sale từ bảng `products`:
   $$\text{Subtotal} = \text{Unit Price}_{\text{DB}} \times \text{Quantity}$$
   $$\text{Total Money} = \sum \text{Subtotal}$$
4. **Trừ kho nguyên tử:**
   ```sql
   UPDATE product_variants SET stock = stock - ? WHERE product_id = ? AND size = ? AND stock >= ?
   ```
5. **Xác nhận giao dịch:** `$this->conn->commit()`.

### 2.2 Hoàn Kho Tự Động Khi Hủy Đơn (Atomic Restock)
Khi khách hàng hoặc quản trị viên hủy đơn hàng ở trạng thái `pending`, hệ thống tự động duyệt qua `order_details` và cộng hoàn lại số lượng giày vào đúng biến thể size:
```sql
UPDATE product_variants SET stock = stock + ? WHERE product_id = ? AND size = ?
```

---

## 3. Xác Thực, Phân Quyền & Bảo Mật Hệ Thống (Auth & OWASP Security)

1. **Bảo mật mật khẩu:**
   - Mã hóa mật khẩu 1 chiều bằng thuật toán an toàn: `password_hash($password, PASSWORD_BCRYPT)`.
   - Kiểm tra đăng nhập bằng: `password_verify($password, $user['password'])`.
   - Khóa tài khoản: Kiểm tra cột `status === 'locked'` để từ chối đăng nhập với thông báo rõ ràng.
2. **Bảo vệ chống CSRF (Cross-Site Request Forgery):**
   - Tạo token ngẫu nhiên mã hóa an toàn qua `bin2hex(random_bytes(32))` lưu trong `$_SESSION['csrf_token']`.
   - Nhúng token vào form qua `CsrfHelper::csrfField()` và kiểm tra qua `CsrfHelper::verifyToken()`.
3. **Bảo vệ chống SQL Injection & XSS:**
   - Ép kiểu số nguyên rõ ràng `(int)$id`, `(int)$offset`, `(int)$limit`.
   - Bọc toàn bộ biến đầu ra trên giao diện bằng hàm `SecurityHelper::e()`.
4. **Phân quyền vai trò (Role-Based Access Control):**
   - 100% Controller trong khu vực quản trị (`AdminController`, `AdminProductController`, `AdminOrderController`, `AdminStockController`, `AdminBrandController`, `AdminUserController`) bắt buộc gọi `AuthHelper::requireAdmin()` trong hàm khởi tạo `__construct()`.

---

## 4. Chuẩn Hóa Hợp Đồng API & Swagger / OpenAPI 3.0

- Kế thừa [`controllers/api/BaseApiController.php`](file:///c:/xampp2/htdocs/shop_ban_hang/controllers/api/BaseApiController.php) để chuẩn hóa cấu trúc JSON Envelope:
  ```json
  {
    "status": 200,
    "success": true,
    "message": "Thành công",
    "data": {},
    "meta": {}
  }
  ```
- Toàn bộ danh mục API được định nghĩa chuẩn xác tại [`docs/openapi.json`](file:///c:/xampp2/htdocs/shop_ban_hang/docs/openapi.json) và tích hợp giao diện chạy thử **Swagger UI** tại [`docs/index.php`](file:///c:/xampp2/htdocs/shop_ban_hang/docs/index.php).
- Hỗ trợ cơ chế xác thực API bằng `Authorization: Bearer <token>` thông qua bảng `user_tokens`.

