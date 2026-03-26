@php
    $get = fn($key) => old("itinerary.$index.$key",
        is_array($item)
            ? ($item[$key] ?? null)
            : ($item->$key ?? null)
    );

    $destinationId   = $get('destination_id');
    $destinationName = $get('destination_name');
    $note            = $get('note');
@endphp

<div class="itinerary-item border rounded-xl p-4 bg-gray-50 cursor-move relative">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-3">
        <h4 class="font-semibold day-label text-gray-700">Hari</h4>

        <button type="button"
                class="btn btn-danger btn-sm btn-remove-itinerary">
            Hapus
        </button>
    </div>

    {{-- ORDER --}}
    <input type="hidden"
           name="itinerary[{{ $index }}][day_order]"
           class="day-order-input">

    {{-- FORM --}}
    <div class="grid md:grid-cols-2 gap-4">

        {{-- DESTINATION --}}
        <div>
            <label class="label">Destination *</label>

            <select name="itinerary[{{ $index }}][destination_id]"
                    class="input destination-select"
                    required>

                <option value="">-- Pilih Destination --</option>

                @foreach($destinations as $dest)
                    <option value="{{ $dest->id }}"
                        @selected($destinationId == $dest->id)>
                        {{ $dest->city }} ({{ $dest->country }})
                    </option>
                @endforeach

                <option value="__new__" @selected($destinationId === '__new__')>
                    + Tambah Destination Baru
                </option>

            </select>

            {{-- INPUT MANUAL --}}
            <input type="text"
                   name="itinerary[{{ $index }}][destination_name]"
                   value="{{ $destinationName }}"
                   placeholder="Nama Destination Baru"
                   class="input mt-2 manual-destination {{ $destinationId === '__new__' ? '' : 'hidden' }}">
        </div>

        {{-- NOTE --}}
        <div>
            <label class="label">Catatan</label>
            <textarea name="itinerary[{{ $index }}][note]"
                      class="input">{{ $note }}</textarea>
        </div>

    </div>

</div>