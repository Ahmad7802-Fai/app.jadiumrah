import './bootstrap'


// Global SCSS (admin / agent shared)
import '../scss/app.scss'

// Alpine
import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()

/* =====================================================
   GLOBAL APP CONTEXT
===================================================== */

const APP = document.body.dataset.app || null

/* =====================================================
   🔥 GLOBAL CLICK GUARD (MODAL SAFE)
   - HARUS di luar DOMContentLoaded
   - Mencegah event modal bocor ke:
     Bootstrap / Alpine / Sidebar / Overlay
===================================================== */

document.addEventListener(
    'click',
    (e) => {
        if (e.target.closest('.modal')) {
            e.stopPropagation()
        }
    },
    true // ⬅️ capture phase (KUNCI UTAMA)
)

/* =====================================================
   DOM READY
===================================================== */

document.addEventListener('DOMContentLoaded', () => {

    /* =================================================
       CABANG / ADMIN ONLY LOGIC
       (AGENT TIDAK DISENTUH)
    ================================================= */

    if (APP === 'cabang') {

        const toggleBtn = document.getElementById('btnSidebarToggle')
        const overlay   = document.getElementById('sidebar-overlay')

        toggleBtn?.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-open')
        })

        overlay?.addEventListener('click', () => {
            document.body.classList.remove('sidebar-open')
        })

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                document.body.classList.remove('sidebar-open')
            }
        })

        // Auto close sidebar after click menu (mobile)
        document.querySelectorAll('.js-sidebar-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    document.body.classList.remove('sidebar-open')
                }
            })
        })
    }

    /* =================================================
       AGENT AREA
       (TIDAK ADA LOGIC KHUSUS DI SINI)
       Modal custom pakai inline JS (onclick)
    ================================================= */

})

// import './bootstrap'
// import 'bootstrap/dist/js/bootstrap.bundle.min.js'

// // Global
// import '../scss/app.scss'


// // Alpine
// import Alpine from 'alpinejs'
// window.Alpine = Alpine
// Alpine.start()

// document.addEventListener('DOMContentLoaded', () => {
//     if (document.body.dataset.app !== 'cabang') return

//     const toggleBtn = document.getElementById('btnSidebarToggle')
//     const overlay   = document.getElementById('sidebar-overlay')

//     toggleBtn?.addEventListener('click', () => {
//         document.body.classList.toggle('sidebar-open')
//     })

//     overlay?.addEventListener('click', () => {
//         document.body.classList.remove('sidebar-open')
//     })

//     window.addEventListener('resize', () => {
//         if (window.innerWidth >= 992) {
//             document.body.classList.remove('sidebar-open')
//         }
//     })

//     // 🔥 AUTO CLOSE SIDEBAR AFTER CLICK MENU (MOBILE)
//     document.querySelectorAll('.js-sidebar-link').forEach(link => {
//         link.addEventListener('click', () => {
//             if (window.innerWidth < 992) {
//                 document.body.classList.remove('sidebar-open')
//             }
//         })
//     })
// })

// import './bootstrap'
// import 'bootstrap/dist/js/bootstrap.bundle.min.js'

// // Global (superadmin, dsb)
// import '../scss/app.scss'
// import './superadmin/toast'
// import './cabang/sidebar'

// // Alpine
// import Alpine from 'alpinejs'
// window.Alpine = Alpine
// Alpine.start()

// /* =========================================================
//    CABANG SIDEBAR TOGGLE (MOBILE ONLY)
// ========================================================= */
// document.addEventListener('DOMContentLoaded', () => {

//     // 🚫 HANYA JALAN DI CABANG
//     if (document.body.dataset.app !== 'cabang') return

//     const toggleBtn = document.getElementById('btnSidebarToggle')
//     const overlay   = document.getElementById('sidebar-overlay')

//     // Toggle sidebar
//     toggleBtn?.addEventListener('click', () => {
//         document.body.classList.toggle('sidebar-open')
//     })

//     // Close on overlay click
//     overlay?.addEventListener('click', () => {
//         document.body.classList.remove('sidebar-open')
//     })

//     // Auto close sidebar when resize ke desktop
//     window.addEventListener('resize', () => {
//         if (window.innerWidth >= 992) {
//             document.body.classList.remove('sidebar-open')
//         }
//     })
// })
