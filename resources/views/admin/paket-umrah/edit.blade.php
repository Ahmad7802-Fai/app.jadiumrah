@extends('layouts.admin')

@section('content')

@php
    $title = "Edit Paket Umrah";
@endphp

<div class="container-fluid">

    <h4 class="fw-bold mb-4">Edit Paket Umrah</h4>

    <form action="{{ route('admin.paket-umrah.update', $item->id) }}"
          method="POST" enctype="multipart/form-data"
          class="card shadow-sm border-0 rounded-4 p-4">
        @csrf
        @method('PUT')

        <div class="row">

            <!-- JUDUL -->
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Judul Paket</label>
                <input type="text" name="title"
                    class="form-control rounded-pill"
                    value="{{ old('title', $item->title) }}" required>
            </div>

            <!-- SEO TITLE -->
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">SEO Title</label>
                <input type="text" name="seo_title"
                    class="form-control rounded-pill"
                    value="{{ old('seo_title', $item->seo_title) }}" required>
            </div>

            <!-- TGL BERANGKAT -->
            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Tanggal Berangkat</label>
                <input type="date" name="tglberangkat"
                    class="form-control rounded-pill"
                    value="{{ $item->tglberangkat }}" required>
            </div>

            <!-- PESAWAT -->
            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Pesawat</label>
                <input type="text" name="pesawat"
                    class="form-control rounded-pill"
                    value="{{ $item->pesawat }}" required>
            </div>

            <!-- FLIGHT -->
            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Flight</label>
                <input type="text" name="flight"
                    class="form-control rounded-pill"
                    value="{{ $item->flight }}" required>
            </div>

            <!-- DURASI -->
            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Durasi (hari)</label>
                <input type="number" name="durasi"
                    class="form-control rounded-pill"
                    value="{{ $item->durasi }}" required>
            </div>

            <!-- SEAT -->
            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Seat</label>
                <input type="number" name="seat"
                    class="form-control rounded-pill"
                    value="{{ $item->seat }}" required>
            </div>

            <!-- FOTO -->
            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Thumbnail Paket</label>
                <input type="file" name="photo" class="form-control rounded-pill"
                       accept="image/*" onchange="previewImg(event)">
                
                <div class="mt-2">
                    @if($item->photo)
                        <img id="preview"
                             src="{{ asset('storage/'.$item->photo) }}"
                             style="width:180px;height:120px;object-fit:cover;"
                             class="rounded">
                    @else
                        <img id="preview" style="display:none;width:180px;height:120px;" class="rounded">
                    @endif
                </div>
            </div>

            <!-- HOTEL MEKKAH -->
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Hotel Mekkah</label>
                <input type="text" name="hotmekkah"
                    class="form-control rounded-pill"
                    value="{{ $item->hotmekkah }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Rating Hotel Mekkah</label>
                <input type="number" name="rathotmekkah"
                    class="form-control rounded-pill"
                    value="{{ $item->rathotmekkah }}" required>
            </div>

            <!-- HOTEL MADINAH -->
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Hotel Madinah</label>
                <input type="text" name="hotmadinah"
                    class="form-control rounded-pill"
                    value="{{ $item->hotmadinah }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Rating Hotel Madinah</label>
                <input type="number" name="rathotmadinah"
                    class="form-control rounded-pill"
                    value="{{ $item->rathotmadinah }}" required>
            </div>

            <!-- HARGA -->
            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Harga Quad</label>
                <input type="number" name="quad"
                    class="form-control rounded-pill"
                    value="{{ $item->quad }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Harga Triple</label>
                <input type="number" name="triple"
                    class="form-control rounded-pill"
                    value="{{ $item->triple }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Harga Double</label>
                <input type="number" name="double"
                    class="form-control rounded-pill"
                    value="{{ $item->double }}" required>
            </div>

            <!-- ITIN -->
            <div class="col-12 mb-3">
                <label class="fw-semibold">Itinerary</label>
                <textarea name="itin" class="form-control" rows="4" required>{{ $item->itin }}</textarea>
            </div>

            <!-- FASILITAS -->
            <div class="col-12 mb-3">
                <label class="fw-semibold d-block mb-2">Fasilitas Tambahan</label>

                <div class="mb-2">
                    <label class="fw-semibold">Thaif:</label>
                    <select name="thaif" class="form-control rounded-pill">
                        <option value="Ya" {{ $item->thaif=='Ya'?'selected':'' }}>Ya</option>
                        <option value="Tidak" {{ $item->thaif=='Tidak'?'selected':'' }}>Tidak</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="fw-semibold">Dubai:</label>
                    <select name="dubai" class="form-control rounded-pill">
                        <option value="Ya" {{ $item->dubai=='Ya'?'selected':'' }}>Ya</option>
                        <option value="Tidak" {{ $item->dubai=='Tidak'?'selected':'' }}>Tidak</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="fw-semibold">Kereta Cepat:</label>
                    <select name="kereta" class="form-control rounded-pill">
                        <option value="Ya" {{ $item->kereta=='Ya'?'selected':'' }}>Ya</option>
                        <option value="Tidak" {{ $item->kereta=='Tidak'?'selected':'' }}>Tidak</option>
                    </select>
                </div>
            </div>

            <!-- DESKRIPSI -->
            <div class="col-12 mb-3">
                <label class="fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4" required>{{ $item->deskripsi }}</textarea>
            </div>

            <!-- STATUS -->
            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Status</label>
                <select name="status" class="form-control rounded-pill">
                    <option value="Aktif" {{ $item->status=='Aktif'?'selected':'' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ $item->status=='Tidak Aktif'?'selected':'' }}>Tidak Aktif</option>
                </select>
            </div>

        </div>

        <div class="mt-4">
            <button class="btn btn-primary rounded-pill px-4">Update</button>

            <a href="{{ route('admin.paket-umrah.index') }}"
               class="btn btn-light rounded-pill px-4">Kembali</a>
        </div>

    </form>

</div>

@endsection

@push('scripts')
<script>
function previewImg(event) {
    let img = document.getElementById('preview');
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
}
</script>
@endpush
