<!-- التذييل -->
<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            
            <!-- عن المتجر -->
            <div class="col-md-4">
                <h5 class="text-gradient mb-3">
                    <i class="fas fa-store"></i> <?= SITE_NAME ?>
                </h5>
                <p class="text-muted">
                    متجر إلكتروني متكامل — مشروع مقرر تطوير تطبيقات الويب
                </p>
                <p class="text-muted mb-0">
                    <i class="fas fa-user-shield"></i> إعداد: <?= SITE_OWNERS ?>
                </p>
            </div>

            <!-- روابط سريعة -->
            <div class="col-md-4">
                <h5 class="mb-3"><i class="fas fa-link"></i> روابط سريعة</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>" class="text-white-50 text-decoration-none">
                            <i class="fas fa-angle-left"></i> الرئيسية
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>pages/products.php" class="text-white-50 text-decoration-none">
                            <i class="fas fa-angle-left"></i> المنتجات
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>pages/cart.php" class="text-white-50 text-decoration-none">
                            <i class="fas fa-angle-left"></i> السلة
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>pages/profile.php" class="text-white-50 text-decoration-none">
                            <i class="fas fa-angle-left"></i> الملف الشخصي
                        </a>
                    </li>
                </ul>
            </div>

            <!-- تواصل معنا -->
            <div class="col-md-4">
                <h5 class="mb-3"><i class="fas fa-headset"></i> تواصل معنا</h5>
                
                <!-- حاتم الصبان -->
                <div class="mb-4">
                    <p class="mb-2 text-white">
                        <i class="fas fa-user-circle"></i> <strong>حاتم الصبان</strong>
                    </p>
                    <a href="tel:771506470" class="text-white-50 text-decoration-none d-block mb-1">
                        <i class="fas fa-phone"></i> 771506470
                    </a>
                    <a href="https://wa.me/967771506470" target="_blank" class="text-success text-decoration-none d-block mb-1">
                        <i class="fab fa-whatsapp"></i> واتساب
                    </a>
                    <a href="https://t.me/hatem_alsabban" target="_blank" class="text-info text-decoration-none d-block mb-1">
                        <i class="fab fa-telegram"></i> تلجرام
                    </a>
                    <a href="https://instagram.com/hatem_alsabban" target="_blank" class="text-danger text-decoration-none d-block mb-1">
                        <i class="fab fa-instagram"></i> إنستغرام
                    </a>
                    <a href="https://facebook.com/hatem.alsabban" target="_blank" class="text-primary text-decoration-none d-block">
                        <i class="fab fa-facebook"></i> فيسبوك
                    </a>
                </div>

                <!-- حسام مرزح -->
                <div>
                    <p class="mb-2 text-white">
                        <i class="fas fa-user-circle"></i> <strong>حسام مرزح</strong>
                    </p>
                    <a href="tel:781607965" class="text-white-50 text-decoration-none d-block mb-1">
                        <i class="fas fa-phone"></i> 781607965
                        </a>
                    <a href="https://wa.me/967781607965" target="_blank" class="text-success text-decoration-none d-block">
                        <i class="fab fa-whatsapp"></i> واتساب
                    </a>
                </div>
            </div>
        </div>

        <hr class="bg-white-50 my-4">

        <div class="text-center text-white-50">
            <p class="mb-0 small">
                © <?= date('Y') ?> <?= SITE_NAME ?> — جميع الحقوق محفوظة
            </p>
            <p class="mb-0 small">
                مشروع مقرر تطوير تطبيقات الويب | تخصص الأمن السيبراني
            </p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>