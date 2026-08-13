@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Edit Berita</h4>

        <a href="{{ route('admin.berita.index') }}"
           class="btn btn-light border rounded-pill px-4 shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Card -->
    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-body">

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="alert alert-danger rounded-4">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.berita.update', $item->id) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="row">

                    <!-- Judul -->
                    <div class="col-md-8 mb-3">
                        <label class="fw-semibold">Judul Berita</label>
                        <input type="text"
                               name="judul"
                               value="{{ old('judul', $item->judul) }}"
                               class="form-control rounded-pill @error('judul') is-invalid @enderror"
                               placeholder="Masukkan judul berita...">
                    </div>

                    <!-- Kategori -->
                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Kategori</label>
                        <input type="text"
                               name="kategori"
                               value="{{ old('kategori', $item->kategori) }}"
                               class="form-control rounded-pill"
                               placeholder="cth: Umrah, Tips, Edukasi">
                    </div>

                </div>

                <!-- Konten -->
                <div class="mb-3">
                    <label class="fw-semibold">Konten Berita</label>

                    <textarea name="konten"
                              id="summernote"
                              class="form-control">
                        {!! old('konten', $item->konten) !!}
                    </textarea>
                </div>

                <!-- Thumbnail -->
                <div class="mb-3">
                    <label class="fw-semibold">Thumbnail</label>
                    <input type="file"
                           name="thumbnail"
                           accept="image/*"
                           class="form-control rounded-pill"
                           onchange="previewImage(event)">

                    <div class="mt-3">
                        <label class="fw-semibold">Preview:</label><br>

                        <img id="imgPreview"
                             src="{{ $item->thumbnail ? asset('storage/'.$item->thumbnail) : asset('noimage.png') }}"
                             class="rounded"
                             style="max-height: 180px; border:1px solid #ddd;">
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-4 text-end">
                    <button class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> Update Berita
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection


@push('scripts')
<!-- Summernote -->
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    $('#summernote').summernote({
        height: 280,
        placeholder: "Tulis konten berita di sini...",
        styleTags: ['p', 'h3', 'h4'],
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen']]
        ]
    });
});

// Preview thumbnail realtime
function previewImage(event) {
    let img = document.getElementById('imgPreview');
    img.src = URL.createObjectURL(event.target.files[0]);
    img.onload = () => URL.revokeObjectURL(img.src);
}
</script>
@endpush
