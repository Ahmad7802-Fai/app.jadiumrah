@extends('layouts.admin')

@section('title','Edit Biaya Operasional')

@section('content')
<div class="page-container" style="max-width:auto">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">
        <div class="page-header-left">

            {{-- MOBILE BACK --}}
            <a href="{{ route('keuangan.operasional.index') }}"
               class="btn btn-outline-secondary btn-icon d-md-none">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div>
                <h1 class="page-title">Edit Biaya Operasional</h1>
                <p class="page-subtitle">
                    Perbarui informasi pengeluaran operasional
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
    | ALERT
    ===================================================== --}}
    @include('components.alert')


    {{-- =====================================================
    | FORM CARD
    ===================================================== --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Form Edit Biaya Operasional
            </h3>
        </div>

        <div class="card-body">

            <form action="{{ route('keuangan.operasional.update', $item->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- KATEGORI --}}
                    <div class="col-md-4">
                        <label class="form-label">
                            Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="kategori"
                               value="{{ old('kategori', $item->kategori) }}"
                               class="form-control @error('kategori') is-invalid @enderror"
                               required>

                        @error('kategori')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- TANGGAL --}}
                    <div class="col-md-4">
                        <label class="form-label">
                            Tanggal <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="tanggal"
                               value="{{ old('tanggal', $item->tanggal) }}"
                               class="form-control @error('tanggal') is-invalid @enderror"
                               required>

                        @error('tanggal')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- JUMLAH --}}
                    <div class="col-md-4">
                        <label class="form-label">
                            Jumlah (Rp) <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               name="jumlah"
                               value="{{ old('jumlah', $item->jumlah) }}"
                               class="form-control @error('jumlah') is-invalid @enderror"
                               min="0"
                               required>

                        @error('jumlah')
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
                                  placeholder="Keterangan tambahan (opsional)">{{ old('deskripsi', $item->deskripsi) }}</textarea>

                        @error('deskripsi')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- BUKTI --}}
                    <div class="col-12">
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

                        {{-- PREVIEW EXISTING --}}
                        @if($item->bukti)
                            <div class="mt-3">
                                <div class="text-muted small mb-1">
                                    Bukti saat ini:
                                </div>

                                @if(Str::endsWith(strtolower($item->bukti), ['.jpg','.jpeg','.png']))
                                    <img src="{{ asset('storage/'.$item->bukti) }}"
                                         alt="Bukti"
                                         class="img-thumbnail"
                                         style="max-width:160px">
                                @else
                                    <a href="{{ asset('storage/'.$item->bukti) }}"
                                       target="_blank"
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-file-pdf"></i>
                                        Lihat PDF
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                </div>

                {{-- ACTIONS --}}
                <div class="card-footer d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Pengeluaran
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
