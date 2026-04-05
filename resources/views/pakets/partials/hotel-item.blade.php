@php
    $get = fn($key) => old("hotels.$index.$key",
        is_array($hotel)
            ? ($hotel[$key] ?? null)
            : ($hotel->$key ?? null)
    );

    $city     = $get('city');
    $name     = $get('hotel_name');
    $rating   = $get('rating');
    $distance = $get('distance_to_haram');
@endphp

<div class="hotel-item border rounded-lg p-3 bg-gray-50 relative text-sm">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-2">
        <h4 class="text-xs font-semibold text-gray-600 hotel-label">
            Hotel
        </h4>

        <button type="button"
            class="text-red-500 text-xs px-2 py-1 rounded hover:bg-red-50 btn-remove-hotel">
            ✕
        </button>
    </div>

    {{-- FORM --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">

        {{-- KOTA --}}
        <div class="space-y-1">
            <label class="text-[10px] text-gray-400">Kota</label>
            <select name="hotels[{{ $index }}][city]"
                class="w-full px-2 py-1.5 text-xs rounded-md border border-gray-300 focus:ring-1 focus:ring-primary"
                required>
                <option value="">--</option>
                <option value="mekkah"  @selected($city==='mekkah')>Mekkah</option>
                <option value="madinah" @selected($city==='madinah')>Madinah</option>
            </select>
        </div>

        {{-- NAMA --}}
        <div class="space-y-1">
            <label class="text-[10px] text-gray-400">Hotel</label>
            <input type="text"
                name="hotels[{{ $index }}][hotel_name]"
                value="{{ $name }}"
                class="w-full px-2 py-1.5 text-xs rounded-md border border-gray-300 focus:ring-1 focus:ring-primary"
                required>
        </div>

        {{-- RATING --}}
        <div class="space-y-1">
            <label class="text-[10px] text-gray-400">★</label>
            <input type="number"
                name="hotels[{{ $index }}][rating]"
                value="{{ $rating }}"
                min="1" max="5"
                class="w-full px-2 py-1.5 text-xs rounded-md border border-gray-300 focus:ring-1 focus:ring-primary">
        </div>

        {{-- JARAK --}}
        <div class="space-y-1">
            <label class="text-[10px] text-gray-400">Jarak</label>
            <input type="text"
                name="hotels[{{ $index }}][distance_to_haram]"
                value="{{ $distance }}"
                class="w-full px-2 py-1.5 text-xs rounded-md border border-gray-300 focus:ring-1 focus:ring-primary">
        </div>

    </div>

</div>