<?php
$page_title = 'إتمام الطلب';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';

// يجب تسجيل الدخول
require_login();

// إذا كانت السلة فارغة
if (empty($_SESSION['cart'])) {
    $_SESSION['error'] = "السلة فارغة";
    redirect('pages/cart.php');
}

// جلب بيانات السلة
$ids = array_keys($_SESSION['cart']);
$placeholders = str_repeat('?,', count($ids) - 1) . '?';
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll();

$cart_items = [];
$cart_total = 0;

foreach ($products as $p) {
    $qty = $_SESSION['cart'][$p['id']];
    $subtotal = $p['price'] * $qty;
    $cart_total += $subtotal;
    
    // التحقق من المخزون
    if ($qty > $p['stock']) {
        $_SESSION['error'] = "المنتج {$p['name']} غير متوفر بالكمية المطلوبة";
        redirect('pages/cart.php');
    }
    
    $cart_items[] = [
        'product'  => $p,
        'quantity' => $qty,
        'subtotal' => $subtotal
    ];
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = clean($_POST['address'] ?? '');
    $phone   = clean($_POST['phone'] ?? '');
    $notes   = clean($_POST['notes'] ?? '');

    // Validation
    if (empty($address)) $errors[] = "العنوان مطلوب";
    if (empty($phone)) $errors[] = "رقم الهاتف مطلوب";
    elseif (!preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) $errors[] = "رقم الهاتف غير صالح";

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // 1. إنشاء الطلب
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $cart_total]);
            $order_id = $pdo->lastInsertId();

            // 2. إضافة تفاصيل الطلب
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart_items as $item) {
                $stmt->execute([
                    $order_id,
                    $item['product']['id'],
                    $item['quantity'],
                    $item['product']['price']
                ]);

                // 3. خصم من المخزون
                $update = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $update->execute([$item['quantity'], $item['product']['id']]);
            }

            $pdo->commit();

            // تفريغ السلة
            $_SESSION['cart'] = [];
            $_SESSION['success'] = "تم إنشاء طلبك بنجاح! رقم الطلب: #$order_id";
            redirect('pages/my-orders.php');

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "حدث خطأ أثناء إنشاء الطلب: " . $e->getMessage();
        }
    }
}

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<h1 class="fw-bold mb-4"><i class="fas fa-credit-card text-gradient"></i> إتمام الطلب</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="row g-4">
        <!-- بيانات الشحن -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fas fa-truck"></i> بيانات الشحن</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">العنوان الكامل *</label>
                        <textarea name="address" class="form-control" rows="3" required 
                                  placeholder="المدينة، الحي، الشارع، رقم المبنى"><?= e($_POST['address'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم الهاتف *</label>
                        <input type="text" name="phone" class="form-control" required 
                               placeholder="مثال: 777123456" value="<?= e($_POST['phone'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ملاحظات إضافية (اختياري)</label>
                        <textarea name="notes" class="form-control" rows="2"><?= e($_POST['notes'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- ملخص الطلب -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="fas fa-receipt"></i> ملخص الطلب</h5>
                    
                    <?php foreach ($cart_items as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= e($item['product']['name']) ?> × <?= $item['quantity'] ?></span>
                            <span><?= number_format($item['subtotal'], 2) ?> ريال</span>
                        </div>
                    <?php endforeach; ?>
                    
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">الإجمالي:</span>
                        <span class="price-tag"><?= number_format($cart_total, 2) ?> ريال</span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-check-circle"></i> تأكيد الطلب
                    </button>
                    <a href="<?= BASE_URL ?>pages/cart.php" class="btn btn-outline-secondary w-100 mt-2">
                        رجوع للسلة
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>