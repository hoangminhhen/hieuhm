<?php
/**
 * Toast Container & Session Flash Bridge
 */
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<div id="toast-container"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['success'])): ?>
        ShopAPI.showToast(<?= json_encode($_SESSION['success']) ?>, 'success', 'Thành công');
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        ShopAPI.showToast(<?= json_encode($_SESSION['error']) ?>, 'error', 'Thông báo lỗi');
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['warning'])): ?>
        ShopAPI.showToast(<?= json_encode($_SESSION['warning']) ?>, 'warning', 'Cảnh báo');
        <?php unset($_SESSION['warning']); ?>
    <?php endif; ?>
});
</script>

