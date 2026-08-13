export const PNRModals = {

    hideAll() {
        document.querySelectorAll('[data-pnr-modal]').forEach(m => {
            m.classList.add('hidden');
        });
        document.body.classList.remove('overflow-hidden');
    },

    open(id) {
        this.hideAll();
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    },

    close(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
};

/* =============================
 | GLOBAL ACCESS FOR BLADE
 ============================= */
window.openClientModal   = () => PNRModals.open('clientModal');
window.openPricingModal  = () => PNRModals.open('pricingModal');
window.openSectorDrawer  = () => PNRModals.open('sectorDrawer');

window.closeClientModal  = () => PNRModals.close('clientModal');
window.closePricingModal = () => PNRModals.close('pricingModal');
window.closeSectorDrawer = () => PNRModals.close('sectorDrawer');
