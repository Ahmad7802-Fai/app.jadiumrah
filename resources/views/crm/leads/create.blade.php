@extends('layouts.admin')

@section('title', 'Tambah Lead')

@section('content')

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Tambah Lead</h1>
        <div class="page-subtitle">
            Tambahkan data lead baru ke sistem CRM
        </div>
    </div>

    <div class="page-header__actions">
        <a href="{{ route('crm.leads.index') }}"
           class="btn btn-light btn-sm">
            ← Kembali
        </a>
    </div>
</div>

{{-- ======================================================
| FORM CARD
====================================================== --}}
<div class="card card-hover">
    <div class="card-body">

        <form action="{{ route('crm.leads.store') }}"
              method="POST"
              class="form">
            @csrf

            {{-- ================= LEAD FORM ================= --}}
            <x-leads.form :sources="$sources" />

            {{-- ================= ACTIONS ================= --}}
            <div class="form-actions">
                <a href="{{ route('crm.leads.index') }}"
                   class="btn btn-light">
                    Batal
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    Simpan Lead
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
