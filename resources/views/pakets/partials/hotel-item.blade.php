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

<div class="hotel-item border rounded-xl p-4 bg-gray-50 relative">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-3">
        <h4 class="font-semibold hotel-label text-gray-700">Hotel</h4>

        <button type="button"
                class="btn btn-danger btn-sm btn-remove-hotel">
            Hapus
        </button>
    </div>

    {{-- FORM --}}
    <div class="grid md:grid-cols-4 gap-4">

        {{-- KOTA --}}
        <div>
            <label class="label">Kota *</label>
            <select name="hotels[{{ $index }}][city]"
                    class="input"
                    required>
                <option value="">-- Pilih Kota --</option>
                <option value="mekkah"  @selected($city==='mekkah')>Mekkah</option>
                <option value="madinah" @selected($city==='madinah')>Madinah</option>
            </select>
        </div>

        {{-- NAMA --}}
        <div>
            <label class="label">Nama Hotel *</label>
            <input type="text"
                   name="hotels[{{ $index }}][hotel_name]"
                   value="{{ $name }}"
                   class="input"
                   required>
        </div>

        {{-- RATING --}}
        <div>
            <label class="label">Rating</label>
            <input type="number"
                   name="hotels[{{ $index }}][rating]"
                   value="{{ $rating }}"
                   min="1" max="5"
                   class="input">
        </div>

        {{-- JARAK --}}
        <div>
            <label class="label">Jarak ke Haram</label>
            <input type="text"
                   name="hotels[{{ $index }}][distance_to_haram]"
                   value="{{ $distance }}"
                   class="input">
        </div>

    </div>

</div>