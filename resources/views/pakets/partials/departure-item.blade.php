@php

$roomTypes = ['double','triple','quad'];

if (is_array($dep)) {
    $rawDeparture = $dep['departure_date'] ?? null;
    $rawReturn    = $dep['return_date'] ?? null;
} else {
    $rawDeparture = $dep->departure_date ?? null;
    $rawReturn    = $dep->return_date ?? null;
}

/*
|--------------------------------------------------------------------------
| FORMAT DATE
|--------------------------------------------------------------------------
*/
$departureDate = old(
    "departures.$index.departure_date",
    $rawDeparture ? \Carbon\Carbon::parse($rawDeparture)->format('Y-m-d') : null
);

$returnDate = old(
    "departures.$index.return_date",
    $rawReturn ? \Carbon\Carbon::parse($rawReturn)->format('Y-m-d') : null
);

$quota = old(
    "departures.$index.quota",
    is_array($dep) ? ($dep['quota'] ?? null) : ($dep->quota ?? null)
);

@endphp


<div class="border rounded-xl p-5 space-y-5 departure-item bg-gray-50">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center">
        <h4 class="font-semibold text-gray-700 text-sm">
            Departure #{{ $index + 1 }}
        </h4>

        <button type="button"
                onclick="this.closest('.departure-item').remove()"
                class="text-red-500 text-xs">
            Hapus
        </button>
    </div>


    {{-- ================= BASIC ================= --}}
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


    {{-- ================= ROOM PRICING ================= --}}
    <div class="space-y-3">

        <div class="text-xs font-semibold text-gray-500">
            Harga & Promo per Room
        </div>

        @foreach($roomTypes as $i => $room)

            @php
                $model = is_array($dep)
                    ? ($dep['prices'][$i] ?? [])
                    : optional($dep->prices->firstWhere('room_type',$room));

                $price = old("departures.$index.prices.$i.price", $model['price'] ?? $model->price ?? null);
                $promoType = old("departures.$index.prices.$i.promo_type", $model['promo_type'] ?? $model->promo_type ?? null);
                $promoValue = old("departures.$index.prices.$i.promo_value", $model['promo_value'] ?? $model->promo_value ?? null);
                $promoLabel = old("departures.$index.prices.$i.promo_label", $model['promo_label'] ?? $model->promo_label ?? null);
            @endphp

            <div class="grid grid-cols-12 gap-2 items-center">

                {{-- ROOM --}}
                <div class="col-span-2 text-[11px] font-semibold uppercase">
                    {{ $room }}
                </div>

                {{-- ROOM TYPE --}}
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
                <select
                    name="departures[{{ $index }}][prices][{{ $i }}][promo_type]"
                    class="input col-span-2 text-xs">

                    <option value="">-</option>
                    <option value="percent" @selected($promoType=='percent')>%</option>
                    <option value="fixed" @selected($promoType=='fixed')>Rp</option>

                </select>

                {{-- PROMO VALUE --}}
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