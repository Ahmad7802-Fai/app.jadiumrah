@php
    $destinationId = old("itinerary.$index.destination_id",
        $item->destination_id ?? null);

    $destinationName = old("itinerary.$index.destination_name");

    $note = old("itinerary.$index.note",
        $item->note ?? null);
@endphp

<div class="itinerary-item border rounded-xl p-4 bg-gray-50 cursor-move">

    <div class="flex justify-between items-center mb-3">
        <h4 class="font-semibold day-label">
            Hari {{ $index + 1 }}
        </h4>

        <button type="button"
                onclick="this.closest('.itinerary-item').remove(); updateDayOrder();"
                class="btn btn-danger btn-sm">
            Hapus
        </button>
    </div>

    <input type="hidden"
           name="itinerary[{{ $index }}][day_order]"
           class="day-order-input"
           value="{{ $index + 1 }}">

    <div class="grid md:grid-cols-2 gap-4">

        {{-- Dropdown --}}
        <div>
            <label class="label">Destination *</label>

            <select name="itinerary[{{ $index }}][destination_id]"
                    class="input destination-select"
                    onchange="toggleManualInput(this)">
                <option value="">-- Pilih Destination --</option>

                @foreach($destinations as $dest)
                    <option value="{{ $dest->id }}"
                        {{ $destinationId == $dest->id ? 'selected' : '' }}>
                        {{ $dest->city }} ({{ $dest->country }})
                    </option>
                @endforeach

                <option value="__new__">+ Tambah Destination Baru</option>
            </select>

            {{-- Manual Input --}}
            <input type="text"
                   name="itinerary[{{ $index }}][destination_name]"
                   value="{{ $destinationName }}"
                   placeholder="Nama Destination Baru"
                   class="input mt-2 manual-destination d-none">
        </div>

        {{-- Note --}}
        <div>
            <label class="label">Catatan</label>
            <textarea name="itinerary[{{ $index }}][note]"
                      class="input">{{ $note }}</textarea>
        </div>

    </div>

</div>

<script>
function toggleManualInput(selectElement) {

    const wrapper = selectElement.closest('.itinerary-item');
    const manualInput = wrapper.querySelector('.manual-destination');

    if (selectElement.value === '__new__') {
        manualInput.classList.remove('d-none');
        manualInput.required = true;
        selectElement.required = false;
    } else {
        manualInput.classList.add('d-none');
        manualInput.required = false;
        selectElement.required = true;
    }
}
</script>