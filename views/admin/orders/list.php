<?php require_once 'views/admin/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Quản lý Đơn hàng</h3>
    
    <div class="d-flex gap-2">
        <form action="index.php" method="GET" class="d-flex">
            <input type="hidden" name="controller" value="adminOrder">
            <input type="hidden" name="action" value="index">
            
            <div class="input-group">
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Tìm mã, tên, SĐT..." 
                       value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                
                <button type="submit" class="btn btn-primary" title="Tìm kiếm">
                    <i class="fas fa-search"></i>
                </button>
                
                <?php if(isset($_GET['keyword']) && $_GET['keyword'] != ''): ?>
                    <a href="index.php?controller=adminOrder&action=index" class="btn btn-secondary" title="Xóa lọc">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
        <a href="index.php?controller=adminOrder&action=export" class="btn btn-success text-nowrap">
            <i class="fas fa-file-excel me-2"></i> Xuất Excel
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered bg-white align-middle">
        <thead class="table-primary">
            <tr>
                <th>Mã ĐH</th>
                <th>Khách hàng</th>
                
                <th>Số điện thoại</th>
                
                <th>Tổng tiền</th>
                <th>Ngày đặt</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Không tìm thấy đơn hàng nào phù hợp.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        
                        <td>
                            <strong><?= $order['fullname'] ?? $order['customer_name'] ?></strong>
                        </td>
                        
                        <td>
                            <?php 

                                if (!empty($order['phone'])) {
                                    echo '<span class="text-primary fw-bold">' . $order['phone'] . '</span>';
                                } elseif (!empty($order['customer_phone'])) {
                                    echo '<span class="text-secondary">' . $order['customer_phone'] . '</span>';
                                } else {
                                    echo '<span class="text-danger small">Không có</span>';
                                }
                            ?>
                        </td>

                        <td class="fw-bold text-danger"><?= number_format($order['total_money'], 0, ',', '.') ?>đ</td>
                        
                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        
                        <td>
                            <?php 
                                $colors = [
                                    'pending' => 'bg-warning text-dark',
                                    'shipping' => 'bg-info text-dark',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger'
                                ];
    
                                $statusMap = [
                                    'pending' => 'Chờ xử lý',
                                    'shipping' => 'Đang giao',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy'
                                ];
                                
                                $sttKey = strtolower($order['status']);
                                $colorClass = $colors[$sttKey] ?? 'bg-secondary';
                                $label = $statusMap[$sttKey] ?? strtoupper($order['status']);

                                echo "<span class='badge {$colorClass}'>{$label}</span>";
                            ?>
                        </td>
                        
                        <td>
                            <a href="index.php?controller=adminOrder&action=detail&id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
 require_once 'views/admin/layout/footer.php'; 
?>