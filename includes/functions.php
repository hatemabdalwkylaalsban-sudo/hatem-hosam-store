ا<?php
/**
 * دوال مساعدة عامة + دوال الحماية
 * متجر حاتم وحسام
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * حماية من XSS - تهريب المخرجات
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * تنظيف المدخلات
 */
function clean($input) {
    return trim(strip_tags($input ?? ''));
}

/**
 * التحقق من تسجيل الدخول
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * التحقق من أن المستخدم أدمن
 */
function is_admin() {
    return is_logged_in() && ($_SESSION['user_role'] ?? '') === 'admin';
}

/**
 * إعادة التوجيه
 */
function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit;
}

/**
 * منع الوصول لغير المسجلين
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['error'] = "يجب تسجيل الدخول أولاً";
        redirect('auth/login.php');
    }
}

/**
 * منع الوصول لغير الأدمن
 */
function require_admin() {
    if (!is_admin()) {
        $_SESSION['error'] = "ليس لديك صلاحية الوصول";
        redirect('index.php');
    }
}

/**
 * التحقق من أن المستخدم يملك الصفحة (حماية الملف الشخصي)
 * @param int $owner_id - معرف صاحب المورد (مثلاً user_id)
 */
function require_owner($owner_id) {
    if (!is_logged_in()) {
        $_SESSION['error'] = "يجب تسجيل الدخول أولاً";
        redirect('auth/login.php');
    }
    
    // إذا كان المستخدم أدمن، يُسمح له بالوصول
    if (is_admin()) {
        return;
    }
    
    // إذا لم يكن صاحب المورد، يُرفض
    if ((int)$_SESSION['user_id'] !== (int)$owner_id) {
        $_SESSION['error'] = "لا يمكنك الوصول لهذه الصفحة";
        redirect('index.php');
    }
}

/**
 * توليد رمز CSRF
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * التحقق من رمز CSRF
 */
function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * عرض رسائل الخطأ والنجاح
 */
function show_flash() {
    if (!empty($_SESSION['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show">';
        echo '<i class="fas fa-exclamation-circle"></i> ' . e($_SESSION['error']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        unset($_SESSION['error']);
    }
    if (!empty($_SESSION['success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show">';
        echo '<i class="fas fa-check-circle"></i> ' . e($_SESSION['success']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        unset($_SESSION['success']);
    }
}

/**
 * تسجيل نشاط المستخدم (اختياري - للتوثيق)
 */
function log_activity($action, $details = '') {
    // يمكن إضافة جدول logs لاحقاً
    error_log("[ACTIVITY] User {$_SESSION['user_id']}: $action - $details");
}