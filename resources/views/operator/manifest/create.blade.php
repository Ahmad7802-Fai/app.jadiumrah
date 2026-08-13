@extends('layouts.admin')

@section('title','Tambah Manifest Jamaah')

@section('content')
<div class="page-manifest-create">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Manifest Jamaah</h1>
            <p class="page-subtitle">
                Atur kamar jamaah berdasarkan keberangkatan
            </p>
        </div>

        {{-- DESKTOP ACTION --}}
        <div class="page-actions d-none d-md-flex">
            <a href="{{ route('operator.manifest.index') }}"
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

            <form action="{{ route('operator.manifest.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    {{-- ===============================
                       KEBERANGKATAN
                    ================================ --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Keberangkatan <span class="text-danger">*</span>
                        </label>

                        <select name="keberangkatan_id"
                                id="keberangkatanSelect"
                                class="form-select @error('keberangkatan_id') is-invalid @enderror"
                                required>
                            <option value="">-- Pilih Keberangkatan --</option>
                            @foreach($keberangkatanList as $k)
                                <option value="{{ $k->id }}"
                                    @selected(request('keberangkatan_id')==$k->id)>
                                    {{ $k->kode_keberangkatan }}
                                    ({{ \Carbon\Carbon::parse($k->tanggal_berangkat)->format('d M Y') }})
                                </option>
                            @endforeach
                        </select>

                        @error('keberangkatan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if(!request('keberangkatan_id'))
                            <small class="text-muted">
                                Pilih keberangkatan untuk memunculkan jamaah
                            </small>
                        @endif
                    </div>

                    {{-- ===============================
                       JAMAAH
                    ================================ --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Jamaah <span class="text-danger">*</span>
                        </label>

                        <select name="jamaah_id"
                                id="jamaahSelect"
                                class="form-select"
                                {{ request('keberangkatan_id') ? '' : 'disabled' }}
                                required>
                            <option value="">-- Pilih Jamaah --</option>
                            @foreach($jamaahList as $j)
                                <option value="{{ $j->id }}">
                                    {{ $j->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>

                        @if(!request('keberangkatan_id'))
                            <small class="text-muted">
                                Pilih keberangkatan terlebih dahulu
                            </small>
                        @endif
                    </div>

                    {{-- ===============================
                       TIPE KAMAR
                    ================================ --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Tipe Kamar <span class="text-danger">*</span>
                        </label>

                        <select name="tipe_kamar"
                                id="tipeKamarSelect"
                                class="form-select"
                                required>
                            <option value="Quad">Quad</option>
                            <option value="Triple">Triple</option>
                            <option value="Double">Double</option>
                        </select>
                    </div>

                    {{-- ===============================
                       NOMOR KAMAR (AUTO)
                    ================================ --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Nomor Kamar
                        </label>

                        <input type="text"
                               id="nomorKamar"
                               name="nomor_kamar"
                               class="form-control bg-light"
                               readonly>

                        <small class="text-muted">
                            Nomor kamar otomatis berdasarkan tipe kamar
                        </small>
                    </div>

                </div>

                <hr class="my-4">

                {{-- ===============================
                   ACTION BUTTONS
                ================================ --}}
                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('operator.manifest.index') }}"
                       class="btn btn-outline-secondary">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Simpan Manifest
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const tipeSelect = document.getElementById('tipeKamarSelect')
    const nomorField = document.getElementById('nomorKamar')
    const keberangkatanSelect = document.getElementById('keberangkatanSelect')

    function generateRoom() {
        if (!tipeSelect || !nomorField) return

        const prefix =
            tipeSelect.value === 'Quad'   ? 'Q' :
            tipeSelect.value === 'Triple' ? 'T' : 'D'

        const nomor = Math.floor(101 + Math.random() * 30)
        nomorField.value = prefix + nomor
    }

    if (tipeSelect) {
        tipeSelect.addEventListener('change', generateRoom)
        generateRoom()
    }

    if (keberangkatanSelect) {
        keberangkatanSelect.addEventListener('change', function () {
            if (this.value) {
                window.location = '?keberangkatan_id=' + this.value
            }
        })
    }

})
</script>
@endpush
