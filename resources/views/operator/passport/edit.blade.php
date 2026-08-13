@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <!-- ================================
         MOBILE: Back Button
    ================================= -->
    <div class="mb-3 d-md-none">
        <a href="{{ route('operator.passport.index') }}"
           class="btn-ju-outline w-100 rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- ================================
         DESKTOP HEADER
    ================================= -->
    <div class="d-none d-md-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Edit Data Passport Jamaah</h4>

        <a href="{{ route('operator.passport.index') }}"
           class="btn-ju-outline rounded-pill px-4">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>


    <!-- ================================
         CARD FORM
    ================================= -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-3 p-md-4">

            <form action="{{ route('operator.passport.update', $passport->id) }}" method="POST">
                @csrf
                @method('PUT')


                <!-- JAMAAH -->
                <label class="form-label fw-semibold">Nama Jamaah</label>
                <input type="text"
                       class="form-control rounded-pill mb-3 bg-light"
                       value="{{ $passport->jamaah->nama_lengkap }}"
                       disabled>


                <!-- NOMOR PASPOR -->
                <label class="form-label fw-semibold">Nomor Paspor *</label>
                <input type="text" name="nomor_paspor"
                       class="form-control rounded-pill mb-3"
                       value="{{ $passport->nomor_paspor }}" required>


                <!-- TANGGAL TERBIT & HABIS -->
                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Tanggal Terbit *</label>
                        <input type="date" name="tanggal_terbit_paspor"
                               class="form-control rounded-pill mb-3"
                               value="{{ $passport->tanggal_terbit_paspor }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Tanggal Habis *</label>
                        <input type="date" name="tanggal_habis_paspor"
                               class="form-control rounded-pill mb-3"
                               value="{{ $passport->tanggal_habis_paspor }}" required>
                    </div>
                </div>


                <!-- TEMPAT TERBIT & NEGARA PENERBIT -->
                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Tempat Terbit *</label>
                        <input type="text" name="tempat_terbit_paspor"
                               class="form-control rounded-pill mb-3"
                               value="{{ $passport->tempat_terbit_paspor }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Negara Penerbit *</label>
                        <input type="text" name="negara_penerbit"
                               class="form-control rounded-pill mb-3"
                               value="{{ $passport->negara_penerbit }}" required>
                    </div>
                </div>


                <!-- ALAMAT -->
                <label class="form-label fw-semibold">Alamat Lengkap *</label>
                <textarea name="alamat_lengkap"
                          rows="2"
                          class="form-control rounded-3 mb-3"
                          required>{{ $passport->alamat_lengkap }}</textarea>


                <!-- KODE POS -->
                <label class="form-label fw-semibold">Kode POS *</label>
                <input type="text" name="kode_pos"
                       class="form-control rounded-pill mb-3"
                       value="{{ $passport->kode_pos }}" required>


                <!-- TUJUAN IMIGRASI -->
                <label class="form-label fw-semibold">Tujuan Imigrasi *</label>
                <input type="text" name="tujuan_imigrasi"
                       class="form-control rounded-pill mb-3"
                       value="{{ $passport->tujuan_imigrasi }}" required>


                <!-- REKOMENDASI -->
                <label class="form-label fw-semibold">Rekomendasi Passport *</label>
                <select name="rekomendasi_paspor"
                        class="form-control rounded-pill mb-3">
                    <option value="Masih Berlaku" 
                        {{ $passport->rekomendasi_paspor == 'Masih Berlaku' ? 'selected' : '' }}>
                        Masih Berlaku
                    </option>
                    <option value="Segera Perpanjang"
                        {{ $passport->rekomendasi_paspor == 'Segera Perpanjang' ? 'selected' : '' }}>
                        Segera Perpanjang
                    </option>
                    <option value="Perlu Perpanjang"
                        {{ $passport->rekomendasi_paspor == 'Perlu Perpanjang' ? 'selected' : '' }}>
                        Perlu Perpanjang
                    </option>
                </select>



                <!-- ================================
                     BUTTONS — Mobile First
                ================================= -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-4">

                    <button class="btn-ju rounded-pill px-4 w-100 w-md-auto">
                        <i class="fas fa-save me-1"></i> Update Passport
                    </button>

                    <a href="{{ route('operator.passport.index') }}"
                       class="btn-ju-outline rounded-pill px-4 w-100 w-md-auto">
                        Batal
                    </a>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection
