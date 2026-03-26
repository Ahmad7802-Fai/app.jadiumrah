@php
    $city = old("hotels.$index.city",
        is_array($hotel) ? ($hotel['city'] ?? null) : ($hotel->city ?? null)
    );

    $hotelName = old("hotels.$index.hotel_name",
        is_array($hotel) ? ($hotel['hotel_name'] ?? null) : ($hotel->hotel_name ?? null)
    );

    $rating = old("hotels.$index.rating",
        is_array($hotel) ? ($hotel['rating'] ?? null) : ($hotel->rating ?? null)
    );

    $distance = old("hotels.$index.distance_to_haram",
        is_array($hotel) ? ($hotel['distance_to_haram'] ?? null) : ($hotel->distance_to_haram ?? null)
    );
@endphp

<div class="hotel-item border rounded-xl p-4 bg-gray-50 relative">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center mb-3">

        {{-- AUTO LABEL (DIISI JS) --}}
        <h4 class="font-semibold hotel-label text-gray-700">
            Hotel
        </h4>

        <button type="button"
                class="btn btn-danger btn-sm btn-remove-hotel">
            Hapus
        </button>

    </div>

    {{-- ================= FORM ================= --}}
    <div class="grid md:grid-cols-4 gap-4">

        {{-- KOTA --}}
        <div>
            <label class="label">Kota *</label>
            <select name="hotels[{{ $index }}][city]"
                    class="input"
                    required>
                <option value="">-- Pilih Kota --</option>

                <option value="mekkah" @selected($city=='mekkah')>
                    Mekkah
                </option>

                <option value="madinah" @selected($city=='madinah')>
                    Madinah
                </option>
            </select>
        </div>

        {{-- NAMA HOTEL --}}
        <div>
            <label class="label">Nama Hotel *</label>
            <input type="text"
                   name="hotels[{{ $index }}][hotel_name]"
                   value="{{ $hotelName }}"
                   class="input"
                   required>
        </div>

        {{-- RATING --}}
        <div>
            <label class="label">Rating</label>
            <input type="number"
                   min="1"
                   max="5"
                   name="hotels[{{ $index }}][rating]"
                   value="{{ $rating }}"
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