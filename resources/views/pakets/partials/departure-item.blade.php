@php
    use Carbon\Carbon;

    $roomTypes = ['double','triple','quad'];

    // helper universal (array / object)
    $get = fn($key) => is_array($dep)
        ? ($dep[$key] ?? null)
        : ($dep->$key ?? null);

    // DATE FORMAT
    $departureDate = old(
        "departures.$index.departure_date",
        $get('departure_date')
            ? Carbon::parse($get('departure_date'))->format('Y-m-d')
            : null
    );

    $returnDate = old(
        "departures.$index.return_date",
        $get('return_date')
            ? Carbon::parse($get('return_date'))->format('Y-m-d')
            : null
    );

    $quota = old("departures.$index.quota", $get('quota'));

    // PRICE HELPER (clean ambil data relasi / array)
    $getPrice = function($room, $i, $field) use ($dep, $index) {

        $model = is_array($dep)
            ? ($dep['prices'][$i] ?? [])
            : optional($dep->prices->firstWhere('room_type',$room));

        return old(
            "departures.$index.prices.$i.$field",
            is_array($model)
                ? ($model[$field] ?? null)
                : ($model->$field ?? null)
        );
    };
@endphp


<div class="departure-item border rounded-xl p-5 space-y-5 bg-gray-50">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <h4 class="font-semibold text-gray-700 text-sm departure-label">
            Departure
        </h4>

        <button type="button"
                class="text-red-500 text-xs btn-remove-departure">
            Hapus
        </button>
    </div>


    {{-- BASIC --}}
    <div class="grid md:grid-cols-3 gap-3">

        <input type="date"
               name="departures[{{ $index }}][departure_date]"
               value="{{ $departureDate }}"
               class="input text-sm"
               required>

        <input type="date"
               name="departures[{{ $index }}][return_date]"
               value="{{ $returnDate }}"
               class="input text-sm">

        <input type="number"
               name="departures[{{ $index }}][quota]"
               value="{{ $quota }}"
               class="input text-sm"
               placeholder="Quota"
               min="1"
               required>

    </div>


    {{-- ROOM PRICING --}}
    <div class="space-y-3">

        <div class="text-xs font-semibold text-gray-500">
            Harga & Promo per Room
        </div>

        @foreach($roomTypes as $i => $room)

            @php
                $price      = $getPrice($room,$i,'price');
                $promoType  = $getPrice($room,$i,'promo_type');
                $promoValue = $getPrice($room,$i,'promo_value');
                $promoLabel = $getPrice($room,$i,'promo_label');
            @endphp

            <div class="grid grid-cols-12 gap-2 items-center">

                {{-- ROOM --}}
                <div class="col-span-2 text-[11px] font-semibold uppercase">
                    {{ $room }}
                </div>

                {{-- TYPE --}}
                <input type="hidden"
                       name="departures[{{ $index }}][prices][{{ $i }}][room_type]"
                       value="{{ $room }}">

                {{-- PRICE --}}
                <input type="number"
                       name="departures[{{ $index }}][prices][{{ $i }}][price]"
                       value="{{ $price }}"
                       class="input col-span-2 text-xs"
                       placeholder="Harga">

                {{-- PROMO TYPE --}}
                <select name="departures[{{ $index }}][prices][{{ $i }}][promo_type]"
                        class="input col-span-2 text-xs">

                    <option value="">-</option>
                    <option value="percent" @selected($promoType==='percent')>%</option>
                    <option value="fixed"   @selected($promoType==='fixed')>Rp</option>

                </select>

                {{-- VALUE --}}
                <input type="number"
                       name="departures[{{ $index }}][prices][{{ $i }}][promo_value]"
                       value="{{ $promoValue }}"
                       class="input col-span-2 text-xs"
                       placeholder="Diskon">

                {{-- LABEL --}}
                <input type="text"
                       name="departures[{{ $index }}][prices][{{ $i }}][promo_label]"
                       value="{{ $promoLabel }}"
                       class="input col-span-4 text-xs"
                       placeholder="Label">

            </div>

        @endforeach

    </div>

</div>