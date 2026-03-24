@csrf

<div class="grid md:grid-cols-2 gap-6">

    <div>
        <label>Kode</label>
        <input type="text" name="code"
               value="{{ old('code', $voucher->code ?? '') }}"
               class="form-input">
    </div>

    <div>
        <label>Tipe</label>
        <select name="type" class="form-select">
            <option value="fixed"
                {{ old('type', $voucher->type ?? '')=='fixed'?'selected':'' }}>
                Fixed
            </option>
            <option value="percent"
                {{ old('type', $voucher->type ?? '')=='percent'?'selected':'' }}>
                Percent
            </option>
        </select>
    </div>

    <div>
        <label>Value</label>
        <input type="number" name="value"
               value="{{ old('value', $voucher->value ?? '') }}"
               class="form-input">
    </div>

    <div>
        <label>Max Discount</label>
        <input type="number" name="max_discount"
               value="{{ old('max_discount', $voucher->max_discount ?? '') }}"
               class="form-input">
    </div>

    <div>
        <label>Quota</label>
        <input type="number" name="quota"
               value="{{ old('quota', $voucher->quota ?? '') }}"
               class="form-input">
    </div>

    <div>
        <label>Expired At</label>
        <input type="date" name="expired_at"
               value="{{ old('expired_at',
               optional($voucher->expired_at ?? null)->format('Y-m-d')) }}"
               class="form-input">
    </div>

</div>

<div class="mt-6">
    <button class="btn btn-primary">
        Simpan
    </button>
</div>