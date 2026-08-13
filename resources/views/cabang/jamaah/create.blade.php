@extends('layouts.cabang')

@section('title', 'Tambah Jamaah')

@section('breadcrumb')
<nav class="cabang-breadcrumb">
    <a href="{{ route('cabang.dashboard') }}" class="crumb-link">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <span class="crumb-sep">/</span>
    <a href="{{ route('cabang.jamaah.index') }}" class="crumb-link">
        Jamaah
    </a>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Tambah</span>
</nav>
@endsection

@section('content')
<div class="cabang-jamaah-create">

    {{-- ===============================
       PAGE HEADER
    =============================== --}}
    <div class="page-header mb-16">
        <div>
            <h1 class="page-title">Tambah Jamaah</h1>
            <p class="page-subtitle">
                Lengkapi data jamaah baru cabang
            </p>
        </div>
    </div>

    {{-- ===============================
       FORM CARD
    =============================== --}}
    <div class="c-card">

        <form method="POST"
              action="{{ route('cabang.jamaah.store') }}"
              enctype="multipart/form-data"
              class="c-form">
            @csrf

            {{-- FORM PARTIAL --}}
            @include('cabang.jamaah._form')

            {{-- ===============================
               FORM ACTION
            =============================== --}}
            {{-- <div class="d-flex justify-between align-center mt-16">

                <a href="{{ route('cabang.jamaah.index') }}"
                   class="c-btn outline">
                    Batal
                </a>

                <button type="submit"
                        class="c-btn primary lg">
                    <i class="fas fa-save"></i>
                    Simpan Jamaah
                </button>

            </div> --}}

        </form>

    </div>

</div>
@endsection
