<script>
document.addEventListener("DOMContentLoaded", function () {

    const paketSelect = document.getElementById("paket-master");
    const tipeSelect = document.getElementById("tipe-kamar");
    const hargaDefault = document.getElementById("harga-default");
    const hargaDiskon = document.getElementById("harga-setelah-diskon");
    const diskonInput = document.querySelector("input[name='diskon']");

    function getSelectedTipe() {
        return tipeSelect ? tipeSelect.value.toLowerCase() : "{{ strtolower($item->tipe_kamar ?? '') }}";
    }

    function updateHarga() {
        const opt = paketSelect.options[paketSelect.selectedIndex];
        if (!opt) {
            hargaDefault.value = 0;
            hargaDiskon.value = 0;
            return;
        }

        const tipe = getSelectedTipe(); // 'quad' | 'triple' | 'double'
        let harga = 0;

        if (tipe === 'quad') harga = parseInt(opt.dataset.quad) || 0;
        else if (tipe === 'triple') harga = parseInt(opt.dataset.triple) || 0;
        else if (tipe === 'double') harga = parseInt(opt.dataset.double) || 0;
        else harga = parseInt(opt.dataset.double) || 0; // fallback

        const diskon = parseInt(diskonInput.value) || 0;
        hargaDefault.value = harga;
        hargaDiskon.value = Math.max(0, harga - diskon);
    }

    // Events
    if (paketSelect) paketSelect.addEventListener("change", updateHarga);
    if (tipeSelect) tipeSelect.addEventListener("change", updateHarga);
    if (diskonInput) diskonInput.addEventListener("input", updateHarga);

    // init on load
    updateHarga();
});
</script>
