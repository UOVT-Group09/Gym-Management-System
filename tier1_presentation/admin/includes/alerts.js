document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.classList.add('hiding');
            alert.addEventListener('transitionend', function () { alert.remove(); }, { once: true });
        }, 3000);
    });
});
