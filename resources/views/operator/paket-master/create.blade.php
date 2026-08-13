@extends('layouts.admin')

@section('title','Tambah Master Paket')

@section('content')
<div class="page-master-paket">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Master Paket</h1>
            <p class="page-subtitle">
                Buat paket umroh baru beserta harga & hotel
            </p>
        </div>

        {{-- DESKTOP ACTION --}}
        <div class="page-actions d-none d-md-flex">
            <a href="{{ route('operator.master-paket.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- ===============================
       FORM CARD
    ================================ --}}
    <form action="{{ route('operator.master-paket.store') }}"
          method="POST"
          class="card card-hover border-0">
        @csrf

        <div class="card-body">

            <div class="row g-3">

                {{-- NAMA PAKET --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Nama Paket <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="nama_paket"
                           value="{{ old('nama_paket') }}"
                           class="form-control @error('nama_paket') is-invalid @enderror"
                           placeholder="Contoh: Umrah Ekonomis November 2026"
                           required>
                    @error('nama_paket')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- PESAWAT --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Pesawat</label>
                    <input type="text"
                           name="pesawat"
                           value="{{ old('pesawat') }}"
                           class="form-control"
                           placeholder="Maskapai (opsional)">
                </div>

                {{-- HOTEL MEKKAH --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hotel Mekkah</label>
                    <input type="text"
                           name="hotel_mekkah"
                           value="{{ old('hotel_mekkah') }}"
                           class="form-control"
                           placeholder="Nama hotel di Mekkah">
                </div>

                {{-- HOTEL MADINAH --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hotel Madinah</label>
                    <input type="text"
                           name="hotel_madinah"
                           value="{{ old('hotel_madinah') }}"
                           class="form-control"
                           placeholder="Nama hotel di Madinah">
                </div>

                {{-- HARGA QUAD --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Harga Quad (Rp) <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="harga_quad"
                           value="{{ old('harga_quad') }}"
                           class="form-control input-currency @error('harga_quad') is-invalid @enderror"
                           placeholder="0"
                           required>
                    @error('harga_quad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- HARGA TRIPLE --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Harga Triple (Rp) <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="harga_triple"
                           value="{{ old('harga_triple') }}"
                           class="form-control input-currency @error('harga_triple') is-invalid @enderror"
                           placeholder="0"
                           required>
                </div>

                {{-- HARGA DOUBLE --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        Harga Double (Rp) <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="harga_double"
                           value="{{ old('harga_double') }}"
                           class="form-control input-currency @error('harga_double') is-invalid @enderror"
                           placeholder="0"
                           required>
                </div>

                {{-- DISKON --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Diskon Default (Rp)</label>
                    <input type="text"
                           name="diskon_default"
                           value="{{ old('diskon_default', 0) }}"
                           class="form-control input-currency"
                           placeholder="0">
                </div>

                {{-- STATUS --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" @selected(old('is_active','1')=='1')>Aktif</option>
                        <option value="0" @selected(old('is_active')=='0')>Tidak Aktif</option>
                    </select>
                </div>

                {{-- CATATAN --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Catatan (Opsional)</label>
                    <textarea name="catatan"
                              rows="3"
                              class="form-control"
                              placeholder="Catatan internal paket...">{{ old('catatan') }}</textarea>
                </div>

            </div>

        </div>

        {{-- ===============================
           CARD FOOTER ACTIONS
        ================================ --}}
        <div class="card-footer bg-white d-flex flex-wrap gap-2">

            <button type="submit"
                    id="btn-submit"
                    class="btn btn-primary">
                <i class="fas fa-save"></i>
                Simpan Paket
            </button>

            <a href="{{ route('operator.master-paket.index') }}"
               class="btn btn-outline-secondary">
                Batal
            </a>

        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const inputs = document.querySelectorAll('.input-currency');

    const format = (val) => {
        val = val.replace(/[^0-9]/g, '');
        return val ? val.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
    };

    inputs.forEach(input => {
        input.value = format(input.value);
        input.addEventListener('input', e => {
            e.target.value = format(e.target.value);
        });
    });

    document.querySelector('form').addEventListener('submit', () => {
        inputs.forEach(i => {
            i.value = i.value.replace(/[^0-9]/g, '') || '0';
        });

        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
    });

});
</script>
@endpush
