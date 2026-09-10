<?php
$page_title = 'الرئيسية';
require_once 'includes/header.php';

// جلب المنتجات المميزة
$stmt = $pdo->query("SELECT p.*, c.name AS category_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     ORDER BY p.created_at DESC 
                     LIMIT 6");
$featured = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero text-center">
    <div class="container">
        <h1><i class="fas fa-store"></i> <?= SITE_NAME ?></h1>
        <p class="lead mb-4"><?= SITE_OWNERS ?> — مشروع تطوير تطبيقات الويب</p>
        <a href="pages/products.php" class="btn btn-light btn-lg">
            <i class="fas fa-shopping-bag"></i> تصفح المنتجات
        </a>
    </div>
</section>

<!-- المنتجات المميزة -->
<section class="container">
    <h2 class="text-center mb-4 fw-bold">
        <i class="fas fa-star text-warning"></i> منتجات مميزة
    </h2>
    
    <div class="row g-4">
        <?php foreach ($featured as $p): ?>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-2"><?= e($p['category_name']) ?></span>
                    <h5 class="card-title"><?= e($p['name']) ?></h5>
                    <p class="card-text text-muted small"><?= e($p['description']) ?></p>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <span class="price-tag"><?= number_format($p['price'], 2) ?> ريال</span>
                    <a href="pages/product.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>