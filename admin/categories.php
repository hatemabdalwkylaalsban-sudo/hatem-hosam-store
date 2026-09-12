<?php
$page_title = 'إدارة التصنيفات';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';
require_admin();

// جلب التصنيفات مع عدد المنتجات
$categories = $pdo->query("SELECT c.*, 
                           (SELECT COUNT(*) FROM products WHERE category_id = c.id) AS product_count
                           FROM categories c 
                           ORDER BY c.id DESC")->fetchAll();

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
        <i class="fas fa-tags text-gradient"></i> إدارة التصنيفات
    </h2>
    <a href="<?= BASE_URL ?>admin/category-form.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة تصنيف جديد
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
                        <th>اسم التصنيف</th>
                        <th>عدد المنتجات</th>
                        <th>التاريخ</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">
                            لا توجد تصنيفات. أضف تصنيفاً جديداً!
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $i => $c): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-bold"><?= e($c['name']) ?></td>
                            <td>
                                <span class="badge bg-info"><?= $c['product_count'] ?> منتج</span>
                            </td>
                            <td class="text-muted small"><?= date('Y-m-d', strtotime($c['created_at'])) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>admin/category-form.php?id=<?= $c['id'] ?>" 
                                   class="btn btn-sm btn-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= BASE_URL ?>admin/category-delete.php?id=<?= $c['id'] ?>" 
                                   class="btn btn-sm btn-danger" title="حذف"
                                   onclick="return confirm('هل أنت متأكد من حذف هذا التصنيف؟ سيتم فصل المنتجات عنه.');">
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