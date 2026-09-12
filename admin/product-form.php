<?php
$page_title = 'إضافة/تعديل منتج';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';
require_admin();

// هل نحن في وضع التعديل؟
$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = $edit_id > 0;
$product = [
    'name'        => '',
    'description' => '',
    'price'       => '',
    'stock'       => '',
    'category_id' => '',
    'image'       => ''
];

if ($is_edit) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$edit_id]);
    $product = $stmt->fetch();
    if (!$product) {
        $_SESSION['error'] = "المنتج غير موجود";
        redirect('admin/products.php');
    }
}

// جلب التصنيفات
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = clean($_POST['name'] ?? '');
    $description = clean($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $stock       = (int)($_POST['stock'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $image_name  = $product['image'] ?? '';

    // Validation
    if (empty($name)) $errors[] = "اسم المنتج مطلوب";
    if (mb_strlen($name) > 150) $errors[] = "اسم المنتج طويل جداً (150 حرف كحد أقصى)";
    if ($price <= 0) $errors[] = "السعر يجب أن يكون أكبر من صفر";
    if ($stock < 0) $errors[] = "المخزون لا يمكن أن يكون سالباً";
    if ($category_id <= 0) $errors[] = "يجب اختيار تصنيف";

    // رفع الصورة
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "نوع الصورة غير مسموح (jpg, png, gif, webp فقط)";
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = "حجم الصورة يجب أن يكون أقل من 2 ميجابايت";
        } else {
            $new_image = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = dirname(FILE) . '/../assets/img/' . $new_image;
            
            // إنشاء مجلد إذا لم يكن موجوداً
            if (!is_dir(dirname($upload_path))) {
                mkdir(dirname($upload_path), 0755, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // حذف الصورة القديمة إن وجدت
                if (!empty($product['image'])) {
                    $old = dirname(FILE) . '/../assets/img/' . $product['image'];
                    if (file_exists($old)) unlink($old);
                }
                $image_name = $new_image;
            } else {
                $errors[] = "فشل رفع الصورة";
            }
        }
    }

    if (empty($errors)) {
        if ($is_edit) {
            $sql = "UPDATE products SET name=?, description=?, price=?, stock=?, category_id=?, image=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price, $stock, $category_id, $image_name, $edit_id]);
            $_SESSION['success'] = "تم تحديث المنتج بنجاح";
        } else {
            $sql = "INSERT INTO products (name, description, price, stock, category_id, image) VALUES (?,?,?,?,?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price, $stock, $category_id, $image_name]);
            $_SESSION['success'] = "تم إضافة المنتج بنجاح";
        }
        redirect('admin/products.php');
    }
}

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
        <i class="fas fa-<?= $is_edit ? 'edit' : 'plus' ?> text-gradient"></i>
        <?= $is_edit ? 'تعديل منتج' : 'إضافة منتج جديد' ?>
    </h2>
    <a href="<?= BASE_URL ?>admin/products.php" class="btn btn-outline-primary">
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
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">اسم المنتج *</label>
                        <input type="text" name="name" class="form-control" required
                               value="<?= e($_POST['name'] ?? $product['name']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="4"><?= e($_POST['description'] ?? $product['description']) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">السعر (ريال) *</label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0.01" required
                                   value="<?= e($_POST['price'] ?? $product['price']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">المخزون *</label>
                            <input type="number" name="stock" class="form-control" min="0" required
                                   value="<?= e($_POST['stock'] ?? $product['stock']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">التصنيف *</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- اختر --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" 
                                        <?= (($_POST['category_id'] ?? $product['category_id']) == $cat['id']) ? 'selected' : '' ?>>
                                        <?= e($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">صورة المنتج</label>
                        <?php if (!empty($product['image']) && file_exists(dirname(FILE) . '/../assets/img/' . $product['image'])): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL ?>assets/img/<?= e($product['image']) ?>" 
                                     class="img-fluid rounded" style="max-height:200px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">jpg, png, gif, webp — حتى 2MB</small>
                    </div>
                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $is_edit ? 'تحديث' : 'إضافة' ?>
            </button>
            <a href="<?= BASE_URL ?>admin/products.php" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</div>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>