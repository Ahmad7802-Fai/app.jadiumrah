<div class="c-card has-header-bg mb-16">

    {{-- HEADER --}}
    <div class="c-card__header">
        <div class="d-flex align-center gap-6">
            <span>Input Pembayaran</span>
            <span class="c-badge warning sm">
                Menunggu approval pusat
            </span>
        </div>
    </div>

    {{-- FORM --}}
    <div class="c-card__body">

        <form method="POST"
              action="{{ route('cabang.payments.store', $jamaah) }}"
              enctype="multipart/form-data"
              class="c-form">
            @csrf

            {{-- TANGGAL BAYAR --}}
            <div class="c-form__group">
                <label class="c-form__label">
                    Tanggal Bayar
                </label>
                <input
                    type="date"
                    name="tanggal_bayar"
                    class="c-form__input"
                    required>
            </div>

            {{-- JUMLAH --}}
            <div class="c-form__group">
                <label class="c-form__label">
                    Jumlah (Rp)
                </label>
                <input
                    type="number"
                    name="jumlah"
                    class="c-form__input"
                    min="10000"
                    placeholder="Contoh: 5.000.000"
                    required>
            </div>

            {{-- METODE --}}
            <div class="c-form__group">
                <label class="c-form__label">
                    Metode Pembayaran
                </label>
                <select
                    name="metode"
                    class="c-form__input"
                    required>
                    <option value="">— Pilih Metode —</option>
                    <option value="transfer">Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="qris">QRIS</option>
                </select>
            </div>

            {{-- KETERANGAN --}}
            <div class="c-form__group">
                <label class="c-form__label">
                    Keterangan
                </label>
                <input
                    type="text"
                    name="keterangan"
                    class="c-form__input"
                    placeholder="Opsional">
            </div>

            {{-- BUKTI --}}
            <div class="c-form__group">
                <label class="c-form__label">
                    Bukti Transfer
                </label>
                <input
                    type="file"
                    name="bukti_transfer"
                    class="c-form__input">
            </div>

            {{-- ACTION --}}
            <div class="d-flex gap-8 mt-12">
                <button class="c-btn primary">
                    💾 Simpan Pembayaran
                </button>
            </div>

        </form>

    </div>
</div>
