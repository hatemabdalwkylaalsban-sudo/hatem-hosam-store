<?php
/**
 * تسجيل الخروج - متجر حاتم وحسام
 * يحذف جميع بيانات الجلسة بشكل آمن
 */

require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';

// تسجيل النشاط قبل الخروج
if (is_logged_in()) {
    log_activity('logout', 'User logged out');
}

// ============ تدمير الجلسة بشكل آمن ============

// 1. حذف متغيرات الجلسة
$_SESSION = [];

// 2. حذف كوكيز الجلسة من المتصفح
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. تدمير الجلسة على السيرفر
session_destroy();

// 4. بدء جلسة جديدة لإظهار رسالة النجاح
session_start();
$_SESSION['success'] = "تم تسجيل الخروج بنجاح";

// 5. التوجيه للصفحة الرئيسية
redirect('index.php');