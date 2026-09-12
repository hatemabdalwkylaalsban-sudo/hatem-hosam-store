# 🚀 دليل النشر - متجر حاتم وحسام

دليل شامل لنشر المشروع على أي استضافة.

---

## 📋 المتطلبات

- PHP 7.4+ (يفضل 8.0+)
- MySQL 5.7+
- Apache مع mod_rewrite
- مساحة تخزين: 50MB على الأقل

---

## 🎯 الخيار 1: النشر على InfinityFree (مجاني)

### الخطوة 1: إنشاء حساب

1. اذهب إلى [infinityfree.com](https://infinityfree.com)
2. اضغط Sign Up
3. أنشئ حساباً بالبريد الإلكتروني

### الخطوة 2: إنشاء موقع جديد

1. من لوحة التحكم، اضغط Create Account
2. اختر Free Subdomain (مثل hatem-hosam.gt.tc)
3. انتظر حتى يتم إنشاء الموقع (2-5 دقائق)

### الخطوة 3: إنشاء قاعدة بيانات

1. افتح Control Panel للموقع
2. اذهب إلى MySQL Databases
3. اضغط Create Database
4. احفظ البيانات:
   - Database Name: epiz_xxxxxxx_hatem_store
   - Username: epiz_xxxxxxx
   - Password: (الذي اخترته)
   - Host: sqlXXX.epizy.com

### الخطوة 4: رفع الملفات

1. افتح Online File Manager أو استخدم FTP
2. اذهب إلى مجلد htdocs/
3. ارفع كل ملفات المشروع (ما عدا install.php)

### الخطوة 5: تعديل إعدادات قاعدة البيانات

افتح ملف config/db.php وعدّل:

`php
define('DB_HOST', 'sqlXXX.epizy.com');  // من لوحة التحكم
define('DB_NAME', 'epiz_xxxxxxx_hatem_store');
define('DB_USER', 'epiz_xxxxxxx');
define('DB_PASS', 'كلمة المرور الخاصة بك');