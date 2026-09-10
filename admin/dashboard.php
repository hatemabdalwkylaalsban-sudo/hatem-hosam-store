<?php
$page_title = 'لوحة التحكم';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';

// حماية الصفحة - للأدمن فقط
require_admin();

// ============ الإحصائيات ============
$stats = [];

// عدد المستخدمين
$stats['users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// عدد المنتجات
$stats['products'] = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// عدد التصنيفات
$stats['categories'] = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

// عدد الطلبات
$stats['orders'] = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// إجمالي المبيعات (من الطلبات المدفوعة)
$stats['sales'] = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'pending'")->fetchColumn();

// أحدث المنتجات
$latest_products = $pdo->query("SELECT p.*, c.name AS category_name 
                                 FROM products p 
                                 LEFT JOIN categories c ON p.category_id = c.id 
                                 ORDER BY p.created_at DESC LIMIT 5")->fetchAll();

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
        <i class="fas fa-tachometer-alt text-gradient"></i> لوحة التحكم
    </h2>
    <span class="badge bg-success">
        <i class="fas fa-user-shield"></i> مرحباً <?= e($_SESSION['user_name']) ?>
    </span>
</div>

<!-- بطاقات الإحصائيات -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">المستخدمون</h6>
                        <h2 class="fw-bold mb-0"><?= $stats['users'] ?></h2>
                    </div>
                    <i class="fas fa-users fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">المنتجات</h6>
                        <h2 class="fw-bold mb-0"><?= $stats['products'] ?></h2>
                    </div>
                    <i class="fas fa-box fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">التصنيفات</h6>
                        <h2 class="fw-bold mb-0"><?= $stats['categories'] ?></h2>
                    </div>
                    <i class="fas fa-tags fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">الطلبات</h6>
                        <h2 class="fw-bold mb-0"><?= $stats['orders'] ?></h2>
                    </div>
                    <i class="fas fa-shopping-bag fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- إجمالي المبيعات -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #14b8a6 0%, #06b6d4 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><i class="fas fa-dollar-sign"></i> إجمالي المبيعات</h5>
                        <h2 class="fw-bold mb-0"><?= number_format($stats['sales'], 2) ?> ريال</h2>
                    </div>
                    <i class="fas fa-chart-line fa-4x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- روابط سريعة -->
<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><i class="fas fa-bolt text-warning"></i> إجراءات سريعة</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= BASE_URL ?>admin/products.php" class="btn btn-primary">
                        <i class="fas fa-box"></i> إدارة المنتجات
                    </a>
                    <a href="<?= BASE_URL ?>admin/categories.php" class="btn btn-success">
                        <i class="fas fa-tags"></i> إدارة التصنيفات
                    </a>
                    <a href="<?= BASE_URL ?>admin/orders.php" class="btn btn-warning">
                        <i class="fas fa-shopping-bag"></i> إدارة الطلبات
                    </a>
                    <a href="<?= BASE_URL ?>admin/users.php" class="btn btn-info">
                        <i class="fas fa-users"></i> إدارة المستخدمين
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- أحدث المنتجات -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="fas fa-clock text-primary"></i> أحدث المنتجات</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>التصنيف</th>
                        <th>السعر</th>
                        <th>المخزون</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($latest_products)): ?>
                        <tr><td colspan="6" class="text-center text-muted">لا توجد منتجات</td></tr>
                    <?php else: ?>
                        <?php foreach ($latest_products as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($p['name']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= e($p['category_name']) ?></span></td>
                            <td class="fw-bold text-success"><?= number_format($p['price'], 2) ?> ريال</td>
                            <td><?= $p['stock'] ?></td>
                            <td><?= date('Y-m-d', strtotime($p['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>