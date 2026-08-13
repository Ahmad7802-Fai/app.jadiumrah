@csrf

<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
    <p class="text-sm text-yellow-800">
        ⚠️ Closing akan dikirim ke <b>Pusat</b> untuk approval.
        Setelah dikirim, lead tidak bisa diedit.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div>
        <label class="form-label">Jamaah (opsional)</label>
        <input type="number"
            name="jamaah_id"
            value="{{ old('jamaah_id') }}"
            class="form-input"
            placeholder="ID Jamaah (jika sudah ada)">
    </div>

    <div>
        <label class="form-label">Nominal DP</label>
        <input type="number"
            step="0.01"
            name="nominal_dp"
            value="{{ old('nominal_dp') }}"
            class="form-input"
            placeholder="Contoh: 5000000">
    </div>

    <div>
        <label class="form-label">Total Paket</label>
        <input type="number"
            step="0.01"
            name="total_paket"
            value="{{ old('total_paket') }}"
            class="form-input"
            placeholder="Contoh: 32000000">
    </div>

    <div class="md:col-span-2">
        <label class="form-label">Catatan Closing</label>
        <textarea
            name="catatan"
            rows="4"
            class="form-input"
            placeholder="Catatan untuk tim pusat">{{ old('catatan') }}</textarea>
    </div>

</div>

<div class="mt-6 flex gap-3">
    <button class="btn-warning">
        Ajukan Closing
    </button>
    <a href="{{ url()->previous() }}" class="btn-secondary">
        Batal
    </a>
</div>
