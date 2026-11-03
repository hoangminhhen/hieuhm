<?php require_once 'views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold text-uppercase text-primary mb-1">
                <i class="fas fa-history me-2"></i> Lịch Sử Đơn Hàng Của Bạn
            </h3>
            <p class="text-muted small mb-0">Theo dõi trạng thái giao hàng và chi tiết các đơn giày bóng đá</p>
        </div>
        <a href="index.php?controller=product&action=list" class="btn btn-outline-primary btn-sm rounded-pill">
            <i class="fas fa-plus me-1"></i> Mua thêm sản phẩm
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <?php if (!empty($orders)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Phương thức TT</th>
                                <th>Trạng thái đơn</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <strong class="text-primary">
                                            <?= htmlspecialchars(!empty($order['order_code']) ? $order['order_code'] : '#' . $order['id']) ?>
                                        </strong>
                                    </td>
                                    <td class="small text-muted">
                                        <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                    </td>
                                    <td class="text-danger fw-bold fs-6">
                                        <?= number_format($order['total_money'], 0, ',', '.') ?> ₫
                                    </td>
                                    <td>
                                        <?php $method = $order['payment_method'] ?? 'COD'; ?>
                                        <?php if ($method === 'BANKING'): ?>
                                             <span class="badge bg-primary"><i class="fas fa-university me-1"></i> Chuyển khoản QR</span> 
                                        <?php else: ?>
                                             <span class="badge bg-secondary"><i class="fas fa-money-bill-wave me-1"></i> Tiền mặt COD</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $status = $order['status'] ?? 'pending';
                                            require __DIR__ . '/../components/status_badge.php'; 
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <a href="index.php?controller=user&action=editOrder&id=<?= $order['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary me-1" 
                                               title="Đổi địa chỉ / SĐT nhận hàng">
                                               <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <a href="index.php?controller=user&action=cancelOrder&id=<?= $order['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Bạn có chắc chắn muốn HỦY đơn hàng này không? Tồn kho giày sẽ được hoàn trả tự động.');"
                                               title="Hủy đơn hàng">
                                               <i class="fas fa-times"></i>
                                            </a>
                                        <?php else: ?>
                                            <small class="text-muted fst-italic">Không thể chỉnh sửa</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-4x text-muted mb-3 opacity-50"></i>
                    <h5 class="fw-bold text-dark">Bạn chưa có đơn hàng nào</h5>
                    <p class="text-muted small">Khám phá các mẫu giày bóng đá mới nhất và đặt hàng ngay hôm nay!</p>
                    <a href="index.php?controller=product&action=list" class="btn btn-ocean-primary rounded-pill px-4 mt-2">
                        Bắt đầu mua sắm
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
