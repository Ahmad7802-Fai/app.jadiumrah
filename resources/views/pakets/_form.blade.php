@csrf

@php
$isEdit = isset($paket) && $paket?->exists;
@endphp

<div class="max-w-6xl mx-auto flex flex-col min-h-[70vh]">

    <div class="space-y-5 flex-1">

        {{-- ================= BASIC + MEDIA ================= --}}
        <div class="grid lg:grid-cols-3 gap-4">

            {{-- LEFT --}}
            <div class="lg:col-span-2 space-y-4">

                <div class="card-compact space-y-4">
                    <h3 class="text-sm font-semibold text-gray-700">Informasi Paket</h3>

                    <div class="grid md:grid-cols-2 gap-3">

                        @foreach([
                            ['name','Nama Paket'],
                            ['code','Kode'],
                            ['departure_city','Kota'],
                            ['airline','Maskapai'],
                            ['duration_days','Durasi','number'],
                        ] as $f)

                        <div class="space-y-1">
                            <label class="text-[11px] text-gray-500">{{ $f[1] }}</label>

                            <input type="{{ $f[2] ?? 'text' }}"
                                name="{{ $f[0] }}"
                                value="{{ old($f[0], $paket->{$f[0]} ?? '') }}"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-1 focus:ring-primary">
                        </div>

                        @endforeach

                    </div>

                    <div class="space-y-1">
                        <label class="text-[11px] text-gray-500">Short Description</label>
                        <textarea name="short_description"
                            rows="2"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-1 focus:ring-primary resize-none">{{ old('short_description', $paket->short_description ?? '') }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[11px] text-gray-500">Description</label>
                        <textarea name="description"
                            rows="3"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-1 focus:ring-primary resize-none">{{ old('description', $paket->description ?? '') }}</textarea>
                    </div>

                </div>

            </div>

            {{-- RIGHT --}}
            <div class="space-y-4">

                <div class="card-compact p-3">
                    @include('pakets.partials.media', [
                        'paket' => $paket ?? null
                    ])
                </div>

                <div class="card-compact p-3 space-y-2 text-sm">

                    <label class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                            @checked(old('is_active', $paket->is_active ?? 1))>
                        <span>Aktif</span>
                    </label>

                    <label class="flex items-center gap-2">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" value="1"
                            @checked(old('is_published', $paket->is_published ?? 0))>
                        <span>Publish</span>
                    </label>

                </div>

            </div>

        </div>

        {{-- ================= HOTEL ================= --}}
        <div class="card-compact space-y-3">
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-semibold text-gray-700">Hotel</h3>
                <button type="button" id="btnAddHotel" class="btn btn-xs">+ Hotel</button>
            </div>

            <div id="hotelWrapper" class="space-y-3">
                @foreach(old('hotels', $paket->hotels ?? []) as $i=>$hotel)
                    @include('pakets.partials.hotel-item', [
                        'index' => $i,
                        'hotel' => $hotel
                    ])
                @endforeach
            </div>

            <template id="hotelTemplate">
                @include('pakets.partials.hotel-item', [
                    'index'=>'__INDEX__',
                    'hotel'=>[]
                ])
            </template>
        </div>

        {{-- ================= ITINERARY ================= --}}
        <div class="card-compact space-y-3">
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-semibold text-gray-700">Itinerary</h3>
                <button type="button" id="btnAddItinerary" class="btn btn-xs">+ Hari</button>
            </div>

            <div id="itineraryWrapper" class="space-y-3">
                @foreach(old('itinerary', $paket->itinerary ?? []) as $i=>$item)
                    @include('pakets.partials.itinerary-item', [
                        'index'=>$i,
                        'item'=>$item,
                        'destinations'=>$destinations
                    ])
                @endforeach
            </div>

            <template id="itineraryTemplate">
                @include('pakets.partials.itinerary-item', [
                    'index'=>'__INDEX__',
                    'item'=>[],
                    'destinations'=>$destinations
                ])
            </template>
        </div>

        {{-- ================= DEPARTURE ================= --}}
        <div class="card-compact space-y-3">
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-semibold text-gray-700">Departure</h3>
                <button type="button" id="btnAddDeparture" class="btn btn-xs">+ Departure</button>
            </div>

            <div id="departureWrapper" class="space-y-3">
                @foreach(old('departures', $paket->departures ?? []) as $i=>$dep)
                    @include('pakets.partials.departure-item', [
                        'index' => $i,
                        'dep' => $dep
                    ])
                @endforeach
            </div>

            <template id="departureTemplate">
                @include('pakets.partials.departure-item', [
                    'index'=>'__INDEX__',
                    'dep'=>[]
                ])
            </template>
        </div>

    </div>

    {{-- ================= SUBMIT (STICK BOTTOM FEEL) ================= --}}
    <div class="pt-4 flex justify-end">
        <button class="btn btn-primary">
            {{ $isEdit ? 'Update' : 'Simpan' }}
        </button>
    </div>

</div>

{{-- ================= MEDIA SCRIPT ================= --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ================= INIT ================= */
    let hotelIndex = document.querySelectorAll('.hotel-item').length
    let itineraryIndex = document.querySelectorAll('.itinerary-item').length
    let departureIndex = document.querySelectorAll('.departure-item').length

    const qs = s => document.querySelector(s)
    const qsa = s => document.querySelectorAll(s)

    const render = (id, i) => {
        const tpl = document.getElementById(id)
        if(!tpl || !tpl.content.firstElementChild) return ''
        return tpl.content.firstElementChild.outerHTML.replaceAll('__INDEX__', i)
    }

    /* =========================================================
       📦 FORM DYNAMIC
    ========================================================= */

    const btnHotel = qs('#btnAddHotel')
    if(btnHotel){
        btnHotel.addEventListener('click', ()=>{
            qs('#hotelWrapper').insertAdjacentHTML('beforeend', render('hotelTemplate', hotelIndex++))
            updateHotel()
        })
    }

    const btnItinerary = qs('#btnAddItinerary')
    if(btnItinerary){
        btnItinerary.addEventListener('click', ()=>{
            qs('#itineraryWrapper').insertAdjacentHTML('beforeend', render('itineraryTemplate', itineraryIndex++))
            updateDay()
        })
    }

    const btnDeparture = qs('#btnAddDeparture')
    if(btnDeparture){
        btnDeparture.addEventListener('click', ()=>{
            qs('#departureWrapper').insertAdjacentHTML('beforeend', render('departureTemplate', departureIndex++))
            updateDeparture()
        })
    }

    document.addEventListener('click', e=>{
        if(e.target.closest('.btn-remove-hotel')){
            e.target.closest('.hotel-item')?.remove()
            updateHotel()
        }
        if(e.target.closest('.btn-remove-itinerary')){
            e.target.closest('.itinerary-item')?.remove()
            updateDay()
        }
        if(e.target.closest('.btn-remove-departure')){
            e.target.closest('.departure-item')?.remove()
            updateDeparture()
        }
    })

    /* ================= DESTINATION ================= */
    document.addEventListener('change', e=>{
        if(e.target.classList.contains('destination-select')){
            const wrap = e.target.closest('.itinerary-item')
            const input = wrap?.querySelector('.manual-destination')

            if(!input) return

            if(e.target.value === '__new__'){
                input.classList.remove('hidden')
                input.required = true
            }else{
                input.classList.add('hidden')
                input.required = false
                input.value = ''
            }
        }
    })

    /* ================= ORDER ================= */
    function updateHotel(){
        qsa('.hotel-item').forEach((el,i)=>{
            const label = el.querySelector('.hotel-label')
            if(label) label.innerText = "Hotel "+(i+1)
        })
    }

    function updateDay(){
        qsa('.itinerary-item').forEach((el,i)=>{
            const label = el.querySelector('.day-label')
            const input = el.querySelector('.day-order-input')

            if(label) label.innerText = "Hari "+(i+1)
            if(input) input.value = i+1
        })
    }

    function updateDeparture(){
        qsa('.departure-item').forEach((el,i)=>{
            const label = el.querySelector('.departure-label')
            if(label) label.innerText = "Departure "+(i+1)
        })
    }

    updateHotel()
    updateDay()
    updateDeparture()

    qsa('.destination-select').forEach(el=>{
        el.dispatchEvent(new Event('change'))
    })

})
</script>
