<?php
$page_title = 'لوحة التحكم';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';

// ✅ حماية صارمة - للأدمن فقط
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

// إجمالي المبيعات
$stats['sales'] = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'pending'")->fetchColumn();

// طلبات قيد المراجعة
$stats['pending'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

// منتجات قاربت على النفاد
$low_stock = $pdo->query("SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC LIMIT 5")->fetchAll();

// أحدث المنتجات
$latest_products = $pdo->query("SELECT p.*, c.name AS category_name 
                                 FROM products p 
                                 LEFT JOIN categories c ON p.category_id = c.id 
                                 ORDER BY p.created_at DESC LIMIT 5")->fetchAll();

// أحدث الطلبات
$latest_orders = $pdo->query("SELECT o.*, u.full_name AS user_name 
                              FROM orders o 
                              LEFT JOIN users u ON o.user_id = u.id 
                              ORDER BY o.created_at DESC LIMIT 5")->fetchAll();

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<!-- ترحيب -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">
            <i class="fas fa-tachometer-alt text-gradient"></i> لوحة التحكم
        </h2>
        <p class="text-muted mb-0">مرحباً بك، <?= e($_SESSION['user_name']) ?></p>
    </div>
    <span class="badge bg-success fs-6">
        <i class="fas fa-user-shield"></i> مدير النظام
    </span>
</div>

<!-- بطاقات الإحصائيات -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 small">المستخدمون</h6>
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
                        <h6 class="text-uppercase mb-1 small">المنتجات</h6>
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
                        <h6 class="text-uppercase mb-1 small">التصنيفات</h6>
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
                        <h6 class="text-uppercase mb-1 small">الطلبات</h6>
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
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #14b8a6 0%, #06b6d4 100%);">
            <div class="card-body text-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><i class="fas fa-dollar-sign"></i> إجمالي المبيعات</h5>
                        <h2 class="fw-bold mb-0"><?= number_format($stats['sales'], 2) ?> ريال</h2>
                    </div>
                    <i class="fas fa-chart-line fa-5x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 small">طلبات معلقة</h6>
                        <h2 class="fw-bold mb-0"><?= $stats['pending'] ?></h2>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- إجراءات سريعة -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="fas fa-bolt text-warning"></i> إجراءات سريعة</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= BASE_URL ?>admin/products.php" class="btn btn-primary">
                <i class="fas fa-box"></i> إدارة المنتجات
            </a>
            <a href="<?= BASE_URL ?>admin/product-form.php" class="btn btn-success">
                <i class="fas fa-plus"></i> إضافة منتج
            </a>
            <a href="<?= BASE_URL ?>admin/categories.php" class="btn btn-warning">
                <i class="fas fa-tags"></i> التصنيفات
            </a>
            <a href="<?= BASE_URL ?>admin/orders.php" class="btn btn-info text-white">
                <i class="fas fa-shopping-bag"></i> الطلبات
            </a>
            <a href="<?= BASE_URL ?>admin/users.php" class="btn btn-secondary">
                <i class="fas fa-users"></i> المستخدمون
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- منتجات قاربت على النفاد -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-exclamation-triangle text-warning"></i> منتجات قاربت على النفاد
                </h5>
                <?php if (empty($low_stock)): ?>
                    <p class="text-muted text-center py-3">
                        <i class="fas fa-check-circle text-success fa-2x d-block mb-2"></i>
                        جميع المنتجات متوفرة بكميات جيدة
                    </p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($low_stock as $p): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><?= e($p['name']) ?></span>
                                <span class="badge bg-<?= $p['stock'] == 0 ? 'danger' : 'warning' ?>">
                                    <?= $p['stock'] ?> قطعة
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- أحدث الطلبات -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-clock text-info"></i> أحدث الطلبات
                </h5>
                <?php if (empty($latest_orders)): ?>
                    <p class="text-muted text-center py-3">
                        <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                        لا توجد طلبات بعد
                    </p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($latest_orders as $o): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong>#<?= $o['id'] ?></strong>
                                    <small class="text-muted d-block"><?= e($o['user_name'] ?? 'محذوف') ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold text-success"><?= number_format($o['total'], 2) ?> ريال</span>
                                    <br>
                                    <small class="text-muted"><?= date('Y-m-d', strtotime($o['created_at'])) ?></small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- أحدث المنتجات -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="fas fa-box text-primary"></i> أحدث المنتجات</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
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
                    <?php foreach ($latest_products as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($p['name']) ?></td>
                        <td><span class="badge bg-light text-dark"><?= e($p['category_name'] ?? 'بدون') ?></span></td>
                        <td class="fw-bold text-success"><?= number_format($p['price'], 2) ?> ريال</td>
                        <td>
                            <?php if ($p['stock'] > 10): ?>
                                <span class="badge bg-success"><?= $p['stock'] ?></span>
                            <?php elseif ($p['stock'] > 0): ?>
                                <span class="badge bg-warning"><?= $p['stock'] ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger">نفذ</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small"><?= date('Y-m-d', strtotime($p['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>