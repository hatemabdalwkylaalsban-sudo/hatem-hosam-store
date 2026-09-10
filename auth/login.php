<?php
$page_title = 'تسجيل الدخول';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';

// إذا كان المستخدم مسجل دخول، نعيده للرئيسية
if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // التحقق من المدخلات
    if (empty($username) || empty($password)) {
        $errors[] = "الرجاء إدخال اسم المستخدم وكلمة المرور";
    } else {
        // استخدام Prepared Statement لمنع SQL Injection
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // نجح تسجيل الدخول
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username']  = $user['username'];

            $_SESSION['success'] = "أهلاً بك " . $user['full_name'];

            // التوجيه حسب الدور
            if ($user['role'] === 'admin') {
                redirect('admin/dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            $errors[] = "اسم المستخدم أو كلمة المرور غير صحيحة";
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
                    <i class="fas fa-sign-in-alt text-gradient"></i> تسجيل الدخول
                </h3>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $err): ?>
                            <div><?= e($err) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">اسم المستخدم أو البريد</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?= e($_POST['username'] ?? '') ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt"></i> دخول
                    </button>
                </form>

                <hr class="my-4">
                <p class="text-center mb-0">
                    ليس لديك حساب؟
                    <a href="<?= BASE_URL ?>auth/register.php" class="text-gradient fw-bold">إنشاء حساب</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>