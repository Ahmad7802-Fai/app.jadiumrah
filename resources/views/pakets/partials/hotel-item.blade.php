@php
    $city = old("hotels.$index.city",
        $hotel->city ?? null);

    $hotelName = old("hotels.$index.hotel_name",
        $hotel->hotel_name ?? null);

    $rating = old("hotels.$index.rating",
        $hotel->rating ?? null);

    $distance = old("hotels.$index.distance_to_haram",
        $hotel->distance_to_haram ?? null);
@endphp

<div class="hotel-item border rounded-xl p-4 bg-gray-50">

    <div class="flex justify-between items-center mb-3">
        <h4 class="font-semibold">Hotel {{ $index + 1 }}</h4>

        <button type="button"
                onclick="this.closest('.hotel-item').remove()"
                class="btn btn-danger btn-sm">
            Hapus
        </button>
    </div>

    <div class="grid md:grid-cols-4 gap-4">

        <div>
            <label class="label">Kota *</label>
            <select name="hotels[{{ $index }}][city]"
                    class="input"
                    required>
                <option value="">-- Pilih Kota --</option>
                <option value="mekkah"
                    {{ $city=='mekkah'?'selected':'' }}>
                    Mekkah
                </option>
                <option value="madinah"
                    {{ $city=='madinah'?'selected':'' }}>
                    Madinah
                </option>
            </select>
        </div>

        <div>
            <label class="label">Nama Hotel *</label>
            <input type="text"
                   name="hotels[{{ $index }}][hotel_name]"
                   value="{{ $hotelName }}"
                   class="input"
                   required>
        </div>

        <div>
            <label class="label">Rating</label>
            <input type="number"
                   min="1"
                   max="5"
                   name="hotels[{{ $index }}][rating]"
                   value="{{ $rating }}"
                   class="input">
        </div>

        <div>
            <label class="label">Jarak ke Haram</label>
            <input type="text"
                   name="hotels[{{ $index }}][distance_to_haram]"
                   value="{{ $distance }}"
                   class="input">
        </div>

    </div>

</div>