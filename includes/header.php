<?php
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/functions.php';

// منع الوصول لغير المسجلين
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'register.php', 'logout.php'];

if (!in_array($current_page, $public_pages) && !is_logged_in()) {
    $_SESSION['error'] = "يجب تسجيل الدخول أولاً";
    redirect('auth/login.php');
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? e($page_title) . ' | ' . SITE_NAME : SITE_NAME ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-gradient" href="<?= BASE_URL ?>">
            <i class="fas fa-store"></i> <?= SITE_NAME ?>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (is_logged_in()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>"><i class="fas fa-home"></i> الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>pages/products.php"><i class="fas fa-box"></i> المنتجات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>pages/my-orders.php"><i class="fas fa-receipt"></i> طلباتي</a>
                </li>
                <?php endif; ?>
                
                <?php if (is_admin()): ?>
                <li class="nav-item">
                    <a class="nav-link text-warning fw-bold" href="<?= BASE_URL ?>admin/dashboard.php">
                        <i class="fas fa-cog"></i> لوحة التحكم
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav">
                <?php if (is_logged_in()): ?>
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?= BASE_URL ?>pages/cart.php">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                        <?php if (!empty($_SESSION['cart'])): ?>
                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-0">
                            <?= count($_SESSION['cart']) ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?= e($_SESSION['user_name'] ?? 'حسابي') ?>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (is_admin()): ?>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>pages/profile.php"><i class="fas fa-user"></i> الملف الشخصي</a></li>
    <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>auth/logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="py-4">
    <div class="container">
        <?php show_flash(); ?>
