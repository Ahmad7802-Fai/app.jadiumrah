<div class="card shadow-sm border-0 rounded-4">

    <div class="card-header bg-white border-0 pb-0">
        <h5 class="card-title mb-0 fw-semibold">
            {{ isset($partner) ? 'Edit Partner' : 'Tambah Partner' }}
        </h5>
    </div>

    <div class="card-body">

        <div class="row">

            <!-- Logo -->
            <div class="col-md-4 text-center mb-4">

                <label class="fw-semibold mb-2 d-block">Logo</label>

                <img id="preview-logo"
                     src="{{ isset($partner->logo)
                            ? asset('storage/'.$partner->logo)
                            : 'https://via.placeholder.com/140?text=Logo' }}"
                     class="rounded shadow-sm"
                     style="width:140px;height:140px;object-fit:cover;">

                <input type="file" name="logo" id="logo"
                       class="form-control mt-3" accept="image/*"
                       onchange="previewLogo(event)">

                @error('logo')
                  <small class="text-danger">{{ $message }}</small>
                @enderror

            </div>

            <div class="col-md-8">

                <!-- Nama -->
                <div class="mb-3">
                    <label class="fw-semibold mb-1">Nama Partner</label>
                    <input type="text" name="nama" class="form-control rounded"
                           value="{{ old('nama', $partner->nama ?? '') }}"
                           placeholder="Masukkan nama partner">

                    @error('nama')
                      <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Website -->
                <div class="mb-3">
                    <label class="fw-semibold mb-1">Website</label>
                    <input type="text" name="website" class="form-control rounded"
                           value="{{ old('website', $partner->website ?? '') }}"
                           placeholder="https://example.com">

                    @error('website')
                      <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

            </div>
        </div>

        <hr>

        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('admin.partner.index') }}"
               class="btn btn-light border rounded-pill px-4">
               <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>

            <button class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-save me-1"></i>
                {{ isset($partner) ? 'Update' : 'Simpan' }}
            </button>

        </div>

    </div>
</div>

@push('scripts')
<script>
function previewLogo(event) {
    let img = document.getElementById('preview-logo');
    img.src = URL.createObjectURL(event.target.files[0]);
}
</script>
@endpush
