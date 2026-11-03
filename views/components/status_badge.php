<?php
/**
 * Order & Entity Status Badge Component
 * Parameter expected: $status (string)
 */

if (!isset($status)) return;

switch ($status) {
    case 'pending':
        echo '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Chờ xử lý</span>';
        break;
    case 'processing':
        echo '<span class="badge bg-info text-dark"><i class="fas fa-cog fa-spin me-1"></i> Đang chuẩn bị</span>';
        break;
    case 'shipping':
        echo '<span class="badge bg-primary"><i class="fas fa-shipping-fast me-1"></i> Đang giao hàng</span>';
        break;
    case 'completed':
        echo '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Hoàn thành</span>';
        break;
    case 'cancelled':
        echo '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Đã hủy</span>';
        break;
    default:
        echo '<span class="badge bg-secondary">' . htmlspecialchars(ucfirst($status)) . '</span>';
        break;
}
?>
