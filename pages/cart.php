<?php
$page_title = 'سلة المشتريات';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';

// تهيئة السلة في الجلسة
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ============ معالجة الإجراءات ============
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// 1. إضافة منتج
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    // التحقق من وجود المنتج
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product && $quantity > 0) {
        // التحقق من المخزون
        if ($quantity > $product['stock']) {
            $_SESSION['error'] = "الكمية المطلوبة أكبر من المخزون المتاح";
        } else {
            // إذا كان المنتج موجوداً في السلة، نزيد الكمية
            if (isset($_SESSION['cart'][$product_id])) {
                $new_qty = $_SESSION['cart'][$product_id] + $quantity;
                if ($new_qty > $product['stock']) {
                    $new_qty = $product['stock'];
                }
                $_SESSION['cart'][$product_id] = $new_qty;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            $_SESSION['success'] = "تم إضافة المنتج إلى السلة";
        }
    } else {
        $_SESSION['error'] = "منتج غير صالح";
    }
    redirect('pages/cart.php');
}

// 2. تحديث الكميات
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantities = $_POST['quantities'] ?? [];
    foreach ($quantities as $pid => $qty) {
        $pid = (int)$pid;
        $qty = (int)$qty;
        if ($qty > 0) {
            $_SESSION['cart'][$pid] = $qty;
        } else {
            unset($_SESSION['cart'][$pid]);
        }
    }
    $_SESSION['success'] = "تم تحديث السلة";
    redirect('pages/cart.php');
}

// 3. حذف منتج
if ($action === 'remove') {
    $product_id = (int)($_GET['id'] ?? 0);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['success'] = "تم حذف المنتج من السلة";
    }
    redirect('pages/cart.php');
}

// 4. تفريغ السلة
if ($action === 'clear') {
    $_SESSION['cart'] = [];
    $_SESSION['success'] = "تم تفريغ السلة";
    redirect('pages/cart.php');
}

// ============ جلب بيانات السلة ============
$cart_items = [];
$cart_total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    
    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']];
        $subtotal = $p['price'] * $qty;
        $cart_total += $subtotal;
        
        $cart_items[] = [
            'product'  => $p,
            'quantity' => $qty,
            'subtotal' => $subtotal
        ];
    }
}

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold"><i class="fas fa-shopping-cart text-gradient"></i> سلة المشتريات</h1>
    <?php if (!empty($cart_items)): ?>
        <a href="<?= BASE_URL ?>pages/cart.php?action=clear" 
           class="btn btn-outline-danger btn-sm"
           onclick="return confirm('هل أنت متأكد من تفريغ السلة؟');">
            <i class="fas fa-trash"></i> تفريغ السلة
        </a>
    <?php endif; ?>
</div>

<?php show_flash(); ?>

<?php if (empty($cart_items)): ?>
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
<h4 class="text-muted">سلتك فارغة</h4>
            <p class="text-muted">ابدأ التسوق الآن واستمتع بالمنتجات</p>
            <a href="<?= BASE_URL ?>pages/products.php" class="btn btn-primary mt-3">
                <i class="fas fa-shopping-bag"></i> تصفح المنتجات
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <!-- جدول السلة -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>المنتج</th>
                                        <th>السعر</th>
                                        <th>الكمية</th>
                                        <th>المجموع</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($item['product']['image']) && file_exists(dirname(FILE) . '/../assets/img/' . $item['product']['image'])): ?>
                                                    <img src="<?= BASE_URL ?>assets/img/<?= e($item['product']['image']) ?>" 
                                                         width="60" height="60" style="object-fit:cover;border-radius:8px;" class="me-3">
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?= e($item['product']['name']) ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= number_format($item['product']['price'], 2) ?> ريال</td>
                                        <td style="width:100px;">
                                            <input type="number" name="quantities[<?= $item['product']['id'] ?>]" 
                                                   value="<?= $item['quantity'] ?>" min="1" 
                                                   max="<?= $item['product']['stock'] ?>" 
                                                   class="form-control form-control-sm text-center">
                                        </td>
                                        <td class="fw-bold text-success"><?= number_format($item['subtotal'], 2) ?> ريال</td>
                                        <td>
                                            <a href="<?= BASE_URL ?>pages/cart.php?action=remove&id=<?= $item['product']['id'] ?>" 
                                               class="btn btn-sm btn-danger" onclick="return confirm('حذف هذا المنتج؟');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-sync"></i> تحديث السلة
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- ملخص الطلب -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">ملخص الطلب</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>عدد المنتجات:</span>
                        <strong><?= array_sum($_SESSION['cart']) ?> قطعة</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">الإجمالي:</span>
                        <span class="price-tag"><?= number_format($cart_total, 2) ?> ريال</span>
                    </div>
                    
                    <a href="<?= BASE_URL ?>pages/checkout.php" class="btn btn-primary w-100">
                        <i class="fas fa-credit-card"></i> إتمام الطلب
                    </a>
                    <a href="<?= BASE_URL ?>pages/products.php" class="btn btn-outline-primary w-100 mt-2">
                        <i class="fas fa-arrow-right"></i> متابعة التسوق
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>