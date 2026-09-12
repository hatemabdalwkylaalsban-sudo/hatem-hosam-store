<?php
$page_title = 'الملف الشخصي';
require_once dirname(__FILE__) . '/../config/db.php';
require_once dirname(__FILE__) . '/../includes/functions.php';

// ✅ حماية الصفحة - يجب تسجيل الدخول
require_login();

// ✅ جلب بيانات المستخدم الحالي فقط
$user_id = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "المستخدم غير موجود";
    redirect('auth/logout.php');
}

$errors = [];

// ============ معالجة النموذج ============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ✅ التحقق من CSRF Token
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = "طلب غير صالح، يرجى إعادة المحاولة";
    } else {
        $action = $_POST['action'] ?? '';

        // ============ الإجراء 1: تحديث البيانات الشخصية ============
        if ($action === 'update_profile') {
            $full_name = clean($_POST['full_name'] ?? '');
            $email     = clean($_POST['email'] ?? '');

            // Validation
            if (empty($full_name)) {
                $errors[] = "الاسم الكامل مطلوب";
            } elseif (mb_strlen($full_name) < 3) {
                $errors[] = "الاسم الكامل قصير جداً";
            } elseif (mb_strlen($full_name) > 100) {
                $errors[] = "الاسم الكامل طويل جداً";
            }

            if (empty($email)) {
                $errors[] = "البريد الإلكتروني مطلوب";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "البريد الإلكتروني غير صالح";
            }

            // التحقق من عدم تكرار البريد
            if (empty($errors)) {
                $check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $check->execute([$email, $user_id]);
                if ($check->fetch()) {
                    $errors[] = "البريد الإلكتروني مستخدم مسبقاً";
                }
            }

            if (empty($errors)) {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
                $stmt->execute([$full_name, $email, $user_id]);
                
                $_SESSION['user_name'] = $full_name;
                $_SESSION['success'] = "تم تحديث البيانات بنجاح";
                log_activity('profile_update', 'User updated profile info');
                redirect('pages/profile.php');
            }
        }

        // ============ الإجراء 2: تغيير كلمة المرور ============
        if ($action === 'change_password') {
            $current = $_POST['current_password'] ?? '';
            $new     = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            // Validation
            if (empty($current)) {
                $errors[] = "كلمة المرور الحالية مطلوبة";
            } elseif (!password_verify($current, $user['password_hash'])) {
                $errors[] = "كلمة المرور الحالية غير صحيحة";
            }

            if (empty($new)) {
                $errors[] = "كلمة المرور الجديدة مطلوبة";
            } elseif (strlen($new) < 8) {
                $errors[] = "كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل";
            } elseif (!preg_match('/[A-Z]/', $new)) {
                $errors[] = "كلمة المرور يجب أن تحتوي على حرف كبير واحد على الأقل";
            } elseif (!preg_match('/[a-z]/', $new)) {
                $errors[] = "كلمة المرور يجب أن تحتوي على حرف صغير واحد على الأقل";
            } elseif (!preg_match('/[0-9]/', $new)) {
                $errors[] = "كلمة المرور يجب أن تحتوي على رقم واحد على الأقل";
            }

            if ($new !== $confirm) {
                $errors[] = "كلمتا المرور الجديدتان غير متطابقتين";
            }

            if ($new === $current) {
                $errors[] = "كلمة المرور الجديدة يجب أن تختلف عن الحالية";
            }
if (empty($errors)) {
                $new_hash = password_hash($new, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$new_hash, $user_id]);
                
                $_SESSION['success'] = "تم تغيير كلمة المرور بنجاح";
                log_activity('password_change', 'User changed password');
                redirect('pages/profile.php');
            }
        }
    }
}

// ============ إحصائيات المستخدم ============
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$orders_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) FROM orders WHERE user_id = ? AND status != 'pending'");
$stmt->execute([$user_id]);
$total_spent = $stmt->fetchColumn();

require_once dirname(__FILE__) . '/../includes/header.php';
?>

<div class="row">
    <!-- بطاقة المستخدم -->
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body p-4">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-6x text-gradient"></i>
                </div>
                <h4 class="fw-bold mb-1"><?= e($user['full_name']) ?></h4>
                <p class="text-muted mb-2">@<?= e($user['username']) ?></p>
                
                <span class="badge bg-<?= $user['role'] === 'admin' ? 'warning' : 'info' ?> mb-3">
                    <i class="fas fa-<?= $user['role'] === 'admin' ? 'crown' : 'user' ?>"></i>
                    <?= $user['role'] === 'admin' ? 'مدير النظام' : 'مستخدم' ?>
                </span>

                <hr>

                <div class="text-start">
                    <p class="mb-2">
                        <i class="fas fa-envelope text-muted"></i>
                        <?= e($user['email']) ?>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-calendar text-muted"></i>
                        عضو منذ: <?= date('Y-m-d', strtotime($user['created_at'])) ?>
                    </p>
                </div>

                <hr>

                <!-- إحصائيات -->
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <h5 class="fw-bold text-gradient mb-0"><?= $orders_count ?></h5>
                        <small class="text-muted">طلبات</small>
                    </div>
                    <div class="col-6">
                        <h5 class="fw-bold text-gradient mb-0"><?= number_format($total_spent, 0) ?></h5>
                        <small class="text-muted">ريال</small>
                    </div>
                </div>

                <hr>

                <?php if (is_admin()): ?>
                <a href="<?= BASE_URL ?>admin/dashboard.php" class="btn btn-warning w-100">
                    <i class="fas fa-cog"></i> لوحة التحكم
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- نماذج التعديل -->
    <div class="col-md-8">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- نموذج البيانات الشخصية -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-user-edit text-gradient"></i> البيانات الشخصية
                </h5>
                
                <form method="POST"></form><input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" name="full_name" class="form-control" required
                                   value="<?= e($_POST['full_name'] ?? $user['full_name']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" required
                                   value="<?= e($_POST['email'] ?? $user['email']) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">اسم المستخدم</label>
                        <input type="text" class="form-control" value="<?= e($user['username']) ?>" disabled>
                        <small class="text-muted">لا يمكن تغيير اسم المستخدم</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ التغييرات
                    </button>
                </form>
            </div>
        </div>

        <!-- نموذج تغيير كلمة المرور -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-key text-gradient"></i> تغيير كلمة المرور
                </h5>
                
                <form method="POST" id="passwordForm">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور الحالية</label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="current" 
                                   class="form-control" required>
                            <button type="button" class="btn btn-outline-secondary" 
                                    onclick="togglePass('current')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">كلمة المرور الجديدة</label>
                            <div class="input-group">
                                <input type="password" name="new_password" id="newpass" 
                                       class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" 
                                        onclick="togglePass('newpass')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تأكيد كلمة المرور</label>
                            <div class="input-group">
                                <input type="password" name="confirm_password" id="confirm" 
                                       class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" 
                                        onclick="togglePass('confirm')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- متطلبات كلمة المرور -->
                    <div class="alert alert-light border small mb-3">
                        <strong>متطلبات كلمة المرور:</strong>
                        <ul class="mb-0 mt-1">
                            <li>8 أحرف على الأقل</li>
                            <li>حرف كبير واحد على الأقل (A-Z)</li>
                            <li>حرف صغير واحد على الأقل (a-z)</li>
                            <li>رقم واحد على الأقل (0-9)</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key"></i> تغيير كلمة المرور
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePass(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

<?php require_once dirname(__FILE__) . '/../includes/footer.php'; ?>