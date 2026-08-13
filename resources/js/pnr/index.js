import { PNRState } from './state';
import './modals';
import { submitPNR } from './submit';

window.PNRState = PNRState;
window.submitPNR = submitPNR;

document.addEventListener('DOMContentLoaded', () => {
    if (!document.body.dataset.page?.startsWith('ticket-pnr')) return;

    document.querySelectorAll('[data-pnr-modal]').forEach(m =>
        m.classList.add('hidden')
    );

    PNRState.render();
});
// import { PNRState } from './state'
// import './modals'
// import './submit'

// // expose ke Blade
// window.PNRState = PNRState

// document.addEventListener('DOMContentLoaded', () => {
//     // HARD GUARD: hanya jalan di halaman PNR
//     if (!document.body.dataset.page?.startsWith('ticket-pnr')) return

//     // pastikan semua modal hidden
//     document.querySelectorAll('[data-pnr-modal]').forEach(m => {
//         m.classList.add('hidden')
//     })

//     // render awal (kosong)
//     PNRState.render()
// })
