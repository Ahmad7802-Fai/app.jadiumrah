@csrf

@php
$isEdit = isset($paket) && $paket?->exists;
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

            <textarea name="short_description" class="input mt-4">{{ old('short_description', $paket->short_description ?? '') }}</textarea>
            <textarea name="description" class="input mt-3">{{ old('description', $paket->description ?? '') }}</textarea>
        </div>
    </div>

    {{-- RIGHT --}}
    <div class="space-y-6">

        {{-- MEDIA --}}
        <div class="card">
            <h3 class="title">Media</h3>

            @if($isEdit && $paket?->thumbnail)
                <img src="{{ asset('storage/'.$paket->thumbnail) }}"
                     class="w-full h-40 object-cover rounded mb-3">
            @endif

            <input type="file" name="thumbnail" class="input">

            {{-- MULTI GALLERY --}}
            <input type="file" name="gallery[]" multiple class="input mt-3" id="galleryInput">

            <div id="galleryPreview" class="grid grid-cols-3 gap-2 mt-3"></div>
        </div>

        {{-- STATUS --}}
        <div class="card space-y-3">
            <label class="flex gap-2 items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                    @checked(old('is_active', $paket->is_active ?? 1))>
                Aktif
            </label>

            <label class="flex gap-2 items-center">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1"
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
<div class="card">
    <div class="flex justify-between">
        <h3 class="title">Itinerary</h3>
        <button type="button" id="btnAddItinerary" class="btn">+ Hari</button>
    </div>

    <div id="itineraryWrapper" class="space-y-4">
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
<div class="card">
    <div class="flex justify-between">
        <h3 class="title">Departure</h3>
        <button type="button" id="btnAddDeparture" class="btn">+ Departure</button>
    </div>

    <div id="departureWrapper" class="space-y-4">
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
{{-- ================= SUBMIT --}}
<div class="text-right">
    <button class="btn btn-primary">
        {{ $isEdit ? 'Update' : 'Simpan' }}
    </button>
</div>

</div>

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
       🚀 GALLERY PRO MAX
    ========================================================= */

    let files = []
    const input = qs('#galleryInput')
    const preview = qs('#galleryPreview')
    const form = document.querySelector('form')

    /* ================= DRAG DROP ================= */
    if(preview){
        preview.addEventListener('dragover', e=>{
            e.preventDefault()
            preview.classList.add('ring-2','ring-green-400')
        })

        preview.addEventListener('dragleave', ()=>{
            preview.classList.remove('ring-2','ring-green-400')
        })

        preview.addEventListener('drop', e=>{
            e.preventDefault()
            preview.classList.remove('ring-2','ring-green-400')

            const dropped = Array.from(e.dataTransfer.files)
            dropped.forEach(f => addFile(f))
        })
    }

    /* ================= INPUT SELECT ================= */
    if(input){
        input.addEventListener('change', e=>{
            Array.from(e.target.files).forEach(f => addFile(f))
            input.value = ''
        })
    }

    /* ================= ADD FILE ================= */
    function addFile(file){
        if(!file.type.startsWith('image/')) return

        compressImage(file).then(compressed=>{
            files.push(compressed)
            renderGallery()
        })
    }

    /* ================= COMPRESS IMAGE ================= */
    function compressImage(file){
        return new Promise(resolve=>{
            const img = new Image()
            const reader = new FileReader()

            reader.onload = e => {
                img.src = e.target.result
            }

            img.onload = () => {
                const canvas = document.createElement('canvas')
                const ctx = canvas.getContext('2d')

                const MAX = 1200
                let w = img.width
                let h = img.height

                if(w > h && w > MAX){
                    h *= MAX / w
                    w = MAX
                }else if(h > MAX){
                    w *= MAX / h
                    h = MAX
                }

                canvas.width = w
                canvas.height = h

                ctx.drawImage(img,0,0,w,h)

                canvas.toBlob(blob=>{
                    const newFile = new File([blob], file.name, {
                        type: 'image/jpeg'
                    })
                    resolve(newFile)
                }, 'image/jpeg', 0.8)
            }

            reader.readAsDataURL(file)
        })
    }

    /* ================= RENDER ================= */
    function renderGallery(){
        preview.innerHTML = ''

        files.forEach((file,index)=>{
            const reader = new FileReader()

            reader.onload = e=>{
                preview.insertAdjacentHTML('beforeend', `
                    <div class="relative group cursor-move" draggable="true" data-index="${index}">

                        <img src="${e.target.result}" class="h-24 w-full object-cover rounded">

                        <button type="button"
                            class="remove-gallery absolute top-1 right-1 bg-red-500 text-white text-xs px-1 rounded"
                            data-index="${index}">
                            ×
                        </button>

                        <div class="absolute bottom-0 left-0 w-full bg-black/50 text-white text-[10px] text-center">
                            ${Math.round(file.size/1024)} KB
                        </div>

                    </div>
                `)
            }

            reader.readAsDataURL(file)
        })
    }

    /* ================= REMOVE ================= */
    document.addEventListener('click', e=>{
        if(e.target.classList.contains('remove-gallery')){
            const i = parseInt(e.target.dataset.index)
            files.splice(i,1)
            renderGallery()
        }
    })

    /* ================= REORDER (DRAG SORT) ================= */
    let dragIndex = null

    preview?.addEventListener('dragstart', e=>{
        dragIndex = e.target.closest('[data-index]')?.dataset.index
    })

    preview?.addEventListener('drop', e=>{
        const target = e.target.closest('[data-index]')
        if(!target) return

        const targetIndex = target.dataset.index

        const temp = files[dragIndex]
        files.splice(dragIndex,1)
        files.splice(targetIndex,0,temp)

        renderGallery()
    })

    /* ================= SUBMIT (SET FILES) ================= */
    if(form){
        form.addEventListener('submit', e=>{

            /* progress UI */
            const btn = form.querySelector('button')
            if(btn){
                btn.innerText = 'Uploading...'
                btn.disabled = true
            }

            const dt = new DataTransfer()
            files.forEach(f => dt.items.add(f))
            input.files = dt.files
        })
    }

    /* =========================================================
       📦 FORM DYNAMIC (FIX NO OPTIONAL CHAINING)
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