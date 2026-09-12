<?php
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // التحقق من وجود منتجات مرتبطة
    $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $check->execute([$id]);
    $count = $check->fetchColumn();

    if ($count > 0) {
        // فصل المنتجات عن التصنيف قبل الحذف
        $update = $pdo->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?");
        $update->execute([$id]);
    }

    // حذف التصنيف
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "تم حذف التصنيف بنجاح" . ($count > 0 ? " (تم فصل $count منتج)" : "");
    } else {
        $_SESSION['error'] = "التصنيف غير موجود";
    }
} else {
    $_SESSION['error'] = "معرف التصنيف غير صالح";
}

redirect('admin/categories.php');