@extends('layouts.admin')

@section('title', 'Tambah Data Visa')

@section('content')
<div class="container-fluid">

    {{-- ===============================
       HEADER
    ================================ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Tambah Data Visa</h4>
            <small class="text-muted">
                Input pengurusan visa jamaah
            </small>
        </div>

        <a href="{{ route('operator.visa.index') }}"
           class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Kembali
        </a>
    </div>


    {{-- ===============================
       FORM CARD
    ================================ --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('operator.visa.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    {{-- JAMAAH --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Jamaah <span class="text-danger">*</span>
                        </label>

                        <select name="jamaah_id"
                                class="form-select @error('jamaah_id') is-invalid @enderror"
                                required>
                            <option value="">— Pilih Jamaah —</option>
                            @foreach($jamaah as $j)
                                <option value="{{ $j->id }}"
                                    @selected(old('jamaah_id') == $j->id)>
                                    {{ $j->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>

                        @error('jamaah_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- KEBERANGKATAN --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Keberangkatan <span class="text-danger">*</span>
                        </label>

                        <select name="keberangkatan_id"
                                class="form-select @error('keberangkatan_id') is-invalid @enderror"
                                required>
                            <option value="">— Pilih Keberangkatan —</option>
                            @foreach($keberangkatan as $k)
                                <option value="{{ $k->id }}"
                                    @selected(old('keberangkatan_id') == $k->id)>
                                    {{ $k->kode_keberangkatan }}
                                    ({{ \Carbon\Carbon::parse($k->tanggal_berangkat)->format('d M Y') }})
                                </option>
                            @endforeach
                        </select>

                        @error('keberangkatan_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- STATUS VISA --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Status Visa <span class="text-danger">*</span>
                        </label>

                        <select name="status"
                                class="form-select @error('status') is-invalid @enderror"
                                required>
                            <option value="Proses"   @selected(old('status')=='Proses')>Proses</option>
                            <option value="Approved" @selected(old('status')=='Approved')>Approved</option>
                            <option value="Rejected" @selected(old('status')=='Rejected')>Rejected</option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- NOMOR VISA --}}
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            Nomor Visa
                            <span class="text-muted">(Opsional)</span>
                        </label>

                        <input type="text"
                               name="nomor_visa"
                               class="form-control @error('nomor_visa') is-invalid @enderror"
                               placeholder="Isi jika sudah tersedia"
                               value="{{ old('nomor_visa') }}">

                        @error('nomor_visa')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                {{-- ACTION --}}
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('operator.visa.index') }}"
                       class="btn btn-outline-secondary">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
