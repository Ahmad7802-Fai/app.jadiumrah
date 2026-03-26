@csrf

@php
$isEdit = isset($paket) && $paket->exists;
@endphp

<div class="max-w-7xl mx-auto space-y-8">

{{-- ================= BASIC + MEDIA ================= --}}
<div class="grid lg:grid-cols-3 gap-6">

    {{-- LEFT --}}
    <div class="lg:col-span-2 space-y-6">

        <div class="card">
            <h3 class="title">Informasi Paket</h3>

            <div class="grid md:grid-cols-2 gap-4">

                @foreach([
                    ['name','Nama Paket'],
                    ['code','Kode Paket'],
                    ['departure_city','Kota Keberangkatan'],
                    ['airline','Maskapai'],
                    ['duration_days','Durasi (Hari)','number'],
                    ['quota','Quota Default','number'],
                ] as $f)

                <div>
                    <label class="label">{{ $f[1] }}</label>
                    <input type="{{ $f[2] ?? 'text' }}"
                        name="{{ $f[0] }}"
                        value="{{ old($f[0], $paket->{$f[0]} ?? '') }}"
                        class="input">
                </div>

                @endforeach

            </div>

            <textarea name="short_description" class="input mt-4">
{{ old('short_description', $paket->short_description ?? '') }}
            </textarea>

            <textarea name="description" class="input mt-3">
{{ old('description', $paket->description ?? '') }}
            </textarea>

        </div>

    </div>

    {{-- RIGHT --}}
    <div class="space-y-6">

        {{-- ================= MEDIA ================= --}}
        <div class="card">
            <h3 class="title">Media</h3>

            @if($isEdit && $paket->thumbnail)
                <img src="{{ asset('storage/'.$paket->thumbnail) }}"
                     class="w-full h-40 object-cover rounded mb-3">
            @endif

            <input type="file" name="thumbnail" class="input">

            <input type="file"
                   name="gallery[]"
                   multiple
                   class="input mt-3"
                   id="galleryInput">

            <div id="galleryPreview"
                 class="grid grid-cols-3 gap-2 mt-3"></div>
        </div>

        {{-- ================= STATUS ================= --}}
        <div class="card space-y-3">

            <label class="flex gap-2 items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       @checked(old('is_active', $paket->is_active ?? 1))>
                Aktif
            </label>

            <label class="flex gap-2 items-center">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox"
                       name="is_published"
                       value="1"
                       @checked(old('is_published', $paket->is_published ?? 0))>
                Publish
            </label>

        </div>

    </div>

</div>
{{-- ================= HOTEL ================= --}}
<div class="card">
    <div class="flex justify-between">
        <h3 class="title">Hotel</h3>
        <button type="button" id="btnAddHotel" class="btn">+ Hotel</button>
    </div>

    <div id="hotelWrapper" class="space-y-4">
        @foreach(old('hotels', $paket->hotels ?? []) as $i=>$hotel)
            @include('pakets.partials.hotel-item',[
                'index'=>$i,
                'hotel'=>$hotel
            ])
        @endforeach
    </div>

    <template id="hotelTemplate">
        @include('pakets.partials.hotel-item',[
            'index'=>'__INDEX__',
            'hotel'=>[]
        ])
    </template>
</div>

{{-- ================= ITINERARY ================= --}}
<div class="card">
    <div class="flex justify-between">
        <h3 class="title">Itinerary</h3>
        <button type="button" id="btnAddItinerary" class="btn">+ Hari</button>
    </div>

    <div id="itineraryWrapper" class="space-y-4">
        @foreach(old('itinerary', $paket->itinerary ?? []) as $i=>$item)
            @include('pakets.partials.itinerary-item',[
                'index'=>$i,
                'item'=>$item,
                'destinations'=>$destinations
            ])
        @endforeach
    </div>

    <template id="itineraryTemplate">
        @include('pakets.partials.itinerary-item',[
            'index'=>'__INDEX__',
            'item'=>[],
            'destinations'=>$destinations
        ])
    </template>
</div>

{{-- ================= DEPARTURE ================= --}}
<div class="card">
    <div class="flex justify-between">
        <h3 class="title">Departure</h3>
        <button type="button" id="btnAddDeparture" class="btn">+ Departure</button>
    </div>

    <div id="departureWrapper" class="space-y-4">
        @foreach(old('departures', $paket->departures ?? []) as $i=>$dep)
            @include('pakets.partials.departure-item',[
                'index'=>$i,
                'dep'=>$dep
            ])
        @endforeach
    </div>

    <template id="departureTemplate">
        @include('pakets.partials.departure-item',[
            'index'=>'__INDEX__',
            'dep'=>[]
        ])
    </template>
</div>

{{-- ================= SUBMIT ================= --}}
<div class="text-right">
    <button class="btn btn-primary">
        {{ $isEdit ? 'Update' : 'Simpan' }}
    </button>
</div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ================= INIT INDEX ================= */
    let hotelIndex     = document.querySelectorAll('.hotel-item').length
    let itineraryIndex = document.querySelectorAll('.itinerary-item').length
    let departureIndex = document.querySelectorAll('.departure-item').length


    /* ================= HELPER ================= */

    const $ = (selector) => document.querySelector(selector)
    const $$ = (selector) => document.querySelectorAll(selector)

    function renderTemplate(id, index){
        const tpl = document.getElementById(id)
        if(!tpl || !tpl.content.firstElementChild) return ''

        return tpl.content.firstElementChild.outerHTML
            .replaceAll('__INDEX__', index)
    }


    /* ================= GALLERY PREVIEW ================= */

    const galleryInput   = $('#galleryInput')
    const galleryPreview = $('#galleryPreview')

    if(galleryInput && galleryPreview){
        galleryInput.addEventListener('change', (e) => {

            galleryPreview.innerHTML = ''

            Array.from(e.target.files).forEach(file => {

                const reader = new FileReader()

                reader.onload = (ev) => {
                    const img = document.createElement('img')
                    img.src = ev.target.result
                    img.className = "h-24 w-full object-cover rounded"
                    galleryPreview.appendChild(img)
                }

                reader.readAsDataURL(file)

            })
        })
    }


    /* ================= ADD ================= */

    $('#btnAddHotel')?.addEventListener('click', () => {

        const html = renderTemplate('hotelTemplate', hotelIndex)
        if(!html) return

        $('#hotelWrapper')?.insertAdjacentHTML('beforeend', html)

        hotelIndex++
        updateHotelOrder()

    })


    $('#btnAddItinerary')?.addEventListener('click', () => {

        const html = renderTemplate('itineraryTemplate', itineraryIndex)
        if(!html) return

        $('#itineraryWrapper')?.insertAdjacentHTML('beforeend', html)

        itineraryIndex++
        updateDayOrder()

    })


    $('#btnAddDeparture')?.addEventListener('click', () => {

        const html = renderTemplate('departureTemplate', departureIndex)
        if(!html) return

        $('#departureWrapper')?.insertAdjacentHTML('beforeend', html)

        departureIndex++
        updateDepartureOrder()

    })


    /* ================= REMOVE ================= */

    document.addEventListener('click', function(e){

        if(e.target.closest('.btn-remove-hotel')){
            e.target.closest('.hotel-item')?.remove()
            updateHotelOrder()
        }

        if(e.target.closest('.btn-remove-itinerary')){
            e.target.closest('.itinerary-item')?.remove()
            updateDayOrder()
        }

        if(e.target.closest('.btn-remove-departure')){
            e.target.closest('.departure-item')?.remove()
            updateDepartureOrder()
        }

    })


    /* ================= ORDER ================= */

    function updateHotelOrder(){
        $$('.hotel-item').forEach((el, i)=>{
            const label = el.querySelector('.hotel-label')
            if(label) label.innerText = "Hotel " + (i+1)
        })
    }

    function updateDayOrder(){
        $$('.itinerary-item').forEach((el, i)=>{
            const label = el.querySelector('.day-label')
            const input = el.querySelector('.day-order-input')

            if(label) label.innerText = "Hari " + (i+1)
            if(input) input.value = i+1
        })
    }

    function updateDepartureOrder(){
        $$('.departure-item').forEach((el, i)=>{
            const label = el.querySelector('.departure-label')
            if(label) label.innerText = "Departure " + (i+1)
        })
    }


    /* ================= INIT ================= */

    updateHotelOrder()
    updateDayOrder()
    updateDepartureOrder()

})
</script>