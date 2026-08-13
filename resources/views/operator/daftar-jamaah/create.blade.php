@extends('layouts.admin')

@section('title', 'Tambah Jamaah')

@section('content')
<div class="page-jamaah-create">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Jamaah</h1>
            <p class="text-muted text-sm">
                Lengkapi data jamaah untuk proses keberangkatan
            </p>
        </div>

        {{-- DESKTOP BACK ACTION --}}
        <div class="d-none d-md-block">
            <a href="{{ route('operator.daftar-jamaah.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- ===============================
       FORM CARD
    ================================ --}}
    <form method="POST"
          action="{{ route('operator.daftar-jamaah.store') }}"
          enctype="multipart/form-data">
        @csrf

        <div class="card card-hover">
            <div class="card-body">

                @include('operator.daftar-jamaah._form', [
                    'jamaah' => null
                ])

            </div>

            {{-- ACTION --}}
            <div class="card-footer d-flex justify-content-between align-items-center">

                <a href="{{ route('operator.daftar-jamaah.index') }}"
                   class="btn btn-outline-secondary">
                    Batal
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Jamaah
                </button>

            </div>
        </div>

    </form>

</div>

{{-- ===============================
   MOBILE — BOTTOM ACTION
=============================== --}}
<div class="d-md-none mt-3">
    <div class="card">
        <div class="card-body d-flex gap-2">
            <a href="{{ route('operator.daftar-jamaah.index') }}"
               class="btn btn-outline-secondary w-50">
                Batal
            </a>

            <button type="submit"
                    form="jamaah-form"
                    class="btn btn-primary w-50">
                Simpan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/jamaah-form.js') }}"></script>
@endpush
