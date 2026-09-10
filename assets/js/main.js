// تفعيل Tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) {
        return new bootstrap.Tooltip(el);
    });
});

// تأكيد الحذف
function confirmDelete(msg) {
    return confirm(msg || 'هل أنت متأكد من الحذف؟');
}

// إخفاء التنبيهات تلقائياً
setTimeout(function() {
    document.querySelectorAll('.alert').forEach(function(el) {
        el.style.transition = 'opacity 0.5s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
    });
}, 4000);