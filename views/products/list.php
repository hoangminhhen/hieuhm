<?php require_once 'views/layouts/header.php'; ?>

<!-- Breadcrumb & Header Title -->
<div class="py-4 mb-4" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f7fb 100%); border-bottom: 1px solid #bae6fd;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Danh sách giày bóng đá</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0 text-primary">
            <?php 
                if (!empty($keyword)) echo 'Kết quả tìm kiếm: "' . htmlspecialchars($keyword) . '"';
                elseif ($categoryId == 1) echo '<i class="fas fa-layer-group me-2"></i> Giày Sân Cỏ Nhân Tạo (TF)';
                elseif ($categoryId == 2) echo '<i class="fas fa-seedling me-2"></i> Giày Sân Cỏ Tự Nhiên (FG/AG)';
                elseif ($categoryId == 3) echo '<i class="fas fa-cube me-2"></i> Giày Futsal Sân Trong Nhà (IC)';
                elseif ($categoryId == 4) echo '<i class="fas fa-tshirt me-2"></i> Phụ Kiện Bóng Đá';
                else echo '<i class="fas fa-futbol me-2"></i> Tất Cả Giày Bóng Đá Chính Hãng';
            ?>
        </h2>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white fw-bold text-uppercase py-3">
                    <i class="fas fa-filter me-2 text-warning"></i> Bộ Lọc Tìm Kiếm
                </div>
                <div class="card-body p-3">
                    <h6 class="fw-bold text-uppercase small text-muted mb-2">Loại mặt sân</h6>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-1">
                            <a href="index.php?controller=product&action=list" 
                               class="d-flex justify-content-between align-items-center p-2 rounded text-decoration-none <?= empty($categoryId) ? 'bg-primary text-white fw-bold' : 'text-dark' ?>">
                               <span>Tất cả mặt sân</span>
                               <i class="fas fa-chevron-right small opacity-50"></i>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="index.php?controller=product&action=list&category=1" 
                               class="d-flex justify-content-between align-items-center p-2 rounded text-decoration-none <?= ($categoryId == '1') ? 'bg-primary text-white fw-bold' : 'text-dark' ?>">
                               <span>Cỏ nhân tạo (TF)</span>
                               <span class="badge <?= ($categoryId == '1') ? 'bg-white text-primary' : 'bg-light text-muted' ?>">TF</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="index.php?controller=product&action=list&category=2" 
                               class="d-flex justify-content-between align-items-center p-2 rounded text-decoration-none <?= ($categoryId == '2') ? 'bg-primary text-white fw-bold' : 'text-dark' ?>">
                               <span>Cỏ tự nhiên (FG/AG)</span>
                               <span class="badge <?= ($categoryId == '2') ? 'bg-white text-primary' : 'bg-light text-muted' ?>">FG</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="index.php?controller=product&action=list&category=3" 
                               class="d-flex justify-content-between align-items-center p-2 rounded text-decoration-none <?= ($categoryId == '3') ? 'bg-primary text-white fw-bold' : 'text-dark' ?>">
                               <span>Futsal trong nhà (IC)</span>
                               <span class="badge <?= ($categoryId == '3') ? 'bg-white text-primary' : 'bg-light text-muted' ?>">IC</span>
                            </a>
                        </li>
                    </ul>

                    <hr class="text-muted">

                    <h6 class="fw-bold text-uppercase small text-muted mb-2">Sắp xếp theo</h6>
                    <select class="form-select form-select-sm" onchange="location = this.value;">
                        <option value="index.php?controller=product&action=list&sort=newest<?= !empty($categoryId) ? '&category='.$categoryId : '' ?><?= !empty($keyword) ? '&keyword='.urlencode($keyword) : '' ?>" <?= ($sort == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="index.php?controller=product&action=list&sort=price_asc<?= !empty($categoryId) ? '&category='.$categoryId : '' ?><?= !empty($keyword) ? '&keyword='.urlencode($keyword) : '' ?>" <?= ($sort == 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="index.php?controller=product&action=list&sort=price_desc<?= !empty($categoryId) ? '&category='.$categoryId : '' ?><?= !empty($keyword) ? '&keyword='.urlencode($keyword) : '' ?>" <?= ($sort == 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
                        <option value="index.php?controller=product&action=list&sort=views_desc<?= !empty($categoryId) ? '&category='.$categoryId : '' ?><?= !empty($keyword) ? '&keyword='.urlencode($keyword) : '' ?>" <?= ($sort == 'views_desc') ? 'selected' : '' ?>>Xem nhiều nhất</option>
                        <option value="index.php?controller=product&action=list&sort=name_az<?= !empty($categoryId) ? '&category='.$categoryId : '' ?><?= !empty($keyword) ? '&keyword='.urlencode($keyword) : '' ?>" <?= ($sort == 'name_az') ? 'selected' : '' ?>>Tên A - Z</option>
                    </select>

                    <div class="mt-4 p-3 rounded bg-light border text-center">
                        <i class="fas fa-shield-alt text-success fa-2x mb-2"></i>
                        <h6 class="fw-bold mb-1">Chính sách bảo hành</h6>
                        <p class="small text-muted mb-0">Bảo hành keo chỉ 6 tháng, hỗ trợ đổi size linh hoạt.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <?php if(empty($products)): ?>
                <div class="card shadow-sm border-0 rounded-4 text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="fw-bold text-muted">Không tìm thấy sản phẩm nào phù hợp!</h5>
                        <p class="text-muted small">Hãy thử tìm với từ khóa khác (ví dụ: Mercurial, Predator, Nike, Puma) hoặc xóa bộ lọc.</p>
                        <a href="index.php?controller=product&action=list" class="btn btn-ocean-primary btn-sm px-4 rounded-pill">
                            Xem tất cả sản phẩm
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach($products as $productItem): ?>
                        <div class="col-md-4 col-sm-6">
                            <?php 
                                $product = $productItem;
                                require __DIR__ . '/../components/product_card.php'; 
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination Component -->
                <?php if (isset($totalPages) && $totalPages > 1): ?>
                    <nav class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?controller=product&action=list&page=<?= $i ?><?= !empty($keyword) ? '&keyword='.urlencode($keyword) : '' ?><?= !empty($categoryId) ? '&category='.$categoryId : '' ?><?= !empty($sort) ? '&sort='.$sort : '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
