(function () {

    const container = document.getElementById('sa-toast-container');
    if (!container) return;

    function showToast(type, message, timeout = 3500) {

        const toast = document.createElement('div');
        toast.className = `sa-toast sa-toast-${type}`;

        const icon = {
            success: '✓',
            error: '⚠️',
            warning: '⚠️',
            info: 'ℹ️'
        }[type] || 'ℹ️';

        toast.innerHTML = `
            <div class="sa-toast-icon">${icon}</div>
            <div class="sa-toast-message">${message}</div>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-6px)';
            setTimeout(() => toast.remove(), 200);
        }, timeout);
    }

    // Render queued toast (from Blade)
    if (window.__toastQueue) {
        window.__toastQueue.forEach(t =>
            showToast(t.type, t.message)
        );
    }

    // Expose global
    window.saToast = showToast;

})();
