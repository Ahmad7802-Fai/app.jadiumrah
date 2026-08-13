document.addEventListener('change', async (e) => {
    const input = e.target.closest('.sa-toggle-input');
    if (!input) return;

    const toggle = input.closest('.sa-toggle');
    const url = input.dataset.url;
    const prev = !input.checked; // previous state

    toggle.classList.add('is-loading');

    try {
        const res = await fetch(url, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });

        if (!res.ok) throw new Error('Toggle failed');

        // SUCCESS → nothing to do (optimistic already applied)

    } catch (err) {
        // ❌ ROLLBACK
        input.checked = prev;
        toastError('Gagal mengubah status');
    } finally {
        toggle.classList.remove('is-loading');
    }
});
