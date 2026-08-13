<div class="card card-soft">

    {{-- ===============================
        CARD HEADER
    ================================ --}}
    <div class="card-header card-header-soft">
        <h3 class="card-title">
            {{ isset($team) ? 'Edit Anggota' : 'Tambah Anggota' }}
        </h3>
    </div>

    {{-- ===============================
        CARD BODY
    ================================ --}}
    <div class="card-body">

        <div class="row g-4">

            {{-- ===============================
                FOTO
            ================================ --}}
            <div class="col-md-4 text-center">

                <label class="form-label fw-semibold">Foto</label>

                <div class="avatar-upload mx-auto">
                    <img id="preview-image"
                         class="avatar-xl"
                         src="{{ isset($team->photo)
                                ? asset('storage/'.$team->photo)
                                : 'https://ui-avatars.com/api/?size=280&background=E5E7EB&color=6B7280&name=Foto' }}"
                         alt="Preview Foto">
                </div>

                <input type="file"
                       name="photo"
                       id="photo"
                       accept="image/*"
                       class="form-input mt-3"
                       onchange="previewPhoto(event)">

                @error('photo')
                    <div class="form-error">{{ $message }}</div>
                @enderror

            </div>

            {{-- ===============================
                FORM INPUT
            ================================ --}}
            <div class="col-md-8">

                {{-- Nama --}}
                <div class="form-group">
                    <label class="form-label">Nama</label>
                    <input type="text"
                           name="nama"
                           class="form-input"
                           value="{{ old('nama', $team->nama ?? '') }}"
                           placeholder="Masukkan nama anggota">

                    @error('nama')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Jabatan --}}
                <div class="form-group">
                    <label class="form-label">Jabatan</label>
                    <input type="text"
                           name="jabatan"
                           class="form-input"
                           value="{{ old('jabatan', $team->jabatan ?? '') }}"
                           placeholder="Contoh: Direktur, Marketing, dll">

                    @error('jabatan')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

            </div>

        </div>

        {{-- ===============================
            ACTIONS
        ================================ --}}
        <div class="form-actions">

            <a href="{{ route('admin.team.index') }}"
               class="btn btn-light btn-pill">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>

            <button type="submit"
                    class="btn btn-primary btn-pill">
                <i class="fas fa-save"></i>
                {{ isset($team) ? 'Update' : 'Simpan' }}
            </button>

        </div>

    </div>

</div>
@push('scripts')
<script>
function previewPhoto(event) {
    const image = document.getElementById('preview-image');
    if (event.target.files && event.target.files[0]) {
        image.src = URL.createObjectURL(event.target.files[0]);
    }
}
</script>
@endpush
