---
name: frontend-developer
description: "Quy chuẩn thiết kế giao diện Atomic Design, Design System Ocean Blue, xử lý trạng thái AJAX/Fetch 3-states (Loading, Success, Error) và tối ưu trải nghiệm người dùng (UX/UI) cho Shop Giày Bóng Đá PHP MVC."
---

# Skill: Frontend Developer & UI Component Architect

Skill này định hình toàn bộ chuẩn mực phát triển giao diện phía Client (HTML5, Bootstrap 5, CSS3 Variables, Vanilla JavaScript, View Components) cho dự án **Shop Giày Bóng Đá Sport Kicks**:

---

## 1. Kiến Trúc Giao Diện Thành Phần (Atomic Design)

Giao diện cửa hàng giày bóng đá được module hóa tại thư mục `views/components/` theo mô hình Atomic Design:

- **Atoms (Nguyên tử):** Các thành phần nền tảng không thể chia nhỏ:
  - Nút bấm thương hiệu: `.btn-ocean-primary`, `.btn-ocean-outline`, `.search-btn-ocean`.
  - Huy hiệu phân loại: `.badge-discount-ocean`, `.badge-flash-ocean`, `.badge-ground-ocean` (TF, FG, IC).
  - Chip chọn Size giày: `.size-radio` với trạng thái active/disabled theo tồn kho thực tế.
  - Bộ đếm số lượng: Input số lượng kèm nút tăng/giảm (+ / -).

- **Molecules (Phân tử):** Tổ hợp các nguyên tử tạo thành khối tính năng độc lập:
  - [`views/components/product_card.php`](file:///c:/xampp2/htdocs/shop_ban_hang/views/components/product_card.php): Thẻ hiển thị sản phẩm giày bóng đá hoàn chỉnh (ảnh giày hiệu ứng zoom xoay, tên hãng, loại sân TF/FG/IC, giá bán, giá gạch, % giảm, size khả dụng, nút xem chi tiết).
  - [`views/components/cart_icon.php`](file:///c:/xampp2/htdocs/shop_ban_hang/views/components/cart_icon.php): Biểu tượng giỏ hàng với dynamic badge counter tự động nảy số khi gọi AJAX.
  - [`views/components/status_badge.php`](file:///c:/xampp2/htdocs/shop_ban_hang/views/components/status_badge.php): Nhãn trạng thái đơn hàng (Chờ xử lý, Đang giao, Hoàn thành, Đã hủy).
  - [`views/components/toast.php`](file:///c:/xampp2/htdocs/shop_ban_hang/views/components/toast.php): Cầu nối tự động biến Flash Session của PHP thành thông báo Toast nổi.

- **Organisms (Sinh vật):** Khối tổ hợp lớn tạo nên bố cục trang:
  - Header & Navigation (`views/layouts/header.php`): Thanh điều hướng Ocean Deep cố định kèm tìm kiếm và menu người dùng.
  - Hero Banner (`views/home/index.php`): Băng rôn bứt tốc đam mê với bóng cầu thủ và giày nổi bật.
  - Bảng giỏ hàng (`views/cart/index.php`): Bảng danh sách giày đã chọn kèm checkbox tính tổng tiền realtime.
  - Khung thanh toán (`views/cart/checkout.php`): Phân chia rõ ràng thông tin người nhận, phương thức COD/Banking QR và tóm tắt đơn.
  - Sidebar quản trị (`views/admin/layout/header.php`): Cây điều hướng bảng điều khiển admin đồng bộ tông màu biển sâu.

- **Templates & Pages:** Giao diện trang hoàn chỉnh (`home/index.php`, `products/list.php`, `products/detail.php`, `cart/index.php`, `admin/dashboard.php`).

---

## 2. Design System & Bảng Màu Nhất Quán (Ocean Blue Theme)

Toàn bộ dự án tuân thủ nghiêm ngặt hệ thống Design Tokens định nghĩa tại [`assets/css/style.css`](file:///c:/xampp2/htdocs/shop_ban_hang/assets/css/style.css):

| Token | Mã màu Hex | Ứng dụng trong Shop Giày |
| :--- | :--- | :--- |
| `--ocean-primary` | `#0284c7` | Nút hành động chính (Mua ngay, Thêm giỏ), liên kết active |
| `--ocean-primary-hover`| `#0369a1` | Hiệu ứng di chuột (hover state) cho nút và liên kết |
| `--ocean-primary-dark` | `#07263e` | Nền Header Navbar, Hero banner, viền thương hiệu |
| `--ocean-navy` | `#0c4a6e` | Tiêu đề bảng giỏ hàng, Sidebar Admin CP |
| `--ocean-cyan` | `#06b6d4` | Điểm nhấn chữ logo, viền sáng, huy hiệu loại sân TF |
| `--ocean-ice` | `#f0f7fb` | Màu nền trang web, khối tóm tắt đơn hàng |
| `--text-price` | `#e11d48` | Màu đỏ hiển thị giá bán chính thức của giày |
| `--accent-gold` | `#f59e0b` | Màu tia sét Flash Sale, đánh giá sao đánh giá giày |

### Chuẩn Hóa Văn Bản Tiếng Việt (Vietnamese Sports Cleats Copywriting)
- **Thuật ngữ mặt sân chuẩn**: "Sân cỏ nhân tạo (TF)", "Sân cỏ tự nhiên (FG/AG)", "Sân Futsal trong nhà (IC)".
- **Cam kết dịch vụ**: "Bảo hành chính hãng 100% - Đền gấp 10 nếu phát hiện fake", "Đổi size miễn phí trong 7 ngày", "Tặng kèm vớ chống trượt".
- **Bảng đo size chân**: Tích hợp Modal hướng dẫn đo chiều dài bàn chân (cm) sang size chuẩn EUR (39, 40, 41, 42, 43, 44) cho cầu thủ chân thon hoặc chân bè.

---

## 3. Quy Chuẩn Tích Hợp AJAX / Fetch (Xử Lý 3 Trạng Thái - 3 States Handling)

Tất cả các thao tác tương tác API trên giao diện (Thêm giỏ hàng, cập nhật số lượng, xóa sản phẩm) bắt buộc ủy quyền qua đối tượng `ShopAPI` trong [`assets/js/api-client.js`](file:///c:/xampp2/htdocs/shop_ban_hang/assets/js/api-client.js) với đủ 3 trạng thái:

```javascript
// Chuẩn mực xử lý 3 trạng thái trong ShopAPI
async addToCart(productId, size, quantity = 1, triggerBtn = null) {
    // 1. LOADING STATE
    let originalContent = '';
    if (triggerBtn) {
        originalContent = triggerBtn.innerHTML;
        triggerBtn.disabled = true; // Chống spam double-click
        triggerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang thêm...';
    }

    try {
        const response = await fetch('index.php?controller=cart&action=add', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: formData
        });
        const data = await response.json();

        if (response.ok && data.status === 200) {
            // 2. SUCCESS STATE
            this.updateHeaderCartCount(data.cart_count); // Bump animation
            this.showToast(data.message, 'success', 'Giỏ Hàng Sport Kicks');
            if (triggerBtn) {
                triggerBtn.innerHTML = '<i class="fas fa-check me-1"></i> Đã thêm vào giỏ!';
            }
        } else {
            // 3. ERROR STATE (Business error)
            this.showToast(data.message || 'Lỗi kho hoặc hết hàng!', 'error', 'Thông báo');
        }
    } catch (error) {
        // 3. ERROR STATE (Network / Server error)
        this.showToast('Mất kết nối máy chủ hoặc mạng gián đoạn!', 'error', 'Lỗi mạng');
    } finally {
        setTimeout(() => {
            if (triggerBtn) {
                triggerBtn.innerHTML = originalContent;
                triggerBtn.disabled = false;
            }
        }, 1500);
    }
}
```

---

## 4. Bảo Mật & Tối Ưu Media Phía Client

1. **Chống XSS (Cross-Site Scripting):** Mọi trường dữ liệu xuất ra HTML bắt buộc dùng `SecurityHelper::e()` hoặc `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`.
2. **Kiểm duyệt tải ảnh Client (Image Uploader Validator):**
   - Định dạng hợp lệ: `image/jpeg`, `image/png`, `image/webp`.
   - Dung lượng tối đa: `<= 5MB`.
   - Hiển thị bản xem trước tức thời (`FileReader.readAsDataURL`) trước khi gửi lên server.
3. **Thanh toán an toàn:** Không bao giờ cho phép nhập trực tiếp hoặc sửa đổi tổng tiền `total_money` ở client; chỉ hiển thị tạm tính phục vụ trải nghiệm người dùng.

