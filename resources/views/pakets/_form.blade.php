@csrf

@php
$isEdit = isset($paket) && $paket->exists;
@endphp

<div class="max-w-7xl mx-auto space-y-8">

{{-- ================= BASIC + MEDIA ================= --}}
<div class="grid lg:grid-cols-3 gap-6">

    {{-- LEFT --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- BASIC --}}
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
                        value="{{ old($f[0],$paket->{$f[0]} ?? '') }}"
                        class="input">
                </div>

                @endforeach

            </div>

            <textarea name="short_description" class="input mt-4" placeholder="Deskripsi singkat">
                {{ old('short_description',$paket->short_description ?? '') }}
            </textarea>

            <textarea name="description" class="input mt-3" rows="4" placeholder="Deskripsi lengkap">
                {{ old('description',$paket->description ?? '') }}
            </textarea>
        </div>

    </div>

    {{-- RIGHT --}}
    <div class="space-y-6">

        {{-- MEDIA --}}
        <div class="card">
            <h3 class="title">Media</h3>

            @if($isEdit && $paket->thumbnail)
                <img src="{{ asset('storage/'.$paket->thumbnail) }}"
                     class="w-full h-40 object-cover rounded mb-3">
            @endif

            <input type="file" name="thumbnail" class="input">

            <input type="file" name="gallery[]" multiple class="input mt-3" id="galleryInput">

            <div id="galleryPreview" class="grid grid-cols-3 gap-2 mt-3"></div>
        </div>

        {{-- STATUS --}}
        <div class="card space-y-3">

            <label class="flex gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                    @checked(old('is_active',$paket->is_active ?? 1))>
                Aktif
            </label>

            <label class="flex gap-2">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1"
                    @checked(old('is_published',$paket->is_published ?? 0))>
                Publish
            </label>

        </div>

    </div>

</div>

{{-- ================= HOTEL ================= --}}
<div class="card">
    <div class="flex justify-between">
        <h3 class="title">Hotel</h3>
        <button type="button" onclick="addHotel()" class="btn">+ Hotel</button>
    </div>

    <div id="hotelWrapper" class="space-y-3">
        @foreach(old('hotels',$paket->hotels ?? []) as $i=>$hotel)
            @include('pakets.partials.hotel-item',['index'=>$i,'hotel'=>$hotel])
        @endforeach
    </div>
</div>

{{-- ================= ITINERARY ================= --}}
<div class="card">
    <div class="flex justify-between">
        <h3 class="title">Itinerary</h3>
        <button type="button" onclick="addItinerary()" class="btn">+ Hari</button>
    </div>

    <div id="itineraryWrapper" class="space-y-3">
        @foreach(old('itinerary',$paket->itinerary ?? []) as $i=>$item)
            @include('pakets.partials.itinerary-item',['index'=>$i,'item'=>$item])
        @endforeach
    </div>
</div>

{{-- ================= DEPARTURE ================= --}}
<div class="card">
    <div class="flex justify-between">
        <h3 class="title">Departure</h3>
        <button type="button" onclick="addDeparture()" class="btn">+ Departure</button>
    </div>

    <div id="departureWrapper" class="space-y-4">
        @foreach(old('departures',$paket->departures ?? []) as $i=>$dep)
            @include('pakets.partials.departure-item',['index'=>$i,'dep'=>$dep])
        @endforeach
    </div>
</div>

{{-- ================= SUBMIT ================= --}}
<div class="text-right">
    <button class="btn btn-primary">
        {{ $isEdit ? 'Update' : 'Simpan' }}
    </button>
</div>

</div> 

<script>
document.addEventListener('DOMContentLoaded',()=>{

window.hotelIndex = document.querySelectorAll('.hotel-item').length
window.itineraryIndex = document.querySelectorAll('.itinerary-item').length
window.departureIndex = document.querySelectorAll('.departure-item').length

const input = document.getElementById('galleryInput')
const preview = document.getElementById('galleryPreview')

if(input){
input.onchange = e=>{
preview.innerHTML=''
[...e.target.files].forEach(f=>{
const r=new FileReader()
r.onload=x=>{
preview.innerHTML+=`<img src="${x.target.result}" class="h-24 w-full object-cover rounded">`
}
r.readAsDataURL(f)
})
}
}

})
</script>