<?php
/**
 * Reusable Product Card Component
 * Parameters expected: $product (array)
 */

if (!isset($product)) return;

$p = $product;
$anchor_price = ($p['old_price'] > 0) ? (float)$p['old_price'] : (float)$p['price'];

// Kiểm tra Flash Sale
$is_flash_sale = false;
$sale_percent = 0;
$display_price = (float)$p['price'];

if (!empty($p['sale_price']) && $p['sale_price'] > 0 && !empty($p['sale_start']) && !empty($p['sale_end'])) {
    $now = time();
    $start = strtotime($p['sale_start']);
    $end = strtotime($p['sale_end']);
    if ($now >= $start && $now <= $end) {
        $is_flash_sale = true;
        $display_price = (float)$p['sale_price'];
        if ($anchor_price > 0) {
            $sale_percent = round((($anchor_price - $display_price) / $anchor_price) * 100);
        }
    }
}

if (!$is_flash_sale && $p['old_price'] > $p['price']) {
    $sale_percent = round((($p['old_price'] - $p['price']) / $p['old_price']) * 100);
}

// Xử lý loại sân
$ground_label = !empty($p['ground_type']) ? $p['ground_type'] : ($p['cat_name'] ?? 'Sân cỏ nhân tạo');
?>

<div class="card-product-ocean">
    <!-- Badges -->
    <?php if ($is_flash_sale): ?>
        <div class="badge-flash-ocean">
            <i class="fas fa-bolt me-1"></i> -<?= $sale_percent ?>% Flash Sale
        </div>
    <?php elseif ($sale_percent > 0): ?>
        <div class="badge-discount-ocean">
            -<?= $sale_percent ?>%
        </div>
    <?php endif; ?>

    <div class="badge-ground-ocean">
        <?= htmlspecialchars($ground_label) ?>
    </div>

    <!-- Product Image -->
    <a href="index.php?controller=product&action=detail&id=<?= (int)$p['id'] ?>" class="card-img-wrapper">
        <img src="<?= htmlspecialchars($p['image']) ?>" 
             alt="<?= htmlspecialchars($p['name']) ?>" 
             loading="lazy"
             onerror="this.src='https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop&q=80'">
    </a>

    <!-- Card Content -->
    <div class="card-body">
        <div class="brand-label">
            <?= htmlspecialchars($p['brand_name'] ?? 'Chính hãng') ?>
        </div>

        <h3 class="product-title" title="<?= htmlspecialchars($p['name']) ?>">
            <a href="index.php?controller=product&action=detail&id=<?= (int)$p['id'] ?>">
                <?= htmlspecialchars($p['name']) ?>
            </a>
        </h3>

        <!-- Sizes available preview -->
        <div class="sizes-preview">
            <?php if (!empty($p['available_sizes'])): ?>
                <i class="fas fa-shoe-prints me-1"></i> Size: <strong><?= htmlspecialchars($p['available_sizes']) ?></strong>
            <?php else: ?>
                <i class="fas fa-check-circle text-success me-1"></i> Đầy đủ size 39 - 44
            <?php endif; ?>
        </div>

        <!-- Price Section -->
        <div class="price-section">
            <div class="d-flex align-items-baseline">
                <span class="price-current"><?= number_format($display_price, 0, ',', '.') ?> ₫</span>
                <?php if ($anchor_price > $display_price): ?>
                    <span class="price-old"><?= number_format($anchor_price, 0, ',', '.') ?> ₫</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-grid mt-2">
            <a href="index.php?controller=product&action=detail&id=<?= (int)$p['id'] ?>" class="btn btn-ocean-outline btn-sm py-2">
                <i class="fas fa-eye me-1"></i> Xem Chi Tiết
            </a>
        </div>
    </div>
</div>

