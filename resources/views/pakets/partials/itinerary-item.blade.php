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

<div class="itinerary-item border rounded-lg p-2.5 bg-gray-50 text-xs relative cursor-move">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-1">
        <h4 class="text-[11px] font-semibold text-gray-600 day-label">
            Hari
        </h4>

        <button type="button"
            class="text-red-500 px-2 py-0.5 rounded hover:bg-red-50 btn-remove-itinerary">
            ✕
        </button>
    </div>

    {{-- ORDER --}}
    <input type="hidden"
        name="itinerary[{{ $index }}][day_order]"
        class="day-order-input">

    {{-- FORM --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">

        {{-- DESTINATION --}}
        <div class="space-y-1">

            <label class="text-[10px] text-gray-400">Dest</label>

            <select name="itinerary[{{ $index }}][destination_id]"
                class="w-full px-2 py-1.5 text-xs rounded-md border border-gray-300 focus:ring-1 focus:ring-primary destination-select"
                required>

                <option value="">--</option>

                @foreach($destinations as $dest)
                    <option value="{{ $dest->id }}"
                        @selected($destinationId == $dest->id)>
                        {{ $dest->city }}
                    </option>
                @endforeach

                <option value="__new__" @selected($destinationId === '__new__')>
                    + Baru
                </option>

            </select>

            {{-- INPUT MANUAL --}}
            <input type="text"
                name="itinerary[{{ $index }}][destination_name]"
                value="{{ $destinationName }}"
                placeholder="Destination"
                class="w-full px-2 py-1.5 text-xs rounded-md border border-gray-300 focus:ring-1 focus:ring-primary manual-destination {{ $destinationId === '__new__' ? '' : 'hidden' }}">
        </div>

        {{-- NOTE --}}
        <div class="space-y-1">
            <label class="text-[10px] text-gray-400">Note</label>

            <textarea name="itinerary[{{ $index }}][note]"
                rows="1"
                class="w-full px-2 py-1.5 text-xs rounded-md border border-gray-300 focus:ring-1 focus:ring-primary resize-none">{{ $note }}</textarea>
        </div>

    </div>

</div>