<?php require_once 'views/admin/layout/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Tổng Quan Hoạt Động Cửa Hàng</h3>
            <p class="text-muted small mb-0">Theo dõi doanh thu, đơn hàng và kho giày bóng đá Sport Kicks</p>
        </div>
        <div>
            <a href="index.php?controller=adminStock&action=index" class="btn btn-ocean-primary btn-sm rounded-pill px-3 me-2">
                <i class="fas fa-plus me-1"></i> Nhập Kho Nhanh
            </a>
            <a href="docs/index.php" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-book me-1"></i> API Contract
            </a>
        </div>
    </div>

    <!-- Stat Cards with Ocean Gradients -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <a href="index.php?controller=adminOrder&action=index" class="text-decoration-none">
                <div class="card text-white stat-card-ocean-1 border-0 shadow-sm rounded-4 h-100 card-hover">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-uppercase opacity-75 small fw-bold">Tổng Doanh Thu</h6>
                                <h3 class="fw-extrabold mb-1"><?= number_format($revenue, 0, ',', '.') ?> ₫</h3>
                                <small class="text-white opacity-75"><i class="fas fa-check-circle me-1"></i> Đơn hàng hoàn tất</small>
                            </div>
                            <i class="fas fa-coins fa-3x opacity-40"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="index.php?controller=adminOrder&action=index" class="text-decoration-none">
                <div class="card text-white stat-card-ocean-2 border-0 shadow-sm rounded-4 h-100 card-hover">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-uppercase opacity-75 small fw-bold">Đơn Hàng Mới</h6>
                                <h3 class="fw-extrabold mb-1"><?= $newOrders ?> đơn</h3>
                                <small class="text-white opacity-75"><i class="fas fa-clock me-1"></i> Chờ duyệt & xuất kho</small>
                            </div>
                            <i class="fas fa-shopping-bag fa-3x opacity-40"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="index.php?controller=adminProduct&action=index" class="text-decoration-none">
                <div class="card text-white stat-card-ocean-3 border-0 shadow-sm rounded-4 h-100 card-hover">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-uppercase opacity-75 small fw-bold">Sản Phẩm Giày</h6>
                                <h3 class="fw-extrabold mb-1"><?= $totalProducts ?> mẫu</h3>
                                <small class="text-white opacity-75"><i class="fas fa-boxes me-1"></i> Đang mở bán</small>
                            </div>
                            <i class="fas fa-futbol fa-3x opacity-40"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Low Stock Warning Box (Nếu có) -->
    <?php if (!empty($lowStockItems)): ?>
        <div class="alert alert-warning border-warning rounded-4 shadow-sm mb-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i> CẢNH BÁO TỒN KHO THẤP (Còn dưới 3 đôi)
                </h6>
                <a href="index.php?controller=adminStock&action=index" class="btn btn-warning btn-sm fw-bold rounded-pill">
                    <i class="fas fa-truck-loading me-1"></i> Nhập kho bổ sung
                </a>
            </div>
            <p class="small text-muted mb-3">Các mẫu giày bóng đá dưới đây đang sắp hết hàng theo từng size, vui lòng lên kế hoạch nhập hàng:</p>
            <div class="row g-2">
                <?php foreach (array_slice($lowStockItems, 0, 4) as $item): ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="p-2 bg-white rounded border d-flex align-items-center">
                            <img src="<?= htmlspecialchars($item['image']) ?>" class="rounded me-2" style="width: 40px; height: 40px; object-fit: contain;">
                            <div class="overflow-hidden">
                                <div class="small fw-bold text-truncate" title="<?= htmlspecialchars($item['name']) ?>"><?= htmlspecialchars($item['name']) ?></div>
                                <small class="text-danger fw-bold">Size: <?= $item['size'] ?> (Còn: <?= $item['stock'] ?> đôi)</small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Charts & Top Selling -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i> Doanh Thu 6 Tháng Gần Nhất
                    </h6>
                    <span class="badge bg-light text-muted">Đơn vị: VNĐ</span>
                </div>
                <div class="card-body p-4">
                    <canvas id="revenueChart" style="max-height: 320px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-fire text-danger me-2"></i> Top Giày Bán Chạy Nhất
                    </h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th class="ps-3">Sản phẩm</th>
                                <th class="text-center">Đã bán</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($topProducts)): ?>
                                <?php foreach($topProducts as $top): ?>
                                    <tr>
                                        <td class="ps-3 py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($top['image']) ?>" 
                                                     width="42" height="42" 
                                                     class="rounded-3 me-2 border p-1" 
                                                     style="object-fit: contain;">
                                                <span class="small fw-bold text-truncate text-dark" style="max-width: 170px;" title="<?= htmlspecialchars($top['name']) ?>">
                                                    <?= htmlspecialchars($top['name']) ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger rounded-pill px-3 py-2 fw-bold">
                                                <?= $top['total_sold'] ?> đôi
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="text-center text-muted py-4">Chưa có dữ liệu bán hàng</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = <?php echo json_encode(array_column($chartData, 'month')); ?>;
    const data = <?php echo json_encode(array_column($chartData, 'total')); ?>;

    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar', 
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: data,
                backgroundColor: 'rgba(2, 132, 199, 0.85)',
                borderColor: '#0284c7',
                borderWidth: 1.5,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                        }
                    }
                }
            }
        }
    });
</script>

</div>
</body>
</html>
