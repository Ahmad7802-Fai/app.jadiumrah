@php
    $isEdit = isset($item);
@endphp

<div class="row">

    <!-- Title -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Judul</label>
        <input type="text" name="title"
               class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $item->title ?? '') }}"
               placeholder="Masukkan judul foto...">

        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Category -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Kategori</label>
        <input type="text" name="category"
               class="form-control @error('category') is-invalid @enderror"
               value="{{ old('category', $item->category ?? '') }}"
               placeholder="Contoh: Kegiatan, Masjid, Hotel, dll">

        @error('category')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Foto -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Foto</label>

        <input type="file" name="photo" accept="image/*"
               class="form-control @error('photo') is-invalid @enderror"
               onchange="previewImage(event)">

        @error('photo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Preview -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Preview</label>

        <div class="border rounded p-2"
             style="width:160px;height:160px;overflow:hidden;display:flex;align-items:center;justify-content:center;">

            <img id="preview"
                 src="{{ $isEdit && $item->photo ? asset('storage/'.$item->photo) : 'https://via.placeholder.com/160?text=No+Image' }}"
                 class="img-fluid"
                 style="object-fit:cover;width:160px;height:160px;">
        </div>
    </div>

</div>

<!-- Buttons -->
<div class="mt-4 d-flex gap-2">
    <a href="{{ route('admin.gallery.index') }}" class="btn btn-light rounded-pill px-4">
        <i class="fas fa-arrow-left me-2"></i> Kembali
    </a>

    <button class="btn btn-primary rounded-pill px-4">
        <i class="fas fa-save me-2"></i> {{ $isEdit ? 'Update' : 'Simpan' }}
    </button>
</div>

@push('scripts')
<script>
function previewImage(event) {
    let reader = new FileReader();
    reader.onload = function(){
        document.getElementById('preview').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endpush
