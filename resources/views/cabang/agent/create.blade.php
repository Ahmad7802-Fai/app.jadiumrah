@extends('layouts.cabang')

@section('title', 'Tambah Agent')

@section('content')

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="mb-16">
    <a href="{{ route('cabang.agent.index') }}"
       class="c-btn outline sm mb-6">
        ← Kembali
    </a>

    <h1 class="page-title mb-2">
        Tambah Agent
    </h1>

    <p class="page-subtitle">
        Tambahkan sales / agent baru ke cabang
    </p>
</div>

{{-- ======================================================
| ERROR MESSAGE
====================================================== --}}
@if(session('error'))
    <div class="c-card danger-soft mb-16 fs-12">
        {{ session('error') }}
    </div>
@endif

{{-- ======================================================
| FORM
====================================================== --}}
<form method="POST"
      action="{{ route('cabang.agent.store') }}">
    @csrf

    <div class="c-card">

        {{-- ===============================
           FORM BODY
        =============================== --}}
        <div class="c-form">

            @include('cabang.agent._form')

        </div>

        {{-- ===============================
           ACTION
        =============================== --}}
        <div class="d-flex justify-between align-center mt-12">

            <a href="{{ route('cabang.agent.index') }}"
               class="c-btn outline">
                Batal
            </a>

            <button class="c-btn primary">
                💾 Simpan Agent
            </button>

        </div>

    </div>
</form>

@endsection
