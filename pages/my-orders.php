<?php
$page_title = 'طلباتي';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';
require_login();

// جلب طلبات المستخدم
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

// جلب تفاصيل كل طلب
foreach ($orders as &$order) {
    $stmt2 = $pdo->prepare("SELECT oi.*, p.name AS product_name 
                            FROM order_items oi 
                            LEFT JOIN products p ON oi.product_id = p.id 
                            WHERE oi.order_id = ?");
    $stmt2->execute([$order['id']]);
    $order['items'] = $stmt2->fetchAll();
}
unset($order);

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<h1 class="fw-bold mb-4"><i class="fas fa-receipt text-gradient"></i> طلباتي</h1>

<?php show_flash(); ?>

<?php if (empty($orders)): ?>
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
            <i class="fas fa-shopping-bag fa-5x text-muted mb-3"></i>
            <h4 class="text-muted">لا توجد طلبات بعد</h4>
            <a href="<?= BASE_URL ?>pages/products.php" class="btn btn-primary mt-3">
                ابدأ التسوق
            </a>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <strong>طلب #<?= $order['id'] ?></strong>
                <small class="text-muted d-block"><?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></small>
            </div>
            <div>
                <?php
                $status_labels = [
                    'pending' => ['قيد المراجعة', 'warning'],
                    'paid'    => ['مدفوع', 'info'],
                    'shipped' => ['تم الشحن', 'primary'],
                    'done'    => ['مكتمل', 'success']
                ];
                $status = $status_labels[$order['status']] ?? ['غير معروف', 'secondary'];
                ?>
                <span class="badge bg-<?= $status[1] ?>"><?= $status[0] ?></span>
                <span class="price-tag ms-3"><?= number_format($order['total'], 2) ?> ريال</span>
            </div>
        </div>
        <div class="card-body">
            <h6 class="text-muted mb-2">المنتجات:</h6>
            <ul class="list-unstyled mb-0">
                <?php foreach ($order['items'] as $item): ?>
                    <li class="d-flex justify-content-between border-bottom py-2">
                        <span><?= e($item['product_name']) ?> × <?= $item['quantity'] ?></span>
                        <span><?= number_format($item['price'] * $item['quantity'], 2) ?> ريال</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>