<?php require_once 'views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="text-center mb-5">
        <span class="badge bg-primary text-uppercase px-3 py-2 rounded-pill fw-bold mb-2">Thanh Toán An Toàn</span>
        <h2 class="fw-bold text-uppercase text-dark">Xác Nhận & Đặt Hàng</h2>
        <p class="text-muted small">Vui lòng kiểm tra lại thông tin nhận hàng và danh sách giày bóng đá</p>
    </div>
    
    <div class="row g-4">
        <!-- Customer & Delivery Form -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-primary text-white fw-bold py-3">
                    <i class="fas fa-map-marker-alt me-2"></i> 1. Thông Tin Người Nhận Hàng
                </div>
                <div class="card-body p-4">
                    <form action="index.php?controller=cart&action=processCheckout" method="POST">
                        <?= CsrfHelper::csrfField() ?>
                        <input type="hidden" name="order_type" value="<?= isset($orderType) ? htmlspecialchars($orderType) : 'cart' ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Họ và tên người nhận (*)</label>
                            <input type="text" name="fullname" class="form-control form-control-lg fs-6" 
                                   value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Số điện thoại liên hệ (*)</label>
                            <input type="tel" name="phone" class="form-control form-control-lg fs-6" 
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                                   placeholder="Ví dụ: 0901234567" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Địa chỉ nhận hàng chi tiết (*)</label>
                            <textarea name="address" class="form-control" rows="3" 
                                      placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..." required><?= htmlspecialchars($user['address'] ?? '') ?></textarea> 
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Ghi chú giao hàng (Tùy chọn)</label>
                            <textarea name="note" class="form-control" rows="2" 
                                      placeholder="Ví dụ: Giao hàng vào buổi chiều, gọi trước khi đến..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-3">
                                <i class="fas fa-wallet me-2 text-primary"></i> Phương Thức Thanh Toán
                            </label>
                            
                            <div class="form-check p-3 border rounded-3 mb-2 bg-light">
                                <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="cod" value="COD" checked>
                                <label class="form-check-label fw-bold text-dark d-block cursor-pointer" for="cod">
                                    <i class="fas fa-money-bill-wave text-success me-2"></i> Thanh toán tiền mặt khi nhận hàng (COD)
                                    <span class="d-block text-muted small fw-normal mt-1">Được kiểm tra hàng trước khi thanh toán cho shipper</span>
                                </label>
                            </div>

                            <div class="form-check p-3 border rounded-3 bg-light">
                                <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="banking" value="BANKING">
                                <label class="form-check-label fw-bold text-dark d-block cursor-pointer" for="banking">
                                    <i class="fas fa-university text-primary me-2"></i> Chuyển khoản ngân hàng 24/7 (VietQR Code)
                                    <span class="d-block text-muted small fw-normal mt-1">Xác nhận đơn tức thì, an toàn và tiện lợi</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger btn-lg w-100 fw-bold py-3 shadow">
                            <i class="fas fa-check-circle me-2"></i> XÁC NHẬN ĐẶT HÀNG NGAY
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Summary Column -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-dark text-white fw-bold py-3">
                    <i class="fas fa-shopping-bag me-2"></i> 2. Đơn Hàng Của Bạn
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush mb-4">
                        <?php if(!empty($cart)): ?>
                            <?php foreach($cart as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="<?= htmlspecialchars($item['image']) ?>" 
                                             class="rounded-3 me-3 border" 
                                             style="width: 55px; height: 55px; object-fit: contain;"
                                             onerror="this.src='https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop&q=80'">
                                        
                                        <div>
                                            <h6 class="my-0 small fw-bold text-dark"><?= htmlspecialchars($item['name']) ?></h6>
                                            <div class="small text-muted mt-1">
                                                Size: <strong><?= htmlspecialchars($item['size']) ?></strong> | SL: <strong><?= $item['quantity'] ?> đôi</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="fw-bold text-dark">
                                        <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> ₫
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <li class="list-group-item d-flex justify-content-between px-0 py-2 border-0 small text-muted">
                            <span>Tạm tính hàng hóa:</span>
                            <span><?= number_format($totalPrice, 0, ',', '.') ?> ₫</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between px-0 py-2 border-0 small text-muted">
                            <span>Phí vận chuyển toàn quốc:</span>
                            <span class="text-success fw-bold">MIỄN PHÍ</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between px-0 py-3 mt-2 border-top fw-extrabold text-dark fs-5">
                            <span>Tổng thanh toán:</span>
                            <span class="text-danger"><?= number_format($totalPrice, 0, ',', '.') ?> ₫</span>
                        </li>
                    </ul>
                    
                    <div class="p-3 rounded-3 bg-light border small text-muted">
                        <i class="fas fa-truck text-primary me-2"></i> Đơn hàng sẽ được nhân viên Sport Kicks gọi điện xác nhận trong vòng 15 phút.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
