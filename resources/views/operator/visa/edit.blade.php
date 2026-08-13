@extends('layouts.admin')

@section('title', 'Edit Data Visa')

@section('content')
<div class="container-fluid">

    {{-- ===============================
       HEADER
    ================================ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Edit Data Visa</h4>
            <small class="text-muted">
                Perbarui status dan nomor visa jamaah
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

            <form action="{{ route('operator.visa.update', $visa->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- JAMAAH (READ ONLY) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jamaah</label>
                        <input type="text"
                               class="form-control bg-light"
                               value="{{ $visa->jamaah->nama_lengkap }}"
                               disabled>
                    </div>

                    {{-- KEBERANGKATAN --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Keberangkatan <span class="text-danger">*</span>
                        </label>

                        <select name="keberangkatan_id"
                                class="form-select @error('keberangkatan_id') is-invalid @enderror"
                                required>
                            @foreach($keberangkatan as $k)
                                <option value="{{ $k->id }}"
                                    @selected($visa->keberangkatan_id == $k->id)>
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
                            <option value="Proses"   @selected($visa->status=='Proses')>Proses</option>
                            <option value="Approved" @selected($visa->status=='Approved')>Approved</option>
                            <option value="Rejected" @selected($visa->status=='Rejected')>Rejected</option>
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
                               placeholder="Isi nomor visa jika tersedia"
                               value="{{ old('nomor_visa', $visa->nomor_visa) }}">

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
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
