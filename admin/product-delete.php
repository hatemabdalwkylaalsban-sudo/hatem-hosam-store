<?php
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // جلب اسم الصورة لحذفها من المجلد
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if ($product) {
        // حذف الصورة من المجلد
        if (!empty($product['image'])) {
            $img_path = dirname(FILE) . '/../assets/img/' . $product['image'];
            if (file_exists($img_path)) {
                unlink($img_path);
            }
        }
        
        // حذف المنتج من قاعدة البيانات
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "تم حذف المنتج بنجاح";
    } else {
        $_SESSION['error'] = "المنتج غير موجود";
    }
} else {
    $_SESSION['error'] = "معرف المنتج غير صالح";
}

redirect('admin/products.php');