@extends('layouts.admin')

@section('title', 'Tambah Passport Jamaah')

@section('content')
<div class="container-fluid">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">

        <div>
            <h1 class="page-title">Tambah Passport Jamaah</h1>
            <p class="text-muted text-sm">
                Input data passport jamaah untuk keperluan administrasi & SRP
            </p>
        </div>

        {{-- DESKTOP ACTION --}}
        <div class="d-none d-lg-flex gap-2">
            <a href="{{ route('operator.passport.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>

    </div>

    {{-- MOBILE BACK --}}
    <div class="d-lg-none mb-3">
        <a href="{{ route('operator.passport.index') }}"
           class="btn btn-outline-secondary w-100">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- ===============================
       FORM CARD
    ================================ --}}
    <div class="card card-hover">
        <div class="card-body">

            <form action="{{ route('operator.passport.store') }}" method="POST">
                @csrf

                {{-- ===============================
                   PILIH JAMAAH
                ================================ --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Pilih Jamaah <span class="text-danger">*</span>
                    </label>

                    <select name="jamaah_id"
                            class="form-control js-jamaah-search @error('jamaah_id') is-invalid @enderror"
                            required>
                        <option value="">— Pilih Jamaah —</option>
                        @foreach($jamaah as $j)
                            <option value="{{ $j->id }}">
                                {{ $j->nama_lengkap }} ({{ $j->no_id }})
                            </option>
                        @endforeach
                    </select>

                    @error('jamaah_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ===============================
                   NOMOR PASPOR
                ================================ --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Nomor Paspor <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="nomor_paspor"
                           class="form-control @error('nomor_paspor') is-invalid @enderror"
                           required>

                    @error('nomor_paspor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ===============================
                   TANGGAL PASPOR
                ================================ --}}
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">
                            Tanggal Terbit <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="tanggal_terbit_paspor"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">
                            Tanggal Habis <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="tanggal_habis_paspor"
                               class="form-control"
                               required>
                    </div>
                </div>

                {{-- ===============================
                   TEMPAT & NEGARA
                ================================ --}}
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">
                            Tempat Terbit <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="tempat_terbit_paspor"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">
                            Negara Penerbit <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="negara_penerbit"
                               class="form-control"
                               required>
                    </div>
                </div>

                {{-- ===============================
                   ALAMAT
                ================================ --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Alamat Lengkap <span class="text-danger">*</span>
                    </label>
                    <textarea name="alamat_lengkap"
                              class="form-control"
                              rows="2"
                              required></textarea>
                </div>

                {{-- ===============================
                   WILAYAH
                ================================ --}}
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">
                            Kecamatan <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="kecamatan"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">
                            Kota <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="kota"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">
                            Provinsi <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="provinsi"
                               class="form-control"
                               required>
                    </div>
                </div>

                {{-- ===============================
                   KODE POS
                ================================ --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Kode POS <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="kode_pos"
                           class="form-control"
                           required>
                </div>

                {{-- ===============================
                   TUJUAN IMIGRASI
                ================================ --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Tujuan Imigrasi <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="tujuan_imigrasi"
                           class="form-control"
                           required>
                </div>

                {{-- ===============================
                   ACTION BUTTON
                ================================ --}}
                <div class="d-grid gap-2 d-lg-flex justify-content-lg-start">

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Passport
                    </button>

                    <a href="{{ route('operator.passport.index') }}"
                       class="btn btn-outline-secondary">
                        Batal
                    </a>

                </div>

            </form>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('.js-jamaah-search').select2({
        placeholder: 'Ketik nama / No ID jamaah',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush
