<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Panel | Sport Kicks</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        body {
            background-color: #f0f7fb;
            min-height: 100vh;
            overflow-x: hidden;
            font-family: 'Segoe UI', -apple-system, sans-serif;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #07263e 0%, #0c4a6e 100%);
            color: #fff;
            padding-top: 20px;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
            border-right: 1px solid rgba(6, 182, 212, 0.2);
        }
        .sidebar a {
            text-decoration: none;
            color: #bae6fd;
            padding: 12px 20px;
            display: block;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            transition: 0.25s;
            cursor: pointer;
        }
        .sidebar a:hover {
            background-color: rgba(2, 132, 199, 0.25);
            color: #fff;
            padding-left: 24px;
            border-left: 4px solid #06b6d4;
        }
        .sidebar a.active {
            background-color: #0284c7;
            color: #fff;
            font-weight: bold;
            border-left: 4px solid #ffffff;
        }
        
        .submenu a {
            padding-left: 48px !important;
            font-size: 0.88rem;
            background-color: rgba(0, 0, 0, 0.2);
            border-bottom: none;
        }
        .submenu a:hover {
            padding-left: 54px !important;
            color: #38bdf8 !important;
        }
        .submenu a.sub-active {
            color: #38bdf8 !important;
            font-weight: bold;
        }

        .sidebar i { width: 25px; text-align: center; }
        
        .admin-content {
            margin-left: 250px;
            padding: 24px;
            width: calc(100% - 250px);
            min-height: 100vh;
        }
        
        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .sidebar .link-text, .user-info, .fa-chevron-down { display: none; }
            .admin-content { margin-left: 70px; width: calc(100% - 70px); }
            .sidebar h4 { font-size: 0.8rem; }
        }
    </style>
</head>
<body>

<?php
    if (session_status() === PHP_SESSION_NONE) session_start();
    $ctrl = isset($_GET['controller']) ? $_GET['controller'] : 'admin';
    $act = isset($_GET['action']) ? $_GET['action'] : 'dashboard';
?>

<div class="sidebar d-flex flex-column shadow">
    <h4 class="text-center fw-bold mb-3 text-white text-uppercase pb-3 border-bottom border-secondary">
        <i class="fas fa-futbol text-info me-2"></i> SPORT KICKS
    </h4>
    
    <?php if(isset($_SESSION['user'])): ?>
        <div class="user-info text-white text-center mb-3 pb-3 border-bottom border-secondary px-3">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user']['fullname']) ?>&background=0284c7&color=fff" 
                 class="rounded-circle mb-2 border border-info" width="46" alt="Avatar">
            <br>
            <small class="text-light opacity-75">Quản trị viên:</small>
            <div class="fw-bold text-truncate text-white"><?= htmlspecialchars($_SESSION['user']['fullname']) ?></div>
            <span class="badge bg-info text-dark mt-1">Super Admin</span>
        </div>
    <?php endif; ?>
    
    <a href="index.php?controller=admin&action=dashboard" class="<?= ($ctrl=='admin' && $act=='dashboard') ? 'active' : '' ?>">
        <i class="fas fa-tachometer-alt"></i> <span class="link-text">Tổng quan</span>
    </a>
    
    <a href="index.php?controller=adminProduct&action=index" class="<?= ($ctrl=='adminProduct') ? 'active' : '' ?>">
        <i class="fas fa-box-open"></i> <span class="link-text">Giày bóng đá</span>
    </a>

    <?php $isStock = ($ctrl == 'adminStock'); ?>
    <a href="#stockSubmenu" data-bs-toggle="collapse" 
       class="d-flex justify-content-between align-items-center <?= $isStock ? 'active' : '' ?>" 
       aria-expanded="<?= $isStock ? 'true' : 'false' ?>" 
       role="button">
        <span>
            <i class="fas fa-warehouse"></i> <span class="link-text">Quản lý kho</span>
        </span>
        <i class="fas fa-chevron-down" style="font-size: 0.8em;"></i>
    </a>
    
    <div class="collapse submenu <?= $isStock ? 'show' : '' ?>" id="stockSubmenu">
        <a href="index.php?controller=adminStock&action=index" 
           class="<?= ($isStock && $act=='index') ? 'sub-active' : '' ?>">
            <i class="fas fa-plus-circle me-2" style="font-size: 0.8em;"></i> <span class="link-text">Nhập kho mới</span>
        </a>
        <a href="index.php?controller=adminStock&action=history" 
           class="<?= ($isStock && ($act=='history' || $act=='detail')) ? 'sub-active' : '' ?>">
            <i class="fas fa-history me-2" style="font-size: 0.8em;"></i> <span class="link-text">Lịch sử nhập kho</span>
        </a>
    </div>

    <a href="index.php?controller=adminBrand&action=index" class="<?= ($ctrl=='adminBrand') ? 'active' : '' ?>">
        <i class="fas fa-tags"></i> <span class="link-text">Thương hiệu</span>
    </a>
    
    <a href="index.php?controller=adminOrder&action=index" class="<?= ($ctrl=='adminOrder') ? 'active' : '' ?>">
        <i class="fas fa-shopping-cart"></i> <span class="link-text">Đơn hàng</span>
    </a>
    
    <a href="index.php?controller=adminUser&action=index" class="<?= ($ctrl=='adminUser') ? 'active' : '' ?>">
        <i class="fas fa-users"></i> <span class="link-text">Thành viên</span>
    </a>

    <a href="docs/index.php" target="_blank" class="text-warning">
        <i class="fas fa-code"></i> <span class="link-text">Swagger API Docs</span>
    </a>
    
    <div class="mt-auto border-top border-secondary pt-2">
        <a href="index.php" class="text-info">
            <i class="fas fa-store"></i> <span class="link-text">Xem Website Shop</span>
        </a>
        <a href="index.php?controller=user&action=logout" class="text-danger fw-bold">
            <i class="fas fa-sign-out-alt"></i> <span class="link-text">Đăng xuất</span>
        </a>
    </div>
</div>

<div class="admin-content">
    <nav class="navbar navbar-light bg-white shadow-sm rounded-4 mb-4 px-4 py-3 d-flex justify-content-between align-items-center border">
        <div class="d-flex align-items-center">
            <span class="fw-bold text-primary text-uppercase me-2">
                <?php 
                    if($ctrl == 'adminProduct') echo '<i class="fas fa-box me-1"></i> Quản lý Sản Phẩm Giày';
                    elseif($ctrl == 'adminStock' && $act == 'index') echo '<i class="fas fa-warehouse me-1"></i> Nhập Kho Hàng Mới';
                    elseif($ctrl == 'adminStock' && ($act == 'history' || $act == 'detail')) echo '<i class="fas fa-history me-1"></i> Lịch Sử Nhập Hàng';
                    elseif($ctrl == 'adminBrand') echo '<i class="fas fa-tags me-1"></i> Quản lý Thương Hiệu Giày'; 
                    elseif($ctrl == 'adminOrder') echo '<i class="fas fa-shopping-cart me-1"></i> Quản lý Đơn Hàng';
                    elseif($ctrl == 'adminUser') echo '<i class="fas fa-users me-1"></i> Quản lý Thành Viên';
                    else echo '<i class="fas fa-chart-line me-1"></i> Bảng Điều Khiển Tổng Quan (Dashboard)';
                ?>
            </span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="database/migrate.php" class="btn btn-sm btn-outline-info rounded-pill" title="Chạy lại migration & seed data">
                <i class="fas fa-database me-1"></i> Chạy Migrate/Seed
            </a>
            <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i> <?= date('d/m/Y') ?></span>
        </div>
    </nav>
