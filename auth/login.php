<?php
$page_title = 'تسجيل الدخول';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';

// إذا كان المستخدم مسجل دخول، نعيده للرئيسية
if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];
$username_input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_input = clean($_POST['username'] ?? '');
    $password       = $_POST['password'] ?? '';

    // ============ التحقق من المدخلات ============
    if (empty($username_input)) {
        $errors[] = "اسم المستخدم مطلوب";
    }
    if (empty($password)) {
        $errors[] = "كلمة المرور مطلوبة";
    }

    if (empty($errors)) {
        // ✅ حماية من SQL Injection باستخدام Prepared Statements
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username_input, $username_input]);
        $user = $stmt->fetch();

        // ✅ حماية كلمة المرور باستخدام password_verify
        if ($user && password_verify($password, $user['password_hash'])) {
            
            // نجح تسجيل الدخول
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['login_time'] = time();

            // تسجيل الدخول في سجل النشاط
            log_activity('login', 'User logged in successfully');

            $_SESSION['success'] = "أهلاً بك " . $user['full_name'];

            // التوجيه حسب الدور
            if ($user['role'] === 'admin') {
                redirect('admin/dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            // رسالة خطأ عامة (لا نكشف هل المستخدم موجود أم لا - حماية أمنية)
            $errors[] = "اسم المستخدم أو كلمة المرور غير صحيحة";
            log_activity('login_failed', "Failed login attempt for: $username_input");
        }
    }
}

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-user-shield fa-3x text-gradient mb-2"></i>
                    <h3 class="fw-bold mb-0">تسجيل الدخول</h3>
                    <p class="text-muted small">مرحباً بك في <?= SITE_NAME ?></p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= e($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-user"></i> اسم المستخدم أو البريد
                        </label>
                        <input type="text" name="username" class="form-control form-control-lg" 
                               value="<?= e($username_input) ?>" required autofocus
                               placeholder="اكتب اسم المستخدم">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> كلمة المرور
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" 
                                   class="form-control form-control-lg" required
                                   placeholder="اكتب كلمة المرور">
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="remember">
                        <label class="form-check-label small" for="remember">
                            تذكرني على هذا الجهاز
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="fas fa-sign-in-alt"></i> دخول
                    </button>
                </form>

                <hr class="my-4">
                
                <div class="text-center">
                    <p class="mb-0">
                        ليس لديك حساب؟
                        <a href="<?= BASE_URL ?>auth/register.php" class="text-gradient fw-bold">
                            إنشاء حساب جديد
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- تنبيه أمني -->
        <div class="alert alert-info mt-3 small">
            <i class="fas fa-shield-alt"></i>
            <strong>ملاحظة أمنية:</strong>
            هذه الصفحة محمية من SQL Injection و XSS.
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passInput = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    
    if (passInput.type === 'password') {
        passInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>