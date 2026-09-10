<?php
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';

session_destroy();
session_start();
$_SESSION['success'] = "تم تسجيل الخروج بنجاح";
redirect('index.php');