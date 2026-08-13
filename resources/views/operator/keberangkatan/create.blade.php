@extends('layouts.admin')

@section('title','Tambah Keberangkatan')

@section('content')
<div class="page-keberangkatan">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Keberangkatan</h1>
            <p class="page-subtitle">
                Tambahkan jadwal keberangkatan baru
            </p>
        </div>

        <div class="page-actions d-none d-md-flex">
            <a href="{{ route('operator.keberangkatan.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- ===============================
       FORM
    ================================ --}}
    <div class="card card-hover">
        <div class="card-body">

            <form action="{{ route('operator.keberangkatan.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    {{-- PAKET MASTER --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Paket Master <span class="text-danger">*</span>
                        </label>
                        <select name="id_paket_master"
                                class="form-select @error('id_paket_master') is-invalid @enderror">
                            <option value="">Pilih Paket</option>
                            @foreach ($paket as $p)
                                <option value="{{ $p->id }}"
                                    @selected(old('id_paket_master') == $p->id)>
                                    {{ $p->nama_paket }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_paket_master')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- KODE --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Kode Keberangkatan <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="kode_keberangkatan"
                               value="{{ old('kode_keberangkatan') }}"
                               placeholder="Contoh: JR-DEC-01"
                               class="form-control @error('kode_keberangkatan') is-invalid @enderror">
                        @error('kode_keberangkatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- TANGGAL BERANGKAT --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Tanggal Berangkat <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="tanggal_berangkat"
                               value="{{ old('tanggal_berangkat') }}"
                               class="form-control datepicker @error('tanggal_berangkat') is-invalid @enderror"
                               placeholder="YYYY-MM-DD">
                        @error('tanggal_berangkat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- TANGGAL PULANG --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Tanggal Pulang <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="tanggal_pulang"
                               value="{{ old('tanggal_pulang') }}"
                               class="form-control datepicker @error('tanggal_pulang') is-invalid @enderror"
                               placeholder="YYYY-MM-DD">
                        @error('tanggal_pulang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- KUOTA --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Kuota <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               name="kuota"
                               value="{{ old('kuota', 0) }}"
                               class="form-control @error('kuota') is-invalid @enderror">
                        @error('kuota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- SEAT TERISI --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Seat Terisi
                        </label>
                        <input type="number"
                               name="seat_terisi"
                               value="{{ old('seat_terisi', 0) }}"
                               class="form-control @error('seat_terisi') is-invalid @enderror">
                        @error('seat_terisi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- STATUS --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select name="status" class="form-select">
                            <option value="Aktif"   @selected(old('status')=='Aktif')>Aktif</option>
                            <option value="Selesai" @selected(old('status')=='Selesai')>Selesai</option>
                            <option value="Batal"   @selected(old('status')=='Batal')>Batal</option>
                        </select>
                    </div>

                </div>

                {{-- ACTION --}}
                <div class="form-actions mt-4 d-flex gap-2">
                    <button type="submit"
                            class="btn btn-primary"
                            id="btn-submit">
                        <i class="fas fa-save"></i>
                        Simpan Keberangkatan
                    </button>

                    <a href="{{ route('operator.keberangkatan.index') }}"
                       class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
@push('styles')
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    flatpickr('.datepicker', {
        dateFormat: 'Y-m-d',
        allowInput: true
    })

    const btn = document.getElementById('btn-submit')
    if (btn) {
        btn.addEventListener('click', function () {
            btn.disabled = true
            btn.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Menyimpan...'
            btn.closest('form').submit()
        })
    }

})
</script>
@endpush
