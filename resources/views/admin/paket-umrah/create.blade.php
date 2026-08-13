@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <h4 class="fw-bold mb-4">Tambah Paket Umrah</h4>

    <form action="{{ route('admin.paket-umrah.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="card shadow-sm border-0 rounded-4 p-4">
        @csrf

        <div class="row">

            {{-- ===============================
               JUDUL & SEO
            =============================== --}}
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Judul Paket</label>
                <input type="text"
                       name="title"
                       id="title"
                       class="form-control rounded-pill @error('title') is-invalid @enderror"
                       value="{{ old('title') }}"
                       required>

                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-semibold">SEO Title</label>
                <input type="text"
                       name="seo_title"
                       id="seo_title"
                       class="form-control rounded-pill @error('seo_title') is-invalid @enderror"
                       value="{{ old('seo_title') }}"
                       placeholder="Otomatis dari judul">

                <small class="text-muted">
                    Otomatis dari judul, bisa diedit manual
                </small>

                @error('seo_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- ===============================
               BASIC INFO
            =============================== --}}
            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Tanggal Berangkat</label>
                <input type="date"
                       name="tglberangkat"
                       class="form-control rounded-pill @error('tglberangkat') is-invalid @enderror"
                       value="{{ old('tglberangkat') }}"
                       required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Pesawat</label>
                <input type="text"
                       name="pesawat"
                       class="form-control rounded-pill @error('pesawat') is-invalid @enderror"
                       value="{{ old('pesawat') }}"
                       required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Flight</label>
                <input type="text"
                       name="flight"
                       class="form-control rounded-pill @error('flight') is-invalid @enderror"
                       value="{{ old('flight') }}"
                       required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Durasi (Hari)</label>
                <input type="number"
                       name="durasi"
                       class="form-control rounded-pill @error('durasi') is-invalid @enderror"
                       value="{{ old('durasi') }}"
                       required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Seat</label>
                <input type="number"
                       name="seat"
                       class="form-control rounded-pill @error('seat') is-invalid @enderror"
                       value="{{ old('seat') }}"
                       required>
            </div>

            {{-- ===============================
               FOTO
            =============================== --}}
            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Thumbnail Paket</label>
                <input type="file"
                       name="photo"
                       class="form-control @error('photo') is-invalid @enderror"
                       accept="image/*"
                       onchange="previewImg(event)">

                <img id="preview"
                     class="mt-2 rounded"
                     style="width:180px;height:120px;object-fit:cover;display:none;">
            </div>

            {{-- ===============================
               HOTEL
            =============================== --}}
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Hotel Mekkah</label>
                <input type="text"
                       name="hotmekkah"
                       class="form-control rounded-pill"
                       value="{{ old('hotmekkah') }}"
                       required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Rating Hotel Mekkah</label>
                <input type="number"
                       name="rathotmekkah"
                       class="form-control rounded-pill"
                       value="{{ old('rathotmekkah') }}"
                       required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Hotel Madinah</label>
                <input type="text"
                       name="hotmadinah"
                       class="form-control rounded-pill"
                       value="{{ old('hotmadinah') }}"
                       required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Rating Hotel Madinah</label>
                <input type="number"
                       name="rathotmadinah"
                       class="form-control rounded-pill"
                       value="{{ old('rathotmadinah') }}"
                       required>
            </div>

            {{-- ===============================
               HARGA
            =============================== --}}
            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Harga Quad</label>
                <input type="number"
                       name="quad"
                       class="form-control rounded-pill"
                       value="{{ old('quad') }}"
                       required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Harga Triple</label>
                <input type="number"
                       name="triple"
                       class="form-control rounded-pill"
                       value="{{ old('triple') }}"
                       required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Harga Double</label>
                <input type="number"
                       name="double"
                       class="form-control rounded-pill"
                       value="{{ old('double') }}"
                       required>
            </div>

            {{-- ===============================
               ITINERARY & DESKRIPSI
            =============================== --}}
            <div class="col-12 mb-3">
                <label class="fw-semibold">Itinerary</label>
                <textarea name="itin"
                          class="form-control"
                          rows="4"
                          required>{{ old('itin') }}</textarea>
            </div>
            {{-- ===============================
            FASILITAS TAMBAHAN
            =============================== --}}
            <div class="col-12 mb-3">
                <label class="fw-semibold d-block mb-2">Fasilitas Tambahan</label>

                {{-- THAIF --}}
                <div class="mb-2">
                    <span class="fw-semibold me-3">Thaif</span>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input"
                            type="radio"
                            name="thaif"
                            value="Ya"
                            {{ old('thaif', 'Ya') === 'Ya' ? 'checked' : '' }}>
                        <label class="form-check-label">Ya</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input"
                            type="radio"
                            name="thaif"
                            value="Tidak"
                            {{ old('thaif') === 'Tidak' ? 'checked' : '' }}>
                        <label class="form-check-label">Tidak</label>
                    </div>
                </div>

                {{-- DUBAI --}}
                <div class="mb-2">
                    <span class="fw-semibold me-3">Dubai</span>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input"
                            type="radio"
                            name="dubai"
                            value="Ya"
                            {{ old('dubai', 'Ya') === 'Ya' ? 'checked' : '' }}>
                        <label class="form-check-label">Ya</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input"
                            type="radio"
                            name="dubai"
                            value="Tidak"
                            {{ old('dubai') === 'Tidak' ? 'checked' : '' }}>
                        <label class="form-check-label">Tidak</label>
                    </div>
                </div>

                {{-- KERETA CEPAT --}}
                <div>
                    <span class="fw-semibold me-3">Kereta Cepat</span>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input"
                            type="radio"
                            name="kereta"
                            value="Ya"
                            {{ old('kereta', 'Ya') === 'Ya' ? 'checked' : '' }}>
                        <label class="form-check-label">Ya</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input"
                            type="radio"
                            name="kereta"
                            value="Tidak"
                            {{ old('kereta') === 'Tidak' ? 'checked' : '' }}>
                        <label class="form-check-label">Tidak</label>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-3">
                <label class="fw-semibold">Deskripsi</label>
                <textarea name="deskripsi"
                          class="form-control"
                          rows="4"
                          required>{{ old('deskripsi') }}</textarea>
            </div>

            {{-- ===============================
               STATUS
            =============================== --}}
            <div class="col-md-4 mb-3">
                <label class="fw-semibold">Status</label>
                <select name="status" class="form-control rounded-pill">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>

        </div>

        {{-- ===============================
           ACTIONS
        =============================== --}}
        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary rounded-pill px-4">
                Simpan
            </button>

            <a href="{{ route('admin.paket-umrah.index') }}"
               class="btn btn-light rounded-pill px-4">
                Kembali
            </a>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
    const titleInput = document.getElementById('title');
    const seoInput   = document.getElementById('seo_title');
    let seoTouched = false;

    seoInput.addEventListener('input', () => {
        seoTouched = true;
    });

    titleInput.addEventListener('input', () => {
        if (!seoTouched) {
            seoInput.value = titleInput.value;
        }
    });

    function previewImg(e) {
        const img = document.getElementById('preview');
        img.src = URL.createObjectURL(e.target.files[0]);
        img.style.display = 'block';
    }
</script>
@endpush
