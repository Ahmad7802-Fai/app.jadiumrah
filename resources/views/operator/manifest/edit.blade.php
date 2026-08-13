@extends('layouts.admin')

@section('title','Edit Manifest Jamaah')

@section('content')
<div class="page-manifest-edit">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Manifest Jamaah</h1>
            <p class="page-subtitle">
                Perbarui tipe kamar dan nomor kamar jamaah
            </p>
        </div>

        {{-- DESKTOP ACTION --}}
        <div class="page-actions d-none d-md-flex">
            <a href="{{ route('operator.manifest.index', ['keberangkatan_id'=>$manifest->keberangkatan_id]) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- ===============================
       FORM CARD
    ================================ --}}
    <div class="card card-hover">
        <div class="card-body">

            <form method="POST"
                  action="{{ route('operator.manifest.update', $manifest->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- ===============================
                       JAMAAH
                    ================================ --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Nama Jamaah
                        </label>

                        <input type="text"
                               class="form-control bg-light"
                               value="{{ $manifest->jamaah->nama_lengkap }}"
                               readonly>
                    </div>

                    {{-- ===============================
                       KEBERANGKATAN
                    ================================ --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Keberangkatan
                        </label>

                        <input type="text"
                               class="form-control bg-light"
                               value="{{ $manifest->keberangkatan->kode_keberangkatan }}
                               ({{ \Carbon\Carbon::parse($manifest->keberangkatan->tanggal_berangkat)->format('d M Y') }})"
                               readonly>
                    </div>

                    {{-- ===============================
                       TIPE KAMAR
                    ================================ --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Tipe Kamar <span class="text-danger">*</span>
                        </label>

                        <select name="tipe_kamar"
                                class="form-select @error('tipe_kamar') is-invalid @enderror"
                                required>
                            <option value="Quad"   @selected($manifest->tipe_kamar==='Quad')>Quad</option>
                            <option value="Triple" @selected($manifest->tipe_kamar==='Triple')>Triple</option>
                            <option value="Double" @selected($manifest->tipe_kamar==='Double')>Double</option>
                        </select>

                        @error('tipe_kamar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ===============================
                       NOMOR KAMAR
                    ================================ --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Nomor Kamar <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="nomor_kamar"
                               class="form-control @error('nomor_kamar') is-invalid @enderror"
                               value="{{ old('nomor_kamar', $manifest->nomor_kamar) }}"
                               placeholder="Contoh: Q101">

                        <small class="text-muted">
                            Nomor kamar harus unik pada keberangkatan ini
                        </small>

                        @error('nomor_kamar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <hr class="my-4">

                {{-- ===============================
                   ACTION BUTTONS
                ================================ --}}
                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('operator.manifest.index', ['keberangkatan_id'=>$manifest->keberangkatan_id]) }}"
                       class="btn btn-outline-secondary">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>
@endsection
