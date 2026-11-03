/**
 * Sport Kicks - Frontend API Integration Service
 * Xử lý toàn diện các trạng thái mạng (Loading, Error, Success), Toast Feedback và AJAX
 */

const ShopAPI = {
    csrfToken: '',

    init() {
        // Lấy CSRF Token từ thẻ meta hoặc input ẩn
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfInput = document.querySelector('input[name="csrf_token"]');
        if (csrfMeta) {
            this.csrfToken = csrfMeta.getAttribute('content');
        } else if (csrfInput) {
            this.csrfToken = csrfInput.value;
        }

        this.ensureToastContainer();
    },

    ensureToastContainer() {
        if (!document.getElementById('toast-container')) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }
    },

    /**
     * Hiển thị thông báo Toast đẹp mắt, phi chặn (Non-blocking)
     */
    showToast(message, type = 'success', title = null, duration = 3500) {
        this.ensureToastContainer();
        const container = document.getElementById('toast-container');

        const toast = document.createElement('div');
        toast.className = `toast-item toast-${type}`;

        let iconClass = 'fa-check-circle';
        let defaultTitle = 'Thành công';

        if (type === 'error') {
            iconClass = 'fa-exclamation-circle';
            defaultTitle = 'Có lỗi xảy ra';
        } else if (type === 'warning') {
            iconClass = 'fa-exclamation-triangle';
            defaultTitle = 'Cảnh báo';
        } else if (type === 'info') {
            iconClass = 'fa-info-circle';
            defaultTitle = 'Thông báo';
        }

        toast.innerHTML = `
            <i class="fas ${iconClass} toast-icon"></i>
            <div class="toast-content">
                <div class="toast-title">${title || defaultTitle}</div>
                <div class="toast-desc">${message}</div>
            </div>
            <button type="button" class="toast-close" onclick="this.parentElement.remove()">&times;</button>
        `;

        container.appendChild(toast);

        // Hiệu ứng animate vào
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        // Tự động gỡ sau duration ms
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toast.parentElement) toast.remove();
            }, 400);
        }, duration);
    },

    /**
     * Thêm sản phẩm vào giỏ hàng bằng AJAX (Không tải lại trang)
     */
    async addToCart(productId, size, quantity = 1, triggerBtn = null) {
        if (!productId || !size) {
            this.showToast('Vui lòng chọn size giày phù hợp trước khi thêm!', 'warning', 'Chưa chọn size');
            return false;
        }

        let originalContent = '';
        if (triggerBtn) {
            originalContent = triggerBtn.innerHTML;
            triggerBtn.disabled = true;
            triggerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang thêm...';
        }

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('size', size);
        formData.append('quantity', quantity);
        formData.append('action_type', 'add');
        if (this.csrfToken) {
            formData.append('csrf_token', this.csrfToken);
        }

        try {
            const response = await fetch('index.php?controller=cart&action=add', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.status === 200) {
                // Cập nhật số lượng giỏ hàng trên Header Navbar
                this.updateHeaderCartCount(data.cart_count);

                this.showToast(data.message, 'success', 'Giỏ Hàng Sport Kicks');

                if (triggerBtn) {
                    triggerBtn.innerHTML = '<i class="fas fa-check me-1"></i> Đã thêm vào giỏ!';
                    setTimeout(() => {
                        triggerBtn.innerHTML = originalContent;
                        triggerBtn.disabled = false;
                    }, 1500);
                }
                return true;
            } else {
                this.showToast(data.message || 'Không thể thêm vào giỏ hàng', 'error', 'Thông báo kho');
                if (triggerBtn) {
                    triggerBtn.innerHTML = originalContent;
                    triggerBtn.disabled = false;
                }
                return false;
            }
        } catch (error) {
            console.error('Lỗi API Cart:', error);
            this.showToast('Mất kết nối mạng hoặc máy chủ không phản hồi. Vui lòng thử lại!', 'error', 'Lỗi mạng');
            if (triggerBtn) {
                triggerBtn.innerHTML = originalContent;
                triggerBtn.disabled = false;
            }
            return false;
        }
    },

    /**
     * Cập nhật số đếm giỏ hàng trên Header với hiệu ứng Bump
     */
    updateHeaderCartCount(count) {
        const badge = document.getElementById('navbar-cart-count');
        if (badge) {
            badge.innerText = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
            badge.classList.add('bump');
            setTimeout(() => {
                badge.classList.remove('bump');
            }, 300);
        }
    },

    /**
     * Cập nhật số lượng giỏ hàng tức thì (Debounced)
     */
    async updateCartItem(key, quantity, rowEl = null) {
        if (quantity < 1) return;

        const formData = new FormData();
        formData.append('key', key);
        formData.append('quantity', quantity);
        if (this.csrfToken) formData.append('csrf_token', this.csrfToken);

        try {
            const res = await fetch('index.php?controller=cart&action=update', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await res.json();

            if (res.ok && data.status === 200) {
                this.updateHeaderCartCount(data.cart_count);
                
                // Cập nhật thành tiền của dòng
                if (rowEl) {
                    const subtotalEl = rowEl.querySelector('.item-subtotal');
                    if (subtotalEl) {
                        subtotalEl.innerText = new Intl.NumberFormat('vi-VN').format(data.item_total) + ' ₫';
                    }
                }
                
                // Cập nhật tổng thanh toán nếu có hàm tính toán
                if (typeof updateTotal === 'function') {
                    updateTotal();
                }
                this.showToast('Đã cập nhật số lượng', 'info', 'Giỏ hàng', 1500);
            } else {
                this.showToast(data.message || 'Lỗi cập nhật số lượng', 'error');
            }
        } catch (e) {
            console.error(e);
            this.showToast('Không thể kết nối đến máy chủ!', 'error');
        }
    },

    /**
     * Client-side Image Uploader với Preview và kiểm tra dung lượng
     */
    bindImageUploader(fileInput, previewImg, maxSizeMb = 5) {
        if (!fileInput || !previewImg) return;

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            // Kiểm tra định dạng
            const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                ShopAPI.showToast('Chỉ chấp nhận file ảnh định dạng JPG, PNG hoặc WEBP!', 'warning', 'Định dạng sai');
                fileInput.value = '';
                return;
            }

            // Kiểm tra dung lượng
            if (file.size > maxSizeMb * 1024 * 1024) {
                ShopAPI.showToast(`Dung lượng ảnh tối đa cho phép là ${maxSizeMb}MB!`, 'warning', 'Ảnh quá lớn');
                fileInput.value = '';
                return;
            }

            // Hiển thị preview ngay lập tức
            const reader = new FileReader();
            reader.onload = (event) => {
                previewImg.src = event.target.result;
                previewImg.style.display = 'block';
                ShopAPI.showToast('Đã tải ảnh lên bản xem trước!', 'success', 'Tải ảnh', 2000);
            };
            reader.readAsDataURL(file);
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    ShopAPI.init();
});

