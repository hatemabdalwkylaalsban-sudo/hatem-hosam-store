<?php
$page_title = 'إدارة المنتجات';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';
require_admin();

// جلب جميع المنتجات
$products = $pdo->query("SELECT p.*, c.name AS category_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         ORDER BY p.id DESC")->fetchAll();

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
        <i class="fas fa-box text-gradient"></i> إدارة المنتجات
    </h2>
    <a href="<?= BASE_URL ?>admin/product-form.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة منتج جديد
    </a>
</div>

<?php show_flash(); ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>الاسم</th>
                        <th>التصنيف</th>
                        <th>السعر</th>
                        <th>المخزون</th>
                        <th>التاريخ</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">
                            لا توجد منتجات. أضف منتجاً جديداً!
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <?php if (!empty($p['image']) && file_exists(dirname(FILE) . '/../assets/img/' . $p['image'])): ?>
                                    <img src="<?= BASE_URL ?>assets/img/<?= e($p['image']) ?>" 
                                         alt="" width="50" height="50" style="object-fit:cover;border-radius:8px;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="width:50px;height:50px;border-radius:8px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold"><?= e($p['name']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= e($p['category_name'] ?? 'بدون تصنيف') ?></span></td>
                            <td class="text-success fw-bold"><?= number_format($p['price'], 2) ?> ريال</td>
                            <td>
                                <?php if ($p['stock'] > 10): ?>
                                    <span class="badge bg-success"><?= $p['stock'] ?></span>
                                <?php elseif ($p['stock'] > 0): ?>
                                    <span class="badge bg-warning text-dark"><?= $p['stock'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger">نفذ</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= date('Y-m-d', strtotime($p['created_at'])) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>admin/product-form.php?id=<?= $p['id'] ?>"
                                class="btn btn-sm btn-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= BASE_URL ?>admin/product-delete.php?id=<?= $p['id'] ?>" 
                                   class="btn btn-sm btn-danger" title="حذف"
                                   onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>