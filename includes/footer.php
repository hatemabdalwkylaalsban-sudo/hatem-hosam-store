</div>
</main>

<!-- التذييل -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <h5 class="text-gradient mb-3"><?= SITE_NAME ?></h5>
        <p class="mb-2"><i class="fas fa-user-shield"></i> إعداد: <?= SITE_OWNERS ?></p>
        <p class="mb-2 text-muted">مشروع مقرر تطوير تطبيقات الويب</p>
        <div class="mt-3">
            <a href="#" class="text-white mx-2"><i class="fab fa-github fa-lg"></i></a>
            <a href="#" class="text-white mx-2"><i class="fab fa-linkedin fa-lg"></i></a>
            <a href="#" class="text-white mx-2"><i class="fas fa-envelope fa-lg"></i></a>
        </div>
        <hr class="bg-white">
        <p class="mb-0 text-muted small">© <?= date('Y') ?> جميع الحقوق محفوظة</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>