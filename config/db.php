<?php
/**
 * ملف الاتصال بقاعدة البيانات - متجر حاتم وحسام
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'hatem_hosam_store');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// رابط الموقع
define('BASE_URL', 'http://localhost/hatem_hosam_store/');

// اسم المتجر
define('SITE_NAME', 'متجر حاتم وحسام');
define('SITE_OWNERS', 'حاتم الصبان و حسام مرزح');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}