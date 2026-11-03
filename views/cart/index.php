<?php require_once 'views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
        <h2 class="text-uppercase fw-bold text-primary mb-0">
            <i class="fas fa-shopping-cart me-2"></i> Giỏ Hàng Của Bạn
        </h2>
        <span class="badge bg-light text-muted border ms-3">
            <?= count($cart) ?> mục sản phẩm
        </span>
    </div>

    <?php if (empty($cart)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="fas fa-shopping-bag fa-4x text-muted mb-3 opacity-50"></i>
                <h4 class="fw-bold text-dark">Giỏ hàng của bạn đang trống!</h4>
                <p class="text-muted">Chưa có đôi giày bóng đá nào được thêm vào giỏ.</p>
                <a href="index.php?controller=product&action=list" class="btn btn-ocean-primary btn-lg rounded-pill px-4 mt-2">
                    <i class="fas fa-arrow-left me-2"></i> Khám phá các mẫu giày hot
                </a>
            </div>
        </div>
    <?php else: ?>
        
        <form action="index.php?controller=cart&action=checkout" method="POST" id="cartForm">
            <?= CsrfHelper::csrfField() ?>
            
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-cart-ocean align-middle mb-0 text-center">
                                <thead>
                                    <tr>
                                        <th style="width: 48px;">
                                            <input type="checkbox" id="checkAll" class="form-check-input" onclick="toggleAll(this)">
                                        </th>
                                        <th class="text-start">Sản phẩm</th>
                                        <th>Đơn giá</th>
                                        <th style="width: 140px;">Số lượng</th>
                                        <th>Thành tiền</th>
                                        <th style="width: 60px;">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart as $key => $item): ?>
                                        <tr data-key="<?= $key ?>">
                                            <td>
                                                <input type="checkbox" name="selected_items[]" 
                                                       value="<?= $key ?>" 
                                                       class="form-check-input item-check"
                                                       data-price="<?= $item['price'] * $item['quantity'] ?>"
                                                       onclick="updateTotal()">
                                            </td>
                                            
                                            <td class="text-start">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= htmlspecialchars($item['image']) ?>" 
                                                         class="rounded-3 me-3 border" 
                                                         style="width: 65px; height: 65px; object-fit: contain;"
                                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                                         onerror="this.src='https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop&q=80'">
                                                    <div>
                                                        <a href="index.php?controller=product&action=detail&id=<?= $item['id'] ?>" class="fw-bold text-dark text-decoration-none">
                                                            <?= htmlspecialchars($item['name']) ?>
                                                        </a>
                                                        <div class="small text-muted mt-1">
                                                            <span class="badge bg-light text-primary border">Size: <?= htmlspecialchars($item['size']) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-semibold">
                                                <?= number_format($item['price'], 0, ',', '.') ?> ₫
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="changeRowQty('<?= $key ?>', -1)">-</button>
                                                    <input type="number" 
                                                           id="qty_<?= $key ?>" 
                                                           class="form-control text-center fw-bold" 
                                                           value="<?= $item['quantity'] ?>" 
                                                           min="1" max="50"
                                                           onchange="ShopAPI.updateCartItem('<?= $key ?>', this.value, this.closest('tr'))">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="changeRowQty('<?= $key ?>', 1)">+</button>
                                                </div>
                                            </td>
                                            <td class="fw-bold text-danger item-subtotal">
                                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> ₫
                                            </td>
                                            <td>
                                                <a href="index.php?controller=cart&action=delete&id=<?= $key ?>" 
                                                   class="btn btn-sm btn-outline-danger border-0 rounded-circle" 
                                                   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?');"
                                                   title="Xóa khỏi giỏ">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Box -->
                <div class="col-lg-4">
                    <div class="order-summary-card">
                        <div class="card-header py-3">
                            <i class="fas fa-file-invoice me-2"></i> Tóm Tắt Thanh Toán
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Sản phẩm đã chọn:</span>
                                <span class="fw-bold" id="selectedCount">0 sản phẩm</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Tạm tính:</span>
                                <span class="fw-bold text-danger fs-5" id="totalMoney">0 ₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted">Phí giao hàng:</span>
                                <span class="text-success fw-bold">Miễn phí giao hàng</span>
                            </div>

                            <button type="submit" id="btnCheckout" class="btn btn-danger btn-lg w-100 fw-bold py-3 shadow" disabled>
                                <i class="fas fa-lock me-2"></i> TIẾN HÀNH THANH TOÁN
                            </button>

                            <div class="mt-3 text-center">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt text-success me-1"></i> Thanh toán an toàn & bảo mật 100%
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    <?php endif; ?>
</div>

<script>
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
    }

    function toggleAll(master) {
        const checkboxes = document.querySelectorAll('.item-check');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateTotal();
    }

    function updateTotal() {
        const checkboxes = document.querySelectorAll('.item-check:checked');
        let total = 0;
        let count = 0;

        checkboxes.forEach(cb => {
            total += parseFloat(cb.getAttribute('data-price')) || 0;
            count++;
        });

        document.getElementById('selectedCount').innerText = count + ' sản phẩm';
        document.getElementById('totalMoney').innerText = formatCurrency(total);
        document.getElementById('btnCheckout').disabled = (count === 0);

        // Cập nhật trạng thái Master checkbox
        const allCheckboxes = document.querySelectorAll('.item-check');
        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.checked = (allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length);
        }
    }

    function changeRowQty(key, delta) {
        const input = document.getElementById('qty_' + key);
        if (!input) return;
        let val = parseInt(input.value) || 1;
        val += delta;
        if (val < 1) val = 1;
        input.value = val;
        
        const row = input.closest('tr');
        ShopAPI.updateCartItem(key, val, row);
    }
</script>

<?php require_once 'views/layouts/footer.php'; ?>
