<?php require_once 'views/admin/layout/header.php'; ?>

<div class="card shadow mb-5">
    <div class="card-header bg-primary text-white fw-bold">
        <i class="fas fa-plus-circle me-2"></i>Thêm Sản Phẩm Mới
    </div>
    <div class="card-body">
        <form action="index.php?controller=adminProduct&action=store" method="POST" enctype="multipart/form-data">
            <?= CsrfHelper::csrfField() ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Nhập tên giày hoặc phụ kiện...">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Danh mục</label>
                            <select name="category_id" class="form-select">
                                <?php foreach($categories as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Thương hiệu</label>
                            <select name="brand_id" class="form-select">
                                <?php foreach($brands as $b): ?>
                                    <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Giá bán <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="price" class="form-control" required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Giá gốc (nếu có giảm giá)</label>
                            <input type="number" name="old_price" class="form-control" min="0">
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
                                    <input type="number" name="sale_price" id="sale_price" class="form-control" value="0">
                                    <small class="text-muted" style="font-size: 0.75rem;">Nhập 0 để tắt sale</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold small">Bắt đầu</label>
                                    <input type="datetime-local" name="sale_start" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold small">Kết thúc</label>
                                    <input type="datetime-local" name="sale_end" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold">Mô tả chi tiết</label>
                        <textarea name="description" class="form-control" rows="5"></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    
                    <div class="mb-3 border p-3 rounded bg-light">
                        <label class="fw-bold mb-2">Ảnh đại diện chính</label>
                        <input type="file" name="image" class="form-control" required>
                        <small class="text-muted">Ảnh hiển thị ngoài danh sách.</small>
                    </div>

                    <div class="mb-3 border p-3 rounded bg-light">
                        <label class="fw-bold mb-2">Thư viện ảnh (Gallery)</label>
                        <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
                        <small class="text-muted">Giữ phím <strong>Ctrl</strong> để chọn nhiều ảnh.</small>
                    </div>

                    <div class="alert alert-info small mt-3">
                        <i class="fas fa-info-circle"></i> Để nhập Size và Số lượng, vui lòng tạo sản phẩm xong và vào mục <strong>"Nhập Kho"</strong>.
                    </div>

                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="index.php?controller=adminProduct&action=index" class="btn btn-secondary px-4">Quay lại</a>
                <button type="submit" class="btn btn-primary px-5 fw-bold">
                    <i class="fas fa-save me-2"></i> Lưu Sản Phẩm
                </button>
            </div>
        </form>
    </div>
</div>

<script>
  
    document.querySelector('form').addEventListener('submit', function(e) {
        // Lấy giá trị từ ô nhập liệu
        const price = parseFloat(document.querySelector('input[name="price"]').value) || 0;
        const salePrice = parseFloat(document.querySelector('input[name="sale_price"]').value) || 0;

        if (salePrice > 0 && salePrice >= price) {
            e.preventDefault(); // Chặn không cho gửi form đi

            alert('LỖI: Giá Flash Sale (' + salePrice + ') phải NHỎ HƠN Giá bán chính thức (' + price + ')!');
            
            // Focus vào ô lỗi để người dùng sửa
            const saleInput = document.querySelector('input[name="sale_price"]');
            saleInput.focus();
            saleInput.classList.add('is-invalid', 'border-danger');
        }
    });
    // Tự động bỏ viền đỏ khi người dùng nhập lại
    document.querySelector('input[name="sale_price"]').addEventListener('input', function() {
        this.classList.remove('is-invalid', 'border-danger');
    });
</script>