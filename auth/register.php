<?php
$page_title = 'إنشاء حساب جديد';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';

// إذا كان المستخدم مسجل دخول، نعيده للرئيسية
if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استقبال البيانات
    $username  = clean($_POST['username'] ?? '');
    $full_name = clean($_POST['full_name'] ?? '');
    $email     = clean($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    // ============ التحقق من المدخلات (Validation) ============
    
    // 1. اسم المستخدم
    if (empty($username)) {
        $errors[] = "اسم المستخدم مطلوب";
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = "اسم المستخدم يجب أن يكون بين 3 و 50 حرفاً";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "اسم المستخدم يجب أن يحتوي على أحرف إنجليزية وأرقام فقط";
    }

    // 2. الاسم الكامل
    if (empty($full_name)) {
        $errors[] = "الاسم الكامل مطلوب";
    } elseif (strlen($full_name) < 3) {
        $errors[] = "الاسم الكامل قصير جداً";
    }

    // 3. البريد الإلكتروني
    if (empty($email)) {
        $errors[] = "البريد الإلكتروني مطلوب";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "البريد الإلكتروني غير صالح";
    }

    // 4. كلمة المرور
    if (empty($password)) {
        $errors[] = "كلمة المرور مطلوبة";
    } elseif (strlen($password) < 8) {
        $errors[] = "كلمة المرور يجب أن تكون 8 أحرف على الأقل";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "كلمة المرور يجب أن تحتوي على حرف كبير واحد على الأقل";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = "كلمة المرور يجب أن تحتوي على رقم واحد على الأقل";
    }

    // 5. تأكيد كلمة المرور
    if ($password !== $confirm) {
        $errors[] = "كلمتا المرور غير متطابقتين";
    }

    // ============ التحقق من عدم تكرار البيانات ============
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "اسم المستخدم أو البريد الإلكتروني مستخدم مسبقاً";
        }
    }

    // ============ إدخال البيانات ============
    if (empty($errors)) {
        // حماية كلمة المرور باستخدام password_hash
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, full_name, email, password_hash, role) 
                               VALUES (?, ?, ?, ?, 'user')");
        
        if ($stmt->execute([$username, $full_name, $email, $hash])) {
            $_SESSION['success'] = "تم إنشاء الحساب بنجاح! يمكنك تسجيل الدخول الآن";
            redirect('auth/login.php');
        } else {
            $errors[] = "حدث خطأ أثناء إنشاء الحساب";
        }
    }
}

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-body p-4">
                <h3 class="text-center mb-4 fw-bold">
                    <i class="fas fa-user-plus text-gradient"></i> إنشاء حساب جديد
                </h3>

                <!-- عرض الأخطاء -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= e($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
<form method="POST" id="registerForm">
                    <div class="mb-3">
                        <label class="form-label">الاسم الكامل</label>
                        <input type="text" name="full_name" class="form-control" 
                               value="<?= e($_POST['full_name'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">اسم المستخدم</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?= e($_POST['username'] ?? '') ?>" required>
                        <small class="text-muted">أحرف إنجليزية وأرقام فقط</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= e($_POST['email'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <small class="text-muted">8 أحرف على الأقل، مع حرف كبير ورقم</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">تأكيد كلمة المرور</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        <small id="matchMsg" class="text-muted"></small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-user-plus"></i> إنشاء الحساب
                    </button>
                </form>

                <hr class="my-4">
                <p class="text-center mb-0">
                    لديك حساب بالفعل؟
                    <a href="<?= BASE_URL ?>auth/login.php" class="text-gradient fw-bold">تسجيل الدخول</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// التحقق الفوري من تطابق كلمات المرور
document.getElementById('confirm_password').addEventListener('input', function() {
    const pass = document.getElementById('password').value;
    const confirm = this.value;
    const msg = document.getElementById('matchMsg');
    
    if (confirm.length === 0) {
        msg.textContent = '';
        return;
    }
    
    if (pass === confirm) {
        msg.textContent = '✅ كلمتا المرور متطابقتان';
        msg.className = 'text-success small';
    } else {
        msg.textContent = '❌ كلمتا المرور غير متطابقتين';
        msg.className = 'text-danger small';
    }
});
</script>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>
