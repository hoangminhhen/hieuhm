<?php require_once 'views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #07263e 0%, #0284c7 100%);">
                    <i class="fas fa-futbol fa-2x mb-2 text-info"></i>
                    <h4 class="fw-bold mb-0 text-uppercase">Đăng Nhập Thành Viên</h4>
                    <small class="text-light opacity-75">Hệ thống Sport Kicks Store</small>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if(isset($success)): ?>
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?controller=user&action=login">
                        <?= CsrfHelper::csrfField() ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Địa chỉ Email (*)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope text-primary"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Mật khẩu (*)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-lock text-primary"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-ocean-primary w-100 py-3 fw-bold rounded-pill shadow-sm">
                            <i class="fas fa-sign-in-alt me-2"></i> ĐĂNG NHẬP
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="index.php?controller=user&action=forgotPassword" class="small text-muted text-decoration-none">
                            <i class="fas fa-key me-1"></i> Quên mật khẩu?
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <span class="text-muted small">Chưa có tài khoản Sport Kicks?</span><br>
                        <a href="index.php?controller=user&action=register" class="fw-bold text-primary text-decoration-none">
                            <i class="fas fa-user-plus me-1"></i> Tạo tài khoản mới ngay
                        </a>
                    </div>

                    <!-- Fast Demo Credentials Box -->
                    <div class="mt-4 p-3 bg-light rounded-3 border small">
                        <div class="fw-bold text-primary mb-1"><i class="fas fa-info-circle me-1"></i> Tài khoản kiểm thử nhanh:</div>
                        <div class="text-muted">• <strong>Admin:</strong> admin@sportkicks.vn | Pass: <code>admin123</code></div>
                        <div class="text-muted">• <strong>Khách:</strong> khachhang@sportkicks.vn | Pass: <code>123456</code></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
