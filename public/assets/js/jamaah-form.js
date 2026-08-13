document.addEventListener('DOMContentLoaded', () => {

    /* =====================================================
     | ELEMENTS
     ===================================================== */
    const keberangkatan = document.getElementById('keberangkatan');
    const paketNama     = document.getElementById('nama-paket');
    const paketId       = document.getElementById('id_paket_master');
    const tipeKamar     = document.getElementById('tipe-kamar');
    const hargaView     = document.getElementById('harga-view');
    const hargaHidden   = document.getElementById('harga_default');
    const diskonInput   = document.querySelector('input[name="diskon"]');
    const tipeKamarHidden = document.getElementById('tipe-kamar-hidden');

    const tglLahir  = document.getElementById('tgl-lahir');
    const usiaInput = document.getElementById('usia');

    const isEdit     = document.getElementById('is-edit')?.value === '1';
    const hasPayment = document.getElementById('has-payment')?.value === '1';

    if (!keberangkatan || !paketNama || !paketId) return;

    /* =====================================================
     | STATE
     ===================================================== */
    let harga = { quad: 0, triple: 0, double: 0 };
    let isHargaLocked = hasPayment;

    /* =====================================================
     | HELPERS
     ===================================================== */
    const formatRupiah = (v) =>
        v && Number(v) > 0
            ? 'Rp ' + Number(v).toLocaleString('id-ID')
            : '';

    const renderHargaFromPaket = () => {
        if (isHargaLocked) return;

        const h = harga[tipeKamar?.value] || 0;
        hargaView.value   = formatRupiah(h);
        hargaHidden.value = h;
    };

    const renderHargaFromDb = () => {
        const h = Number(hargaHidden?.value || 0);
        hargaView.value = formatRupiah(h);
    };

    const hitungUsia = (tgl) => {
        if (!tgl) return '';
        const today = new Date();
        const birth = new Date(tgl);

        let usia = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();

        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) usia--;
        return usia + ' Tahun';
    };

    const lockField = (el) => {
        if (!el) return;
        el.setAttribute('disabled', 'disabled');
        el.classList.add('bg-light');
    };

    /* =====================================================
     | LOAD PAKET (ENDPOINT NETRAL)
     ===================================================== */
    async function loadPaket(idKeberangkatan) {

    paketNama.value = 'Memuat paket...';

    try {
        const res = await fetch(`/keberangkatan-paket/${idKeberangkatan}`, {
            headers: { 'Accept': 'application/json' }
        });

        if (!res.ok) throw new Error('Fetch gagal');

        const data = await res.json();

        if (!data.paket) {
            paketNama.value = 'Paket belum diset';
            harga = { quad: 0, triple: 0, double: 0 };
            renderHargaFromPaket();
            return;
        }

        // ===============================
        // SET PAKET
        // ===============================
        paketNama.value = data.paket.nama_paket;
        paketId.value   = data.paket.id;

        harga = {
            quad:   Number(data.paket.harga_quad || 0),
            triple: Number(data.paket.harga_triple || 0),
            double: Number(data.paket.harga_double || 0),
        };

        // ===============================
        // 🔥 SYNC TIPE KAMAR (INI PENTING)
        // ===============================
        if (tipeKamarHidden && tipeKamar?.value) {
            tipeKamarHidden.value = tipeKamar.value;
        }

        // ===============================
        // RENDER HARGA
        // ===============================
        if (isHargaLocked) {
            renderHargaFromDb();
        } else {
            renderHargaFromPaket();
        }

    } catch (err) {
        console.error(err);
        paketNama.value = 'Gagal memuat paket';
    }
}

    /* =====================================================
     | EVENTS
     ===================================================== */

    // Keberangkatan berubah → reload paket
    keberangkatan.addEventListener('change', () => {

        paketNama.value = '';
        paketId.value   = '';
        harga = { quad: 0, triple: 0, double: 0 };

        if (!keberangkatan.value) return;

        loadPaket(keberangkatan.value);
    });

    // Tipe kamar berubah → update harga (jika belum terkunci)
    tipeKamar?.addEventListener('change', () => {
        if (tipeKamarHidden) {
            tipeKamarHidden.value = tipeKamar.value;
        }

        if (!isHargaLocked) {
            renderHargaFromPaket();
        }
    });

    // Hitung usia realtime
    tglLahir?.addEventListener('change', () => {
        usiaInput.value = hitungUsia(tglLahir.value);
    });

    /* =====================================================
     | INIT
     ===================================================== */

    // Usia awal
    if (tglLahir?.value) {
        usiaInput.value = hitungUsia(tglLahir.value);
    }

    // Load paket awal (edit)
    if (keberangkatan.value) {
        loadPaket(keberangkatan.value);
    }

    // 🔒 LOCK TOTAL JIKA SUDAH ADA PAYMENT
    if (hasPayment) {
        lockField(keberangkatan);
        lockField(tipeKamar);
        lockField(diskonInput);
    }

});

// document.addEventListener('DOMContentLoaded', () => {

//     const keberangkatan = document.getElementById('keberangkatan');
//     const paketNama     = document.getElementById('nama-paket');
//     const paketId       = document.getElementById('id_paket_master');
//     const tipeKamar     = document.getElementById('tipe-kamar');
//     const hargaView     = document.getElementById('harga-view');
//     const hargaHidden   = document.getElementById('harga_default');
//     const tglLahir      = document.getElementById('tgl-lahir');
//     const usiaInput     = document.getElementById('usia');
//     const isEdit        = document.getElementById('is-edit')?.value === '1';

//     if (!keberangkatan || !paketNama || !paketId) return;

//     let harga = { quad: 0, triple: 0, double: 0 };
//     let isHargaLocked = isEdit && Number(hargaHidden?.value || 0) > 0;

//     /* =====================================================
//      | HELPER
//      ===================================================== */
//     function renderHarga() {
//         if (isHargaLocked) return;

//         const h = harga[tipeKamar?.value] || 0;
//         hargaView.value   = h ? 'Rp ' + h.toLocaleString('id-ID') : '';
//         hargaHidden.value = h;
//     }

//     function hitungUsia(tgl) {
//         if (!tgl) return '';
//         const today = new Date();
//         const birth = new Date(tgl);

//         let usia = today.getFullYear() - birth.getFullYear();
//         const m = today.getMonth() - birth.getMonth();

//         if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) usia--;
//         return usia + ' Tahun';
//     }

//     /* =====================================================
//      | LOAD PAKET (ENDPOINT NETRAL)
//      ===================================================== */
//     async function loadPaket(idKeberangkatan) {
//         paketNama.value = 'Memuat paket...';

//         try {
//             const res = await fetch(`/keberangkatan-paket/${idKeberangkatan}`, {
//                 headers: {
//                     'Accept': 'application/json'
//                 }
//             });

//             if (!res.ok) throw new Error('Fetch gagal');

//             const data = await res.json();

//             if (!data.paket) {
//                 paketNama.value = 'Paket belum diset';
//                 harga = { quad: 0, triple: 0, double: 0 };
//                 renderHarga();
//                 return;
//             }

//             paketNama.value = data.paket.nama_paket;
//             paketId.value   = data.paket.id;

//             harga = {
//                 quad:   Number(data.paket.harga_quad || 0),
//                 triple: Number(data.paket.harga_triple || 0),
//                 double: Number(data.paket.harga_double || 0),
//             };

//             renderHarga();

//         } catch (err) {
//             console.error(err);
//             paketNama.value = 'Gagal memuat paket';
//         }
//     }

//     /* =====================================================
//      | EVENTS
//      ===================================================== */
//     keberangkatan.addEventListener('change', () => {

//         paketNama.value = '';
//         paketId.value   = '';
//         harga = { quad: 0, triple: 0, double: 0 };

//         if (!keberangkatan.value) return;

//         // 🔒 EDIT + sudah ada harga → LOCK
//         if (isEdit && Number(hargaHidden.value || 0) > 0) {
//             isHargaLocked = true;
//             loadPaket(keberangkatan.value); // hanya untuk nama paket
//             return;
//         }

//         isHargaLocked = false;
//         loadPaket(keberangkatan.value);
//     });

//     tipeKamar?.addEventListener('change', () => {
//         if (!isHargaLocked) renderHarga();
//     });

//     tglLahir?.addEventListener('change', () => {
//         usiaInput.value = hitungUsia(tglLahir.value);
//     });

//     /* =====================================================
//      | INIT
//      ===================================================== */
//     if (tglLahir?.value) {
//         usiaInput.value = hitungUsia(tglLahir.value);
//     }

//     if (keberangkatan.value) {
//         keberangkatan.dispatchEvent(new Event('change'));
//     }
// });


// document.addEventListener('DOMContentLoaded', () => {

//     const keberangkatan = document.getElementById('keberangkatan');
//     const paketNama     = document.getElementById('nama-paket');
//     const paketId       = document.getElementById('id_paket_master');
//     const tipeKamar     = document.getElementById('tipe-kamar');
//     const hargaView     = document.getElementById('harga-view');
//     const hargaHidden   = document.getElementById('harga_default');
//     const tglLahir      = document.getElementById('tgl-lahir');
//     const usiaInput     = document.getElementById('usia');
//     const isEdit        = document.getElementById('is-edit')?.value === '1';

//     if (!keberangkatan || !paketNama || !paketId) return;

//     let harga = { quad:0, triple:0, double:0 };
//     let isHargaLocked = isEdit && Number(hargaHidden.value || 0) > 0;

//     /* =============================
//      | HELPER
//      ============================= */
//     function renderHarga() {
//         if (isHargaLocked) return;

//         const h = harga[tipeKamar.value] || 0;
//         hargaView.value   = h ? 'Rp ' + h.toLocaleString('id-ID') : '';
//         hargaHidden.value = h;
//     }

//     function hitungUsia(tgl) {
//         if (!tgl) return '';
//         const today = new Date();
//         const birth = new Date(tgl);
//         let usia = today.getFullYear() - birth.getFullYear();
//         const m = today.getMonth() - birth.getMonth();
//         if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) usia--;
//         return usia + ' Tahun';
//     }

//     /* =============================
//      | LOAD PAKET
//      ============================= */
//     function loadPaket(idKeberangkatan) {
//         fetch(`/operator/keberangkatan-paket/${idKeberangkatan}`, {
//             headers: { 'Accept': 'application/json' }
//         })
//         .then(r => {
//             if (!r.ok) throw new Error('Fetch gagal');
//             return r.json();
//         })
//         .then(res => {
//             if (!res.paket) {
//                 paketNama.value = 'Paket belum diset';
//                 return;
//             }

//             paketNama.value = res.paket.nama_paket;
//             paketId.value   = res.paket.id;

//             harga = {
//                 quad:   Number(res.paket.harga_quad),
//                 triple: Number(res.paket.harga_triple),
//                 double: Number(res.paket.harga_double),
//             };

//             renderHarga();
//         })
//         .catch(() => {
//             paketNama.value = 'Gagal memuat paket';
//         });
//     }

//     /* =============================
//      | EVENT
//      ============================= */
//     keberangkatan.addEventListener('change', () => {

//         paketNama.value = '';
//         paketId.value   = '';
//         harga = { quad:0, triple:0, double:0 };

//         if (!keberangkatan.value) return;

//         // 🔒 EDIT + sudah ada harga → LOCK
//         if (isEdit && Number(hargaHidden.value || 0) > 0) {
//             isHargaLocked = true;
//             loadPaket(keberangkatan.value); // hanya load nama paket
//             return;
//         }

//         isHargaLocked = false;
//         loadPaket(keberangkatan.value);
//     });

//     tipeKamar?.addEventListener('change', () => {
//         if (!isHargaLocked) renderHarga();
//     });

//     tglLahir?.addEventListener('change', () => {
//         usiaInput.value = hitungUsia(tglLahir.value);
//     });

//     /* =============================
//      | INIT
//      ============================= */
//     if (tglLahir?.value) {
//         usiaInput.value = hitungUsia(tglLahir.value);
//     }

//     if (keberangkatan.value) {
//         keberangkatan.dispatchEvent(new Event('change'));
//     }
// });
