<?php
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/functions.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? e($page_title) . ' | ' . SITE_NAME : SITE_NAME ?></title>
    
    <!-- خط Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- التصميم المخصص -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>

<!-- الشريط العلوي -->
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
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>"><i class="fas fa-home"></i> الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>pages/products.php"><i class="fas fa-box"></i> المنتجات</a>
                </li>
                <?php if (is_logged_in()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>pages/my-orders.php"><i class="fas fa-receipt"></i> طلباتي</a>
                </li>
                <?php endif; ?>
                <?php if (is_admin()): ?>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="<?= BASE_URL ?>admin/dashboard.php"><i class="fas fa-cog"></i> لوحة التحكم</a>
                </li>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav">
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
                
                <?php if (is_logged_in()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?= e($_SESSION['user_name'] ?? 'حسابي') ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>pages/profile.php">الملف الشخصي</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>pages/my-orders.php">طلباتي</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>auth/logout.php">تسجيل الخروج</a></li>
</ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="btn btn-outline-primary btn-sm" href="<?= BASE_URL ?>auth/login.php">دخول</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary btn-sm ms-2" href="<?= BASE_URL ?>auth/register.php">حساب جديد</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- محتوى الصفحة يبدأ -->
<main class="py-4">
    <div class="container">
        <?php show_flash(); ?>