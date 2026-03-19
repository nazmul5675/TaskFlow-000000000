/* ===================================================
   TaskFlow — Global Script
   =================================================== */

document.addEventListener('DOMContentLoaded', function () {

    /* ── Password visibility toggle ──────────────────
       Works for any button with [data-toggle-password]
       pointing to an input id via data-target="inputId"
    ─────────────────────────────────────────────────── */
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-target');
            var input    = document.getElementById(targetId);
            var iconEye  = btn.querySelector('.icon-eye');
            var iconEyeOff = btn.querySelector('.icon-eye-off');
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                if (iconEye)    iconEye.classList.add('hidden');
                if (iconEyeOff) iconEyeOff.classList.remove('hidden');
            } else {
                input.type = 'password';
                if (iconEye)    iconEye.classList.remove('hidden');
                if (iconEyeOff) iconEyeOff.classList.add('hidden');
            }
        });
    });

    /* ── Flash / Toast auto-hide ─────────────────────
       Finds #flash-toast, waits 3.5s, fades out.
    ─────────────────────────────────────────────────── */
    var toast = document.getElementById('flash-toast');
    if (toast) {
        setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            toast.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            setTimeout(function () {
                toast.remove();
            }, 550);
        }, 3500);
    }

    /* ── Delete confirmation (Link-based) ───────────
       Legacy: Any link with data-confirm will prompt.
    ─────────────────────────────────────────────────── */
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });
    
    /* ── Delete confirmation (Form-based) ───────────
       Intercepts submission of .delete-form
    ─────────────────────────────────────────────────── */
    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to delete this task? This cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

});
