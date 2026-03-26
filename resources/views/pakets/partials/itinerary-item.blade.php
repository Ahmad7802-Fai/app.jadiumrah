@php
    $destinationId = old("itinerary.$index.destination_id",
        is_array($item) ? ($item['destination_id'] ?? null) : ($item->destination_id ?? null)
    );

    $destinationName = old("itinerary.$index.destination_name",
        is_array($item) ? ($item['destination_name'] ?? null) : null
    );

    $note = old("itinerary.$index.note",
        is_array($item) ? ($item['note'] ?? null) : ($item->note ?? null)
    );
@endphp

<div class="itinerary-item border rounded-xl p-4 bg-gray-50 cursor-move relative">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center mb-3">

        {{-- AUTO LABEL --}}
        <h4 class="font-semibold day-label text-gray-700">
            Hari
        </h4>

        <button type="button"
                class="btn btn-danger btn-sm btn-remove-itinerary">
            Hapus
        </button>

    </div>

    {{-- DAY ORDER --}}
    <input type="hidden"
           name="itinerary[{{ $index }}][day_order]"
           class="day-order-input"
           value="">

    {{-- ================= FORM ================= --}}
    <div class="grid md:grid-cols-2 gap-4">

        {{-- DESTINATION --}}
        <div>
            <label class="label">Destination *</label>

            <select name="itinerary[{{ $index }}][destination_id]"
                    class="input destination-select">
                <option value="">-- Pilih Destination --</option>

                @foreach($destinations as $dest)
                    <option value="{{ $dest->id }}"
                        @selected($destinationId == $dest->id)>
                        {{ $dest->city }} ({{ $dest->country }})
                    </option>
                @endforeach

                <option value="__new__">+ Tambah Destination Baru</option>
            </select>

            {{-- MANUAL INPUT --}}
            <input type="text"
                   name="itinerary[{{ $index }}][destination_name]"
                   value="{{ $destinationName }}"
                   placeholder="Nama Destination Baru"
                   class="input mt-2 manual-destination {{ $destinationId == '__new__' ? '' : 'hidden' }}">
        </div>

        {{-- NOTE --}}
        <div>
            <label class="label">Catatan</label>
            <textarea name="itinerary[{{ $index }}][note]"
                      class="input">{{ $note }}</textarea>
        </div>

    </div>

</div>