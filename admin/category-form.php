<?php
$page_title = 'إضافة/تعديل تصنيف';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';
require_admin();

$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = $edit_id > 0;
$category = ['name' => ''];

if ($is_edit) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$edit_id]);
    $category = $stmt->fetch();
    if (!$category) {
        $_SESSION['error'] = "التصنيف غير موجود";
        redirect('admin/categories.php');
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name'] ?? '');

    // Validation
    if (empty($name)) {
        $errors[] = "اسم التصنيف مطلوب";
    } elseif (mb_strlen($name) < 2) {
        $errors[] = "اسم التصنيف قصير جداً";
    } elseif (mb_strlen($name) > 100) {
        $errors[] = "اسم التصنيف طويل جداً";
    } else {
        // التحقق من عدم التكرار
        $check = $pdo->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
        $check->execute([$name, $edit_id]);
        if ($check->fetch()) {
            $errors[] = "اسم التصنيف موجود مسبقاً";
        }
    }

    if (empty($errors)) {
        if ($is_edit) {
            $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->execute([$name, $edit_id]);
            $_SESSION['success'] = "تم تحديث التصنيف بنجاح";
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            $_SESSION['success'] = "تم إضافة التصنيف بنجاح";
        }
        redirect('admin/categories.php');
    }
}

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
        <i class="fas fa-<?= $is_edit ? 'edit' : 'plus' ?> text-gradient"></i>
        <?= $is_edit ? 'تعديل تصنيف' : 'إضافة تصنيف جديد' ?>
    </h2>
    <a href="<?= BASE_URL ?>admin/categories.php" class="btn btn-outline-primary">
        <i class="fas fa-arrow-right"></i> رجوع للقائمة
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">اسم التصنيف *</label>
                <input type="text" name="name" class="form-control" required autofocus
                       value="<?= e($_POST['name'] ?? $category['name']) ?>"
                       placeholder="مثال: إلكترونيات، ملابس، كتب...">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $is_edit ? 'تحديث' : 'إضافة' ?>
            </button>
            <a href="<?= BASE_URL ?>admin/categories.php" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</div>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>