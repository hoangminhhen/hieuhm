<?php require_once 'views/layouts/header.php'; ?>

<!-- Hero Banner (Ocean Blue Edition) -->
<div class="container my-4">
    <div class="hero-ocean p-5 text-white">
        <div class="row align-items-center py-4">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm">
                    <i class="fas fa-bolt me-1"></i> BỘ SƯU TẬP OCEAN BLUE 2026
                </span>
                <h1 class="display-4 fw-extrabold mb-3 lh-sm">
                    BỨT TỐC ĐAM MÊ <br>LÀM CHỦ MỌI MẶT SÂN
                </h1>
                <p class="lead mb-4 opacity-90">
                    Trang bị những đôi giày đá bóng chính hãng tiên tiến nhất: đệm Air Zoom đàn hồi, da Kangaroo êm ái, bám sân nhân tạo cực đỉnh.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="index.php?controller=product&action=list" class="btn btn-warning btn-lg fw-bold px-4 rounded-pill shadow">
                        <i class="fas fa-shopping-bag me-2"></i> MUA NGAY
                    </a>
                    <a href="index.php?controller=product&action=list&category=1" class="btn btn-outline-light btn-lg fw-bold px-4 rounded-pill">
                        Giày Cỏ Nhân Tạo (TF) <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=700&auto=format&fit=crop&q=80" 
                     alt="Nike Mercurial Ocean" 
                     class="img-fluid drop-shadow" 
                     style="max-height: 320px; filter: drop-shadow(0 20px 30px rgba(0,0,0,0.5)); transform: rotate(-15deg);">
            </div>
        </div>
    </div>
</div>

<div class="container py-3">
    <!-- Value Propositions -->
    <div class="row g-3 mb-5">
        <div class="col-md-3 col-6">
            <div class="feature-box-ocean text-center h-100">
                <i class="fas fa-shipping-fast fa-2x mb-2"></i>
                <h6 class="fw-bold mb-1">Giao Hàng Siêu Tốc</h6>
                <p class="small text-muted mb-0">Nhận hàng toàn quốc trong 2-3 ngày</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="feature-box-ocean text-center h-100">
                <i class="fas fa-certificate fa-2x mb-2 text-info"></i>
                <h6 class="fw-bold mb-1">Cam Kết 100% Chính Hãng</h6>
                <p class="small text-muted mb-0">Đền gấp 10 lần nếu phát hiện hàng giả</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="feature-box-ocean text-center h-100">
                <i class="fas fa-sync-alt fa-2x mb-2 text-warning"></i>
                <h6 class="fw-bold mb-1">Đổi Size Miễn Phí 7 Ngày</h6>
                <p class="small text-muted mb-0">Hỗ trợ đổi tận nhà nếu mang không vừa</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="feature-box-ocean text-center h-100">
                <i class="fas fa-headset fa-2x mb-2 text-danger"></i>
                <h6 class="fw-bold mb-1">Tư Vấn Chuyên Sâu</h6>
                <p class="small text-muted mb-0">Chọn giày chuẩn phom chân thon / chân bè</p>
            </div>
        </div>
    </div>

    <!-- FLASH SALE SECTION (Nếu có) -->
    <?php if (!empty($flashSales)): ?>
        <div class="p-4 rounded-4 mb-5" style="background: linear-gradient(135deg, #07263e 0%, #0369a1 100%);">
            <div class="d-flex justify-content-between align-items-center mb-4 text-white">
                <div>
                    <span class="badge bg-danger text-uppercase px-3 py-1 mb-1">
                        <i class="fas fa-fire me-1"></i> GIỜ VÀNG SĂN GIÀY
                    </span>
                    <h3 class="fw-bold text-warning mb-0">⚡ FLASH SALE ĐANG DIỄN RA</h3>
                </div>
                <a href="index.php?controller=product&action=list" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    Xem tất cả <i class="fas fa-angle-right ms-1"></i>
                </a>
            </div>

            <div class="row g-3">
                <?php foreach ($flashSales as $p): ?>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <?php 
                            $product = $p; 
                            require __DIR__ . '/../components/product_card.php'; 
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- MẶT SÂN NỔI BẬT BANNER -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="p-4 rounded-4 text-white position-relative overflow-hidden shadow-sm" 
                 style="background: linear-gradient(135deg, #0284c7 0%, #0c4a6e 100%); min-height: 160px;">
                <div class="position-relative z-1">
                    <span class="badge bg-warning text-dark mb-2">Đinh dăm TF</span>
                    <h4 class="fw-bold">SÂN CỎ NHÂN TẠO</h4>
                    <p class="small mb-3 opacity-90">Bám sân vượt trội, êm chân trên sân mini 5-7</p>
                    <a href="index.php?controller=product&action=list&category=1" class="btn btn-light btn-sm fw-bold rounded-pill">
                        Khám phá ngay &rarr;
                    </a>
                </div>
                <i class="fas fa-running position-absolute bottom-0 end-0 m-3 opacity-25 fa-5x"></i>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-4 rounded-4 text-white position-relative overflow-hidden shadow-sm" 
                 style="background: linear-gradient(135deg, #0d9488 0%, #115e59 100%); min-height: 160px;">
                <div class="position-relative z-1">
                    <span class="badge bg-warning text-dark mb-2">Đinh cao FG/AG</span>
                    <h4 class="fw-bold">SÂN CỎ TỰ NHIÊN</h4>
                    <p class="small mb-3 opacity-90">Trợ lực tối đa, bứt tốc như tia chớp trên sân 11</p>
                    <a href="index.php?controller=product&action=list&category=2" class="btn btn-light btn-sm fw-bold rounded-pill">
                        Khám phá ngay &rarr;
                    </a>
                </div>
                <i class="fas fa-futbol position-absolute bottom-0 end-0 m-3 opacity-25 fa-5x"></i>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-4 rounded-4 text-white position-relative overflow-hidden shadow-sm" 
                 style="background: linear-gradient(135deg, #b45309 0%, #78350f 100%); min-height: 160px;">
                <div class="position-relative z-1">
                    <span class="badge bg-warning text-dark mb-2">Đế bằng IC</span>
                    <h4 class="fw-bold">GIÀY FUTSAL TRONG NHÀ</h4>
                    <p class="small mb-3 opacity-90">Rê dắt linh hoạt, kiểm soát bóng gầm giày đỉnh cao</p>
                    <a href="index.php?controller=product&action=list&category=3" class="btn btn-light btn-sm fw-bold rounded-pill">
                        Khám phá ngay &rarr;
                    </a>
                </div>
                <i class="fas fa-shield-alt position-absolute bottom-0 end-0 m-3 opacity-25 fa-5x"></i>
            </div>
        </div>
    </div>

    <!-- SẢN PHẨM MỚI VỀ -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <span class="text-uppercase small fw-bold text-primary">Bộ Sưu Tập Mới Nhất</span>
            <h2 class="fw-extrabold text-uppercase mb-0">
                <i class="fas fa-fire text-danger me-2"></i> Giày Đá Bóng Nổi Bật
            </h2>
        </div>
        <a href="index.php?controller=product&action=list" class="btn btn-outline-primary rounded-pill px-4">
            Xem tất cả <i class="fas fa-angle-right ms-1"></i>
        </a>
    </div>

    <div class="row g-4 mb-5">
        <?php if(!empty($newProducts)): ?>
            <?php foreach($newProducts as $p): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <?php 
                        $product = $p; 
                        require __DIR__ . '/../components/product_card.php'; 
                    ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Đang cập nhật thêm các mẫu giày bóng đá mới...</p>
                <a href="database/migrate.php" class="btn btn-ocean-primary btn-sm">Nạp Dữ Liệu Mẫu (Seed Data)</a>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once 'views/layouts/footer.php'; ?>
