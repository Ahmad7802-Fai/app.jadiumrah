@extends('layouts.admin')

@section('title','Tambah Biaya Operasional')

@section('content')
<div class="page-container" style="max-width:1200px">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">
        <div class="page-header-left">
            <a href="{{ route('keuangan.operasional.index') }}"
               class="btn btn-outline-secondary btn-icon d-md-none">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div>
                <h1 class="page-title">Tambah Biaya Operasional</h1>
                <p class="page-subtitle">
                    Catat pengeluaran kantor atau keberangkatan
                </p>
            </div>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.operasional.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>


    {{-- =====================================================
    | FORM CARD
    ===================================================== --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Form Biaya Operasional
            </h3>
        </div>

        <div class="card-body">

            <form action="{{ route('keuangan.operasional.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="row g-3">

                    {{-- KATEGORI --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="kategori"
                               value="{{ old('kategori') }}"
                               class="form-control @error('kategori') is-invalid @enderror"
                               placeholder="Contoh: Konsumsi, ATK, Transportasi"
                               required>

                        @error('kategori')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- TANGGAL --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Tanggal <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="tanggal"
                               value="{{ old('tanggal', date('Y-m-d')) }}"
                               class="form-control @error('tanggal') is-invalid @enderror"
                               required>

                        @error('tanggal')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- JUMLAH --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Jumlah (Rp) <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               name="jumlah"
                               value="{{ old('jumlah') }}"
                               class="form-control @error('jumlah') is-invalid @enderror"
                               placeholder="0"
                               min="0"
                               required>

                        @error('jumlah')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- BUKTI --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Bukti Pembayaran
                        </label>
                        <input type="file"
                               name="bukti"
                               accept=".jpg,.jpeg,.png,.pdf"
                               class="form-control @error('bukti') is-invalid @enderror">

                        <div class="form-text">
                            JPG, PNG, atau PDF · Maks 4 MB
                        </div>

                        @error('bukti')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="col-12">
                        <label class="form-label">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi"
                                  rows="3"
                                  class="form-control @error('deskripsi') is-invalid @enderror"
                                  placeholder="Keterangan tambahan (opsional)">{{ old('deskripsi') }}</textarea>

                        @error('deskripsi')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                {{-- ACTIONS --}}
                <div class="card-footer d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Pengeluaran
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
