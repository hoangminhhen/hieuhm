<?php require_once 'views/admin/layout/header.php'; ?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Quản lý Thành viên</h3>
                
                <form action="index.php" method="GET" class="d-flex">
                    <input type="hidden" name="controller" value="adminUser">
                    <input type="hidden" name="action" value="index">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Tên, Email, SĐT..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                        <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email / SĐT</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th width="200">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($users)): ?>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td>
                                <strong><?= $u['fullname'] ?></strong><br>
                                <small class="text-muted"><?= $u['address'] ?? '' ?></small>
                            </td>
                            <td>
                                <?= $u['email'] ?><br>
                                <?= $u['phone'] ?>
                            </td>
                            <td>
                                <?php if($u['role'] == 'admin'): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php elseif($u['role'] == 'staff'): ?>
                                    <span class="badge bg-primary">Nhân viên</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Khách hàng</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(isset($u['status']) && $u['status'] == 1): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-dark">Đã khóa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#roleModal<?= $u['id'] ?>">
                                    <i class="fas fa-user-edit"></i> Sửa quyền
                                </button>

                                <?php if(isset($u['status']) && $u['status'] == 1): ?>
                                    <a href="index.php?controller=adminUser&action=lock&id=<?= $u['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Chặn tài khoản này mua hàng?')">
                                       <i class="fas fa-lock"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?controller=adminUser&action=unlock&id=<?= $u['id'] ?>" 
                                       class="btn btn-sm btn-success">
                                       <i class="fas fa-lock-open"></i>
                                    </a>
                                <?php endif; ?>

                                <div class="modal fade" id="roleModal<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="index.php?controller=adminUser&action=update_role" method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Phân quyền: <?= $u['fullname'] ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                    
                                                    <label class="form-label fw-bold">Chọn vai trò mới:</label>
                                                    <select name="role" class="form-select">
                                                        <option value="customer" <?= ($u['role'] != 'admin' && $u['role'] != 'staff') ? 'selected' : '' ?>>Khách hàng</option>
                                                        <option value="staff" <?= $u['role'] == 'staff' ? 'selected' : '' ?>>Nhân viên</option>
                                                        <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin (Quản trị)</option>
                                                    </select>
                                                    
                                                    <div class="alert alert-info mt-3 small mb-0">
                                                        <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong><br>
                                                        - <strong>Admin:</strong> Có toàn quyền hệ thống.<br>
                                                        - <strong>Nhân viên:</strong> Quản lý đơn hàng/sản phẩm.<br>
                                                        - <strong>Khách hàng:</strong> Chỉ mua hàng.
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Chưa có người dùng nào khác.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div> </body>
</html>