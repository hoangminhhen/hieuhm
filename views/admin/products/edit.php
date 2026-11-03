<?php require_once 'views/admin/layout/header.php'; ?>

<div class="card shadow mb-5">
    <div class="card-header bg-warning text-dark fw-bold">
        <i class="fas fa-edit me-2"></i>Sửa Sản Phẩm: <?= htmlspecialchars($product['name']) ?>
    </div>
    <div class="card-body">
        <form action="index.php?controller=adminProduct&action=update" method="POST" enctype="multipart/form-data">
            <?= CsrfHelper::csrfField() ?>
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <input type="hidden" name="current_image" value="<?= $product['image'] ?>">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Danh mục</label>
                            <select name="category_id" class="form-select">
                                <?php foreach($categories as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($c['id'] == $product['category_id']) ? 'selected' : '' ?>>
                                        <?= $c['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Thương hiệu</label>
                            <select name="brand_id" class="form-select">
                                <?php foreach($brands as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= ($b['id'] == $product['brand_id']) ? 'selected' : '' ?>>
                                        <?= $b['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Giá bán <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="price" class="form-control" value="<?= $product['price'] ?>" required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Giá gốc</label>
                            <input type="number" name="old_price" class="form-control" value="<?= $product['old_price'] ?>" min="0">
                        </div>
                    </div>

                    <div class="card bg-warning bg-opacity-10 border-warning mb-3">
                        <div class="card-header fw-bold text-warning small text-uppercase">
                            <i class="fas fa-bolt me-1"></i> Cài đặt Flash Sale (Theo giờ)
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold small">Giá Sale (VNĐ)</label>
                                    <input type="number" name="sale_price" id="sale_price" class="form-control" 
                                           value="<?= isset($product['sale_price']) ? $product['sale_price'] : 0 ?>">
                                    <small class="text-muted" style="font-size: 0.75rem;">Nhập 0 để tắt sale</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold small">Bắt đầu</label>
                                    <input type="datetime-local" name="sale_start" class="form-control"
                                           value="<?= !empty($product['sale_start']) ? date('Y-m-d\TH:i', strtotime($product['sale_start'])) : '' ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold small">Kết thúc</label>
                                    <input type="datetime-local" name="sale_end" class="form-control"
                                           value="<?= !empty($product['sale_end']) ? date('Y-m-d\TH:i', strtotime($product['sale_end'])) : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Mô tả chi tiết</label>
                        <textarea name="description" class="form-control" rows="5"><?= $product['description'] ?></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    
                    <div class="mb-3 border p-3 rounded bg-light">
                        <label class="fw-bold mb-2">Ảnh đại diện chính</label>
                        <div class="text-center my-2 border bg-white p-2 rounded">
                            <img src="<?= $product['image'] ?>" class="img-fluid" style="max-height: 150px;">
                        </div>
                        <input type="file" name="image" class="form-control">
                        <small class="text-muted">Chỉ chọn nếu muốn thay ảnh mới.</small>
                    </div>

                    <div class="mb-3 border p-3 rounded bg-light">
                        <label class="fw-bold mb-2">Thư viện ảnh (Gallery)</label>
                        
                        <div class="mb-3">
                            <label class="small text-muted fw-bold">Thêm ảnh mới:</label>
                            <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
                        </div>

                        <?php if (!empty($gallery)): ?>
                            <hr>
                            <label class="small text-muted fw-bold mb-2">Ảnh hiện có (Tích vào ô để xóa):</label>
                            <div class="row g-2">
                                <?php foreach ($gallery as $img): ?>
                                    <div class="col-4 text-center position-relative">
                                        <div class="border rounded p-1 bg-white">
                                            <img src="<?= $img['image_url'] ?>" class="img-fluid" style="height: 60px; object-fit: cover;">
                                        </div>
                                        <div class="form-check d-flex justify-content-center mt-1">
                                            <input class="form-check-input border-danger" type="checkbox" name="delete_gallery[]" value="<?= $img['id'] ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="alert alert-info small mt-3">
                        <i class="fas fa-info-circle"></i> Để cập nhật số lượng và size, vui lòng vào mục <strong>"Nhập Kho"</strong>.
                    </div>

                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="index.php?controller=adminProduct&action=index" class="btn btn-secondary px-4">Hủy</a>
                <button type="submit" class="btn btn-warning px-5 fw-bold">
                    <i class="fas fa-save me-2"></i> Cập Nhật
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        // Lấy giá trị
        const price = parseFloat(document.querySelector('input[name="price"]').value) || 0;
        const salePrice = parseFloat(document.querySelector('input[name="sale_price"]').value) || 0;
        if (salePrice > 0 && salePrice >= price) {
            e.preventDefault(); // Chặn gửi form
            
            alert('LỖI: Giá Flash Sale (' + salePrice + ') phải NHỎ HƠN Giá bán chính thức (' + price + ')!');           
            // Focus vào ô lỗi
            const saleInput = document.querySelector('input[name="sale_price"]');
            saleInput.focus();
            saleInput.classList.add('is-invalid', 'border-danger');
        }
    });// Tự động bỏ viền đỏ khi người dùng nhập lại
    document.querySelector('input[name="sale_price"]').addEventListener('input', function() {
        this.classList.remove('is-invalid', 'border-danger');
    });
</script>