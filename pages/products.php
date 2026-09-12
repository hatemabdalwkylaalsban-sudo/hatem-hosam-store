<?php
$page_title = 'المنتجات';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';

// ============ الفلترة والبحث ============
$search = clean($_GET['search'] ?? '');
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// بناء الاستعلام
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_filter > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_filter;
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// جلب التصنيفات للفلترة
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<!-- عنوان الصفحة -->
<div class="text-center mb-4">
    <h1 class="fw-bold"><i class="fas fa-shopping-bag text-gradient"></i> جميع المنتجات</h1>
    <p class="text-muted">تصفح منتجاتنا المتنوعة</p>
</div>

<!-- شريط البحث والفلترة -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="ابحث عن منتج..." value="<?= e($search) ?>">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select">
                    <option value="0">كل التصنيفات</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $category_filter == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> فلترة
                </button>
            </div>
        </form>
    </div>
</div>

<!-- عدد النتائج -->
<p class="text-muted">
    <i class="fas fa-info-circle"></i> عدد النتائج: <strong><?= count($products) ?></strong> منتج
</p>

<!-- شبكة المنتجات -->
<div class="row g-4">
    <?php if (empty($products)): ?>
        <div class="col-12 text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">لا توجد منتجات مطابقة</h4>
            <a href="<?= BASE_URL ?>pages/products.php" class="btn btn-primary mt-3">عرض كل المنتجات</a>
        </div>
    <?php else: ?>
        <?php foreach ($products as $p): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <?php if (!empty($p['image']) && file_exists(dirname(FILE) . '/../assets/img/' . $p['image'])): ?>
                    <img src="<?= BASE_URL ?>assets/img/<?= e($p['image']) ?>" 
                         class="card-img-top" alt="<?= e($p['name']) ?>">
                <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center">
                        <i class="fas fa-image fa-4x text-muted"></i>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-2"><?= e($p['category_name'] ?? 'بدون تصنيف') ?></span>
                    <h5 class="card-title"><?= e($p['name']) ?></h5>
                    <p class="card-text text-muted small">
                        <?= e(mb_substr($p['description'], 0, 80)) ?><?= mb_strlen($p['description']) > 80 ? '...' : '' ?>
                    </p>
                </div>
                
                <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="price-tag"><?= number_format($p['price'], 2) ?> ريال</div>
                        <?php if ($p['stock'] > 0): ?>
                            <small class="text-success"><i class="fas fa-check-circle"></i> متوفر</small>
                        <?php else: ?>
                            <small class="text-danger"><i class="fas fa-times-circle"></i> نفذ</small>
                        <?php endif; ?>
                    </div>
                    <a href="<?= BASE_URL ?>pages/product.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>