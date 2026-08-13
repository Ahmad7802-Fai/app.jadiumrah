@extends('layouts.admin')

@section('content')

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <h5 class="fw-bold mb-4">Tambah Berita</h5>

        <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Judul -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Judul</label>
                <input type="text" name="judul"
                       class="form-control rounded-3 @error('judul') is-invalid @enderror"
                       value="{{ old('judul') }}"
                       placeholder="Masukkan judul berita...">

                @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kategori -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Kategori</label>
                <input type="text" name="kategori"
                       class="form-control rounded-3 @error('kategori') is-invalid @enderror"
                       value="{{ old('kategori') }}"
                       placeholder="Contoh: Umroh, Travel, Edukasi">

                @error('kategori')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Thumbnail -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Thumbnail</label>

                <input type="file" name="thumbnail" id="thumbnail"
                       class="form-control rounded-3 @error('thumbnail') is-invalid @enderror"
                       accept="image/*">

                @error('thumbnail')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <!-- Preview -->
                <div class="mt-3">
                    <img id="preview-thumbnail"
                         src=""
                         class="rounded shadow-sm d-none"
                         style="max-height: 180px;">
                </div>
            </div>

            <!-- Konten -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Konten</label>
                <textarea id="editor-konten" name="konten"
                          class="form-control @error('konten') is-invalid @enderror">
                    {{ old('konten') }}
                </textarea>

                @error('konten')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.berita.index') }}"
                   class="btn btn-light border rounded-pill px-4">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>

                <button class="btn btn-primary rounded-pill px-5">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>

        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // Thumbnail Preview
    document.getElementById('thumbnail').addEventListener('change', function(e) {
        let file = e.target.files[0];
        let preview = document.getElementById('preview-thumbnail');

        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        }
    });

    // SUMMERNOTE INIT
    $('#editor-konten').summernote({
        height: 300,
        placeholder: 'Tulis konten berita...',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['fontsize', 'color']],
            ['insert', ['picture', 'link', 'video']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });
</script>
@endpush
