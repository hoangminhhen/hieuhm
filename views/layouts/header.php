<?php 
if (session_status() === PHP_SESSION_NONE) session_start(); 

require_once __DIR__ . '/../../helpers/CsrfHelper.php';
require_once __DIR__ . '/../../helpers/SecurityHelper.php';

$currentCategory = isset($_GET['category']) ? $_GET['category'] : '';
$currentKeyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= CsrfHelper::getToken() ?>">
    <title>Sport Kicks - Thế Giới Giày Bóng Đá Chính Hãng</title>
    
    <!-- Bootstrap 5 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Ocean Blue Design System Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">

<!-- Navigation Bar (Ocean Deep Theme) -->
<nav class="navbar navbar-expand-lg navbar-ocean sticky-top py-2">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand text-uppercase d-flex align-items-center" href="index.php">
            <i class="fas fa-futbol me-2 text-info"></i>
            <span>SPORT <span class="highlight text-info">KICKS</span></span>
        </a>
        
        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Categories Navigation -->
            <ul class="navbar-nav me-auto ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link <?= empty($currentCategory) && !isset($_GET['action']) ? 'active' : '' ?>" href="index.php">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentCategory == '1') ? 'active' : '' ?>" href="index.php?controller=product&action=list&category=1">
                        <i class="fas fa-layer-group me-1 opacity-75"></i> Cỏ nhân tạo (TF)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentCategory == '2') ? 'active' : '' ?>" href="index.php?controller=product&action=list&category=2">
                        <i class="fas fa-seedling me-1 opacity-75"></i> Cỏ tự nhiên (FG/AG)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentCategory == '3') ? 'active' : '' ?>" href="index.php?controller=product&action=list&category=3">
                        <i class="fas fa-cube me-1 opacity-75"></i> Giày Futsal (IC)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="docs/index.php" target="_blank" title="Xem tài liệu Swagger API">
                        <i class="fas fa-code me-1 text-warning"></i> API Docs
                    </a>
                </li>
            </ul>

            <!-- Search Bar -->
            <form class="d-flex me-3 my-2 my-lg-0" action="index.php" method="GET" style="min-width: 260px;">
                <input type="hidden" name="controller" value="product">
                <input type="hidden" name="action" value="search">
                <div class="input-group">
                    <input class="form-control form-control-sm search-input-ocean px-3" 
                           type="search" 
                           name="keyword" 
                           value="<?= htmlspecialchars($currentKeyword) ?>"
                           placeholder="Tìm giày Nike, Predator, TF..." 
                           required>
                    <button class="btn btn-sm search-btn-ocean px-3" type="submit" title="Tìm kiếm">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- User & Cart Controls -->
            <ul class="navbar-nav align-items-center gap-2">
                <!-- Cart Icon Component -->
                <li class="nav-item">
                    <?php require __DIR__ . '/../components/cart_icon.php'; ?>
                </li>

                <li class="border-start mx-1 d-none d-lg-block" style="height: 24px; border-color: rgba(255,255,255,0.25)!important;"></li>

                <?php if(isset($_SESSION['user'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user']['fullname']) ?>&background=0284c7&color=fff" 
                                 class="rounded-circle me-2 border border-light" width="30" height="30" alt="Avatar">
                            <span class="d-none d-lg-inline small fw-bold"><?= htmlspecialchars(explode(' ', $_SESSION['user']['fullname'])[0]) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                            <li class="px-3 py-1 text-muted small border-bottom mb-1">
                                Xin chào, <strong><?= htmlspecialchars($_SESSION['user']['fullname']) ?></strong>
                            </li>
                            <?php if($_SESSION['user']['role'] === 'admin'): ?>
                                <li>
                                    <a class="dropdown-item text-primary fw-bold" href="index.php?controller=admin&action=dashboard">
                                        <i class="fas fa-tachometer-alt me-2 text-primary"></i> Quản trị Dashboard
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>

                            <li><a class="dropdown-item" href="index.php?controller=user&action=profile"><i class="fas fa-user-circle me-2 text-secondary"></i> Hồ sơ tài khoản</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=user&action=orders"><i class="fas fa-receipt me-2 text-secondary"></i> Lịch sử đơn hàng</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="index.php?controller=user&action=logout"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm fw-bold px-3 rounded-pill" href="index.php?controller=user&action=login">
                            <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
