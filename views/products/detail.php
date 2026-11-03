<?php require_once 'views/layouts/header.php'; ?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php?controller=product&action=list&category=<?= $product['category_id'] ?>" class="text-decoration-none"><?= htmlspecialchars($product['cat_name'] ?? 'Giày bóng đá') ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4 mb-5">
        <!-- Gallery & Main Image -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden p-3 bg-white text-center position-relative">
                <?php 
                    $anchor_price = ($product['old_price'] > 0) ? (float)$product['old_price'] : (float)$product['price'];
                    $is_flash_sale = false;
                    $percent = 0;
                    $current_price = (float)$product['price'];

                    if (!empty($product['sale_price']) && $product['sale_price'] > 0 && !empty($product['sale_start']) && !empty($product['sale_end'])) {
                        $now = time();
                        if ($now >= strtotime($product['sale_start']) && $now <= strtotime($product['sale_end'])) {
                            $is_flash_sale = true;
                            $current_price = (float)$product['sale_price'];
                            if ($anchor_price > 0) {
                                $percent = round((($anchor_price - $current_price) / $anchor_price) * 100);
                            }
                        }
                    } elseif ($product['old_price'] > $product['price']) {
                        $percent = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100);
                    }
                ?>

                <?php if ($is_flash_sale): ?>
                    <div class="badge-flash-ocean fs-6">
                        <i class="fas fa-bolt me-1"></i> Flash Sale -<?= $percent ?>%
                    </div>
                <?php elseif ($percent > 0): ?>
                    <div class="badge-discount-ocean fs-6">
                        -<?= $percent ?>%
                    </div>
                <?php endif; ?>

                <img id="mainImage" 
                     src="<?= htmlspecialchars($product['image']) ?>" 
                     class="img-fluid rounded-3 object-fit-contain p-4" 
                     style="height: 420px; width: 100%; transition: opacity 0.25s ease;" 
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     onerror="this.src='https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop&q=80'">
            </div>

            <!-- Thumbnail Gallery -->
            <div class="d-flex justify-content-center gap-2 overflow-auto py-3">
                <div class="border border-primary border-2 rounded-3 p-1 cursor-pointer thumb-item active" 
                     onclick="changeImage(this, '<?= htmlspecialchars($product['image']) ?>')">
                    <img src="<?= htmlspecialchars($product['image']) ?>" 
                         class="rounded-2" 
                         style="width: 70px; height: 70px; object-fit: contain;">
                </div>

                <?php if (!empty($secondaryImages)): ?>
                    <?php foreach ($secondaryImages as $img): ?>
                        <div class="border rounded-3 p-1 cursor-pointer thumb-item" 
                             onclick="changeImage(this, '<?= htmlspecialchars($img['image_url']) ?>')">
                            <img src="<?= htmlspecialchars($img['image_url']) ?>" 
                                 class="rounded-2" 
                                 style="width: 70px; height: 70px; object-fit: cover;">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Details & Actions -->
        <div class="col-lg-6">
            <div class="ps-lg-3">
                <span class="badge bg-light text-primary border border-primary px-3 py-1 text-uppercase fw-bold mb-2">
                    <?= htmlspecialchars($product['brand_name'] ?? 'Chính hãng') ?>
                </span>
                
                <h1 class="h2 fw-bold text-dark mb-2"><?= htmlspecialchars($product['name']) ?></h1>
                
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="text-warning small">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <span class="text-muted ms-1">(4.8/5 từ 95 đánh giá)</span>
                    </div>
                    <span class="text-muted">•</span>
                    <span class="badge bg-info text-dark">
                        <i class="fas fa-eye me-1"></i> <?= number_format($product['views'] ?? 0) ?> lượt xem
                    </span>
                </div>

                <!-- Price Box -->
                <div class="p-3 rounded-4 mb-4" style="background: linear-gradient(135deg, #f0f7fb 0%, #e0f2fe 100%); border: 1px solid #bae6fd;">
                    <?php if ($is_flash_sale): ?>
                        <div class="d-flex align-items-center text-danger fw-bold small mb-1">
                            <i class="fas fa-fire me-1"></i> ƯU ĐÃI FLASH SALE KẾT THÚC: <?= date('H:i d/m', strtotime($product['sale_end'])) ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex align-items-baseline gap-3">
                        <span class="fs-2 fw-extrabold text-danger">
                            <?= number_format($current_price, 0, ',', '.') ?> ₫
                        </span>
                        <?php if ($anchor_price > $current_price): ?>
                            <span class="fs-5 text-muted text-decoration-line-through">
                                <?= number_format($anchor_price, 0, ',', '.') ?> ₫
                            </span>
                            <span class="badge bg-danger">Tiết kiệm <?= number_format($anchor_price - $current_price, 0, ',', '.') ?> ₫</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Form Add to Cart & Buy Now -->
                <form id="productForm" action="index.php?controller=cart&action=add" method="POST">
                    <?= CsrfHelper::csrfField() ?>
                    <input type="hidden" name="product_id" id="productId" value="<?= $product['id'] ?>">

                    <!-- Ground Type & Specs -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light small">
                                <span class="text-muted d-block">Mặt sân thi đấu:</span>
                                <strong><?= htmlspecialchars($product['ground_type'] ?? 'Sân cỏ nhân tạo TF') ?></strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-light small">
                                <span class="text-muted d-block">Chất liệu thân giày:</span>
                                <strong><?= htmlspecialchars($product['upper_material'] ?? 'Flyknit / Da tổng hợp') ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Size Selector -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold">
                                Chọn Size (EUR):
                                <span id="stock-message" class="text-danger small ms-2"></span>
                            </label>
                            <a href="#size-guide" data-bs-toggle="modal" class="small text-decoration-none">
                                <i class="fas fa-ruler me-1"></i> Hướng dẫn đo size chân
                            </a>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <?php if(!empty($product['variants'])): ?>
                                <?php foreach($product['variants'] as $v): ?>
                                    <input type="radio" 
                                           class="btn-check size-radio" 
                                           name="size" 
                                           id="size_<?= $v['size'] ?>" 
                                           value="<?= $v['size'] ?>" 
                                           data-stock="<?= $v['stock'] ?>"
                                           required>
                                    <label class="btn btn-outline-primary px-3 py-2 fw-bold" for="size_<?= $v['size'] ?>" style="min-width: 55px;">
                                        <?= $v['size'] ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-warning py-2 px-3 small w-100">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Sản phẩm đang tạm hết hàng tất cả các size. Vui lòng quay lại sau!
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quantity Input -->
                    <div class="mb-4" style="max-width: 180px;">
                        <label class="fw-bold mb-2">Số lượng:</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(-1)">-</button>
                            <input type="number" name="quantity" id="quantity" 
                                   class="form-control text-center fw-bold" 
                                   value="1" min="1" max="50">
                            <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(1)">+</button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3 mb-4">
                        <!-- Nút Thêm vào giỏ hàng sử dụng AJAX API (không tải lại trang) -->
                        <button type="button" id="btnAddToCartAjax" 
                                class="btn btn-outline-primary btn-lg flex-grow-1 fw-bold py-3 shadow-sm"
                                <?= empty($product['variants']) ? 'disabled' : '' ?>>
                            <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ
                        </button>
                        
                        <!-- Nút Mua ngay (Checkout trực tiếp) -->
                        <button type="submit" name="action_type" value="buy_now" 
                                class="btn btn-danger btn-lg flex-grow-1 fw-bold py-3 shadow"
                                <?= empty($product['variants']) ? 'disabled' : '' ?>>
                            <i class="fas fa-bolt me-2"></i> MUA NGAY
                        </button>
                    </div>
                </form>

                <!-- Value Props Highlights -->
                <div class="card border-0 bg-light p-3 rounded-3 small">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <span>Cam kết chính hãng 100% - Đền gấp 10 lần nếu phát hiện hàng giả</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-sync-alt text-primary me-2"></i>
                        <span>Đổi size miễn phí trong 7 ngày nếu mang không vừa chân</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-gift text-danger me-2"></i>
                        <span>Tặng kèm vớ dệt chống trượt & túi rút đựng giày cao cấp</span>
                    </div>
                </div>

                <!-- Description Accordion -->
                <div class="mt-4">
                    <h5 class="fw-bold mb-2">Mô tả sản phẩm</h5>
                    <p class="text-secondary leading-relaxed mb-0">
                        <?= nl2br(htmlspecialchars($product['description'] ?? 'Giày bóng đá chính hãng cao cấp.')) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if(!empty($relatedProducts)): ?>
        <div class="mt-5 border-top pt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-uppercase mb-0">
                    <i class="fas fa-fire text-danger me-2"></i> Sản Phẩm Tương Tự
                </h3>
            </div>
            
            <div class="row g-4">
                <?php foreach($relatedProducts as $rp): ?>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <?php 
                            $product = $rp; 
                            require __DIR__ . '/../components/product_card.php'; 
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Hướng Dẫn Đo Size -->
<div class="modal fade" id="size-guide" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-ruler me-2"></i> Hướng Dẫn Chọn Size Giày Bóng Đá</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p>1. Đặt bàn chân lên tờ giấy A4 sát mép tường vuông góc.</p>
                <p>2. Dùng bút đánh dấu điểm ngón chân dài nhất và gót chân.</p>
                <p>3. Đo chiều dài (cm) và cộng thêm <strong>0.5 cm - 1 cm</strong> nếu phom chân bè:</p>
                <table class="table table-bordered text-center small mt-3">
                    <thead class="table-light">
                        <tr><th>Chiều dài chân (cm)</th><th>Size EUR</th><th>Size US</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>24.5 cm</td><td><strong>39</strong></td><td>6.5</td></tr>
                        <tr><td>25.0 cm</td><td><strong>40</strong></td><td>7.0</td></tr>
                        <tr><td>25.5 - 26.0 cm</td><td><strong>41</strong></td><td>8.0</td></tr>
                        <tr><td>26.5 cm</td><td><strong>42</strong></td><td>8.5</td></tr>
                        <tr><td>27.0 - 27.5 cm</td><td><strong>43</strong></td><td>9.5</td></tr>
                        <tr><td>28.0 cm</td><td><strong>44</strong></td><td>10.0</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function changeImage(element, src) {
        const mainImg = document.getElementById('mainImage');
        mainImg.style.opacity = '0.4';
        setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = '1';
        }, 150);

        document.querySelectorAll('.thumb-item').forEach(el => {
            el.classList.remove('border-primary', 'border-2');
            el.classList.add('border');
        });
        element.classList.remove('border');
        element.classList.add('border-primary', 'border-2');
    }

    function adjustQty(delta) {
        const input = document.getElementById('quantity');
        let val = parseInt(input.value) || 1;
        val += delta;
        if (val < 1) val = 1;
        const max = parseInt(input.max) || 50;
        if (val > max) val = max;
        input.value = val;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sizeRadios = document.querySelectorAll('.size-radio');
        const quantityInput = document.getElementById('quantity');
        const stockMessage = document.getElementById('stock-message');
        const btnAjax = document.getElementById('btnAddToCartAjax');
        const productId = document.getElementById('productId').value;

        // Cập nhật thông báo tồn kho khi chọn size
        sizeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const stock = parseInt(this.getAttribute('data-stock'));
                quantityInput.max = stock;
                stockMessage.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> Còn ' + stock + ' đôi trong kho';
                if (parseInt(quantityInput.value) > stock) {
                    quantityInput.value = 1;
                }
            });
        });

        // Xử lý Thêm vào giỏ hàng bằng AJAX API
        if (btnAjax) {
            btnAjax.addEventListener('click', function() {
                const selectedSize = document.querySelector('input[name="size"]:checked');
                if (!selectedSize) {
                    ShopAPI.showToast('Vui lòng chọn size giày trước khi thêm vào giỏ!', 'warning', 'Chưa chọn size');
                    return;
                }
                const qty = parseInt(quantityInput.value) || 1;
                ShopAPI.addToCart(productId, selectedSize.value, qty, btnAjax);
            });
        }
    });
</script>

<?php require_once 'views/layouts/footer.php'; ?>
