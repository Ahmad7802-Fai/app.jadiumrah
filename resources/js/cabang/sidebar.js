document.addEventListener('DOMContentLoaded', () => {

    const toggle = document.getElementById('sidebarToggle')
    const body   = document.body

    if (!toggle) return

    toggle.addEventListener('click', () => {
        body.classList.toggle('sidebar-open')
    })

    // klik overlay untuk tutup
    body.addEventListener('click', (e) => {
        if (
            body.classList.contains('sidebar-open') &&
            !e.target.closest('#sidebar') &&
            !e.target.closest('#sidebarToggle')
        ) {
            body.classList.remove('sidebar-open')
        }
    })

})
