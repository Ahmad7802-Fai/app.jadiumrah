@extends('layouts.admin')

@section('title', 'Edit Lead')

@section('content')

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Edit Lead</h1>
        <div class="page-subtitle">
            Perbarui informasi lead yang sudah ada
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

        <form method="POST"
              action="{{ route('crm.leads.update', $lead) }}"
              class="form">
            @csrf
            @method('PUT')

            {{-- ================= LEAD FORM ================= --}}
            <x-leads.form :lead="$lead" :sources="$sources" />

            {{-- ================= ACTIONS ================= --}}
            <div class="form-actions">
                <a href="{{ route('crm.leads.index') }}"
                   class="btn btn-light">
                    Batal
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    Update Lead
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
