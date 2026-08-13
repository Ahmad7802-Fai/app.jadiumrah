@extends('layouts.admin')

@section('title', 'Buat Akun Jamaah')

@section('content')
<div class="page-jamaah-user-create">

    {{-- =====================================================
       PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Buat Akun Jamaah</h1>
            <p class="text-muted text-sm">
                Akun login terpisah dari data jamaah
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('operator.jamaah-user.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- =====================================================
       ERROR MESSAGE
    ====================================================== --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =====================================================
       FORM CARD
    ====================================================== --}}
    <form method="POST" action="{{ route('operator.jamaah-user.store') }}">
        @csrf

        <div class="card card-hover">

            <div class="card-body">

                {{-- ===============================
                   PILIH JAMAAH
                =============================== --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Pilih Jamaah <span class="text-danger">*</span>
                    </label>

                    <select name="jamaah_id"
                            class="form-select js-select-jamaah"
                            required>
                        <option value="">-- Pilih Jamaah --</option>
                        @foreach($jamaahList as $j)
                            <option value="{{ $j->id }}" @selected(old('jamaah_id') == $j->id)>
                                {{ $j->nama_lengkap }}
                                | {{ $j->no_hp ?? '-' }}
                                | {{ $j->no_id }}
                            </option>
                        @endforeach
                    </select>

                    <div class="text-muted text-sm mt-1">
                        Cari berdasarkan nama / No HP / No ID
                    </div>
                </div>

                {{-- ===============================
                   EMAIL
                =============================== --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Email (Opsional)
                    </label>

                    <input type="email"
                           name="email"
                           class="form-control"
                           placeholder="email@contoh.com"
                           value="{{ old('email') }}">
                </div>

                {{-- ===============================
                   NO HP
                =============================== --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        No HP (Opsional)
                    </label>

                    <input type="text"
                           name="phone"
                           class="form-control"
                           placeholder="08xxxxxxxxxx"
                           value="{{ old('phone') }}">

                    <div class="text-muted text-sm mt-1">
                        Email atau No HP wajib diisi salah satu
                    </div>
                </div>

            </div>

            {{-- =====================================================
               ACTIONS
            ====================================================== --}}
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('operator.jamaah-user.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary btn-sm">
                        <i class="fas fa-save"></i>
                        Buat Akun
                    </button>

                </div>
            </div>

        </div>
    </form>

</div>
@endsection

{{-- =====================================================
   SELECT2 INIT
====================================================== --}}
@push('scripts')
<script>
$(document).ready(function () {
    $('.js-select-jamaah').select2({
        placeholder: 'Ketik nama / No HP / No ID',
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush
