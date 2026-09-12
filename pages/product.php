<?php
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = "المنتج غير موجود";
    redirect('pages/products.php');
}

// جلب المنتج
$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['error'] = "المنتج غير موجود";
    redirect('pages/products.php');
}

$page_title = $product['name'];

// جلب منتجات مشابهة
$related = $pdo->prepare("SELECT * FROM products 
                          WHERE category_id = ? AND id != ? 
                          LIMIT 3");
$related->execute([$product['category_id'], $id]);
$related_products = $related->fetchAll();

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">الرئيسية</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>pages/products.php">المنتجات</a></li>
        <li class="breadcrumb-item active"><?= e($product['name']) ?></li>
    </ol>
</nav>

<div class="row g-4">
    <!-- صورة المنتج -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <?php if (!empty($product['image']) && file_exists(dirname(FILE) . '/../assets/img/' . $product['image'])): ?>
                <img src="<?= BASE_URL ?>assets/img/<?= e($product['image']) ?>" 
                     class="card-img-top" style="height:400px; object-fit:cover;">
            <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center" style="height:400px;">
                    <i class="fas fa-image fa-5x text-muted"></i>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- تفاصيل المنتج -->
    <div class="col-md-7">
        <span class="badge bg-light text-dark mb-2"><?= e($product['category_name'] ?? 'بدون تصنيف') ?></span>
        <h1 class="fw-bold mb-3"><?= e($product['name']) ?></h1>
        
        <div class="mb-3">
            <span class="price-tag" style="font-size:2rem;"><?= number_format($product['price'], 2) ?> ريال</span>
        </div>

        <p class="text-muted lead"><?= e($product['description']) ?></p>

        <hr>

        <div class="mb-4">
            <?php if ($product['stock'] > 0): ?>
                <p class="text-success"><i class="fas fa-check-circle"></i> متوفر في المخزون (<?= $product['stock'] ?> قطعة)</p>
            <?php else: ?>
                <p class="text-danger"><i class="fas fa-times-circle"></i> نفذ من المخزون</p>
            <?php endif; ?>
        </div>

        <!-- نموذج الإضافة للسلة -->
        <?php if ($product['stock'] > 0): ?>
        <form action="<?= BASE_URL ?>pages/cart.php" method="POST" class="row g-2">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            
            <div class="col-md-4">
                <label class="form-label">الكمية</label>
                <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" 
                       class="form-control text-center">
            </div>
            <div class="col-md-8 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-cart-plus"></i> إضافة إلى السلة
                </button>
            </div>
        </form>
        <?php else: ?>
            <button class="btn btn-secondary" disabled>
                <i class="fas fa-times"></i> غير متوفر حالياً
            </button>
        <?php endif; ?>
    </div>
</div>
<!-- منتجات مشابهة -->
<?php if (!empty($related_products)): ?>
<section class="mt-5">
    <h3 class="fw-bold mb-4"><i class="fas fa-th text-gradient"></i> منتجات مشابهة</h3>
    <div class="row g-4">
        <?php foreach ($related_products as $rp): ?>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?= e($rp['name']) ?></h5>
                    <div class="price-tag"><?= number_format($rp['price'], 2) ?> ريال</div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="<?= BASE_URL ?>pages/product.php?id=<?= $rp['id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                        عرض التفاصيل
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>