<?php
/**
 * دوال مساعدة عامة + دوال الحماية
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function clean($input) {
    return trim(strip_tags($input ?? ''));
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return is_logged_in() && ($_SESSION['user_role'] ?? '') === 'admin';
}

function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit;
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['error'] = "يجب تسجيل الدخول أولاً";
        redirect('auth/login.php');
    }
}

function require_admin() {
    if (!is_admin()) {
        $_SESSION['error'] = "ليس لديك صلاحية الوصول";
        redirect('index.php');
    }
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function show_flash() {
    if (!empty($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . e($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    if (!empty($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . e($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
}