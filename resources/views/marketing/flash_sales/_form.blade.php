@csrf

<div class="grid md:grid-cols-2 gap-6">

    <div>
        <label>Paket</label>
        <select name="paket_id" class="input">
            @foreach($pakets as $paket)
                <option value="{{ $paket->id }}"
                    @selected(old('paket_id',$flashSale->paket_id ?? '') == $paket->id)>
                    {{ $paket->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Tipe Diskon</label>
        <select name="discount_type" class="input">
            <option value="fixed">Fixed</option>
            <option value="percent">Percent</option>
        </select>
    </div>

    <div>
        <label>Nilai</label>
        <input type="number" name="value"
               value="{{ old('value',$flashSale->value ?? '') }}"
               class="input">
    </div>

    <div>
        <label>Seat Limit</label>
        <input type="number" name="seat_limit"
               value="{{ old('seat_limit',$flashSale->seat_limit ?? '') }}"
               class="input">
    </div>

    <div>
        <label>Start At</label>
        <input type="datetime-local"
               name="start_at"
               value="{{ old('start_at',isset($flashSale) ? $flashSale->start_at->format('Y-m-d\TH:i') : '') }}"
               class="input">
    </div>

    <div>
        <label>End At</label>
        <input type="datetime-local"
               name="end_at"
               value="{{ old('end_at',isset($flashSale) ? $flashSale->end_at->format('Y-m-d\TH:i') : '') }}"
               class="input">
    </div>

</div>

<div class="mt-6">
    <button class="btn btn-primary">
        Simpan
    </button>
</div>