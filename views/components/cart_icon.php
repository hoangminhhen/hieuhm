<?php
/**
 * Cart Icon Component with Animated Counter Badge
 */
if (session_status() === PHP_SESSION_NONE) session_start();

$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += (int)($item['quantity'] ?? 0);
    }
}
?>

<a class="cart-icon-btn d-flex align-items-center text-white text-decoration-none" href="index.php?controller=cart&action=index" title="Xem giỏ hàng">
    <i class="fas fa-shopping-cart fa-lg"></i>
    <span id="navbar-cart-count" class="cart-counter-badge" style="<?= ($cartCount > 0) ? '' : 'display: none;' ?>">
        <?= $cartCount ?>
    </span>
</a>

