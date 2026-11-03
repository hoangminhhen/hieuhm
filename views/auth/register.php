<?php require_once 'views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #07263e 0%, #0284c7 100%);">
                    <i class="fas fa-user-plus fa-2x mb-2 text-info"></i>
                    <h4 class="fw-bold mb-0 text-uppercase">Đăng Ký Thành Viên</h4>
                    <small class="text-light opacity-75">Tham gia cộng đồng bóng đá Sport Kicks</small>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?controller=user&action=register">
                        <?= CsrfHelper::csrfField() ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Họ và tên (*)</label>
                            <input type="text" name="fullname" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Địa chỉ Email (*)</label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Số điện thoại (*)</label>
                                <input type="tel" name="phone" class="form-control" placeholder="0901234567" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Mật khẩu (tối thiểu 6 ký tự) (*)</label>
                                <input type="password" name="password" class="form-control" minlength="6" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Nhập lại mật khẩu (*)</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Địa chỉ giao hàng mặc định</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Số nhà, tên đường, quận/huyện..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-ocean-primary w-100 py-3 fw-bold rounded-pill shadow-sm">
                            <i class="fas fa-check-circle me-2"></i> HOÀN TẤT ĐĂNG KÝ
                        </button>
                    </form>

                    <hr class="my-4">
                    
                    <div class="text-center">
                        <span class="text-muted small">Đã có tài khoản Sport Kicks?</span><br>
                        <a href="index.php?controller=user&action=login" class="fw-bold text-primary text-decoration-none">
                            <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
