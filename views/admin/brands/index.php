<?php 
require_once 'views/admin/layout/header.php'; 

// 1. Phòng thủ chống lỗi "Undefined variable $brands"
$brands = isset($brands) && is_array($brands) ? $brands : [];

// 2. Tìm thương hiệu cần sửa (nếu Controller chưa truyền sẵn $editBrand)
if (!isset($editBrand) && isset($_GET['edit_id']) && !empty($brands)) {
    $editId = (int)$_GET['edit_id'];
    foreach ($brands as $b) {
        if ((int)$b['id'] === $editId) {
            $editBrand = $b;
            break;
        }
    }
}
?>

<div class="row g-4">
    <!-- Form Thêm / Sửa Thương Hiệu -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-primary text-white fw-bold py-3">
                <?php if(isset($editBrand) && !empty($editBrand)): ?>
                    <i class="fas fa-edit me-2"></i> Chỉnh Sửa Thương Hiệu
                <?php else: ?>
                    <i class="fas fa-plus-circle me-2"></i> Thêm Thương Hiệu Mới
                <?php endif; ?>
            </div>
            <div class="card-body p-4">
                <form action="index.php?controller=adminBrand&action=<?= (isset($editBrand) && !empty($editBrand)) ? 'update' : 'store' ?>" method="POST">
                    <?= CsrfHelper::csrfField() ?>
                    
                    <?php if(isset($editBrand) && !empty($editBrand)): ?>
                        <input type="hidden" name="id" value="<?= (int)$editBrand['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Tên thương hiệu (*)</label>
                        <input type="text" name="name" class="form-control" required 
                               value="<?= isset($editBrand) && !empty($editBrand) ? htmlspecialchars($editBrand['name']) : '' ?>" 
                               placeholder="VD: Nike, Adidas, Puma, Mizuno...">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-ocean-primary fw-bold py-2 shadow-sm">
                            <i class="fas fa-save me-2"></i> <?= (isset($editBrand) && !empty($editBrand)) ? 'Cập Nhật' : 'Lưu Thương Hiệu' ?>
                        </button>
                        <?php if(isset($editBrand) && !empty($editBrand)): ?>
                            <a href="index.php?controller=adminBrand&action=index" class="btn btn-outline-secondary py-2">
                                <i class="fas fa-times me-1"></i> Hủy chỉnh sửa
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Danh Sách Thương Hiệu -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-bold text-primary mb-0">
                        <i class="fas fa-tags me-2"></i> Danh Sách Thương Hiệu
                    </h5>
                    <small class="text-muted">Tổng cộng: <?= count($brands) ?> thương hiệu</small>
                </div>

                <div class="d-flex gap-2">
                    <!-- Form Tìm kiếm Thương Hiệu -->
                    <form action="index.php" method="GET" class="d-inline-block">
                        <input type="hidden" name="controller" value="adminBrand">
                        <input type="hidden" name="action" value="index">
                        <div class="input-group input-group-sm">
                            <input type="text" name="keyword" class="form-control" placeholder="Tìm tên hãng..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            <?php if (!empty($_GET['keyword'])): ?>
                                <a href="index.php?controller=adminBrand&action=index" class="btn btn-outline-secondary" title="Xóa lọc">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <a href="index.php?controller=adminBrand&action=export" class="btn btn-sm btn-success text-nowrap">
                        <i class="fas fa-file-excel me-1"></i> Xuất CSV
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px;">ID</th>
                                <th class="text-start">Tên thương hiệu</th>
                                <th class="text-end pe-3" style="width: 220px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($brands)): ?>
                                <?php foreach($brands as $b): ?>
                                    <tr>
                                        <td><span class="badge bg-light text-muted border">#<?= (int)$b['id'] ?></span></td>
                                        <td class="text-start">
                                            <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($b['name']) ?></span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="index.php?controller=adminBrand&action=showProducts&id=<?= $b['id'] ?>" 
                                               class="btn btn-sm btn-outline-info me-1" title="Xem danh sách giày thuộc hãng này">
                                                <i class="fas fa-eye me-1"></i> Xem SP
                                            </a>

                                            <a href="index.php?controller=adminBrand&action=index&edit_id=<?= $b['id'] ?>" 
                                               class="btn btn-sm btn-outline-warning me-1" title="Sửa tên thương hiệu">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <a href="index.php?controller=adminBrand&action=delete&id=<?= $b['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Bạn có chắc muốn xóa thương hiệu này? Toàn bộ sản phẩm liên quan sẽ bị ảnh hưởng.');"
                                               title="Xóa thương hiệu">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-tag fa-2x mb-2 d-block opacity-50"></i>
                                        Không tìm thấy thương hiệu nào phù hợp.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/admin/layout/footer.php'; ?>


<?php require_once 'views/admin/layout/footer.php'; ?>