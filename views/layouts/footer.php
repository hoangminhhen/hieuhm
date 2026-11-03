<footer class="footer-ocean text-white py-5 mt-auto">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold text-white text-uppercase d-flex align-items-center mb-3">
                    <i class="fas fa-futbol text-info me-2"></i> SPORT KICKS VIỆT NAM
                </h5>
                <p class="small text-light opacity-75">
                    Hệ thống bán lẻ giày đá bóng chính hãng số 1 Việt Nam. Chuyên cung cấp giày sân cỏ nhân tạo (TF), sân cỏ tự nhiên (FG/AG) và Futsal từ các thương hiệu Nike, Adidas, Puma, Mizuno.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold text-white text-uppercase mb-3">Danh Mục Giày</h6>
                <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                    <li><a href="index.php?controller=product&action=list&category=1">Giày cỏ nhân tạo TF</a></li>
                    <li><a href="index.php?controller=product&action=list&category=2">Giày cỏ tự nhiên FG</a></li>
                    <li><a href="index.php?controller=product&action=list&category=3">Giày Futsal IC</a></li>
                    <li><a href="index.php?controller=product&action=list&category=4">Phụ kiện bóng đá</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-white text-uppercase mb-3">Chính Sách & Hỗ Trợ</h6>
                <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                    <li><a href="#">Chính sách bảo hành 6 tháng</a></li>
                    <li><a href="#">Đổi size miễn phí trong 7 ngày</a></li>
                    <li><a href="#">Hướng dẫn chọn size giày chuẩn</a></li>
                    <li><a href="docs/index.php" target="_blank" class="text-warning fw-bold"><i class="fas fa-book me-1"></i> Swagger API Contract</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-white text-uppercase mb-3">Liên Hệ Trực Tiếp</h6>
                <ul class="list-unstyled small mb-0 d-flex flex-column gap-2 text-light opacity-75">
                    <li><i class="fas fa-map-marker-alt text-info me-2"></i> 79 Trần Duy Hưng, Cầu Giấy, Hà Nội</li>
                    <li><i class="fas fa-phone-alt text-info me-2"></i> Hotline: 0901.234.567 (8h - 22h)</li>
                    <li><i class="fas fa-envelope text-info me-2"></i> cskh@sportkicks.vn</li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-light opacity-75">
            <div>© <?= date('Y') ?> Sport Kicks Football Store. Toàn bộ bản quyền được bảo lưu.</div>
            <div class="mt-2 mt-md-0">
                <span class="badge bg-primary me-1">Chuẩn hóa 3NF</span>
                <span class="badge bg-info text-dark me-1">OpenAPI 3.0</span>
                <span class="badge bg-success">An toàn CSRF/SQLi</span>
            </div>
        </div>
    </div>
</footer>

<!-- Toast Bridge Component -->
<?php require_once __DIR__ . '/../components/toast.php'; ?>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- API Client Service -->
<script src="assets/js/api-client.js"></script>

</body>
</html>
