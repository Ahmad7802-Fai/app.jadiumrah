@php
    use Carbon\Carbon;

    $roomTypes = ['double','triple','quad'];

    $get = fn($key) => is_array($dep)
        ? ($dep[$key] ?? null)
        : ($dep->$key ?? null);

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


<div class="departure-item border rounded-lg p-3 bg-gray-50 text-xs space-y-3">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <h4 class="text-[11px] font-semibold text-gray-600 departure-label">
            Departure
        </h4>

        <button type="button"
            class="text-red-500 px-2 py-0.5 rounded hover:bg-red-50 btn-remove-departure">
            ✕
        </button>
    </div>


    {{-- BASIC --}}
    <div class="grid grid-cols-3 gap-2">

        <input type="date"
            name="departures[{{ $index }}][departure_date]"
            value="{{ $departureDate }}"
            class="w-full px-2 py-1.5 text-xs rounded-md border border-gray-300 focus:ring-1 focus:ring-primary"
            required>

        <input type="date"
            name="departures[{{ $index }}][return_date]"
            value="{{ $returnDate }}"
            class="w-full px-2 py-1.5 text-xs rounded-md border border-gray-300 focus:ring-1 focus:ring-primary">

        <input type="number"
            name="departures[{{ $index }}][quota]"
            value="{{ $quota }}"
            class="w-full px-2 py-1.5 text-xs rounded-md border border-gray-300 focus:ring-1 focus:ring-primary"
            placeholder="Q"
            min="1"
            required>

    </div>


    {{-- ROOM --}}
    <div class="space-y-2">

        @foreach($roomTypes as $i => $room)

            @php
                $price      = $getPrice($room,$i,'price');
                $promoType  = $getPrice($room,$i,'promo_type');
                $promoValue = $getPrice($room,$i,'promo_value');
                $promoLabel = $getPrice($room,$i,'promo_label');
            @endphp

            <div class="grid grid-cols-12 gap-1 items-center">

                {{-- ROOM --}}
                <div class="col-span-2 text-[10px] font-semibold uppercase text-gray-500">
                    {{ substr($room,0,3) }}
                </div>

                <input type="hidden"
                    name="departures[{{ $index }}][prices][{{ $i }}][room_type]"
                    value="{{ $room }}">

                {{-- PRICE --}}
                <input type="number"
                    name="departures[{{ $index }}][prices][{{ $i }}][price]"
                    value="{{ $price }}"
                    class="col-span-2 px-2 py-1 text-xs rounded border border-gray-300 focus:ring-1 focus:ring-primary"
                    placeholder="Harga">

                {{-- TYPE --}}
                <select name="departures[{{ $index }}][prices][{{ $i }}][promo_type]"
                    class="col-span-2 px-1 py-1 text-xs rounded border border-gray-300">

                    <option value="">-</option>
                    <option value="percent" @selected($promoType==='percent')>%</option>
                    <option value="fixed" @selected($promoType==='fixed')>Rp</option>

                </select>

                {{-- VALUE --}}
                <input type="number"
                    name="departures[{{ $index }}][prices][{{ $i }}][promo_value]"
                    value="{{ $promoValue }}"
                    class="col-span-2 px-2 py-1 text-xs rounded border border-gray-300"
                    placeholder="Disc">

                {{-- LABEL --}}
                <input type="text"
                    name="departures[{{ $index }}][prices][{{ $i }}][promo_label]"
                    value="{{ $promoLabel }}"
                    class="col-span-4 px-2 py-1 text-xs rounded border border-gray-300"
                    placeholder="Label">

            </div>

        @endforeach

    </div>

</div>