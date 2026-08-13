@extends('layouts.admin')

@section('title', 'Detail Lead')

@section('content')

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Detail Lead</h1>
        <div class="page-subtitle">
            Informasi lengkap & aktivitas lead
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
| LEAD INFO
====================================================== --}}
<div class="card card-hover mb-4">
    <div class="card-body">
        @include('crm.leads.partials.info', ['lead' => $lead])
    </div>
</div>

{{-- ======================================================
| FOLLOW UP SECTION
====================================================== --}}
<div class="card card-hover mb-4">

    {{-- HEADER --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Riwayat Follow Up</span>

        {{-- ACTION BUTTON --}}
        @can('createFollowUp', $lead)
            @if($lead->status !== 'CLOSED')
                <button
                    class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#followupModal">
                    + Follow Up
                </button>
            @else
                <button
                    class="btn btn-light btn-sm"
                    disabled
                    title="Lead sudah ditutup">
                    Follow Up Terkunci
                </button>
            @endif
        @endcan
    </div>

    {{-- BODY --}}
    <div class="card-body followup-wrapper">

        {{-- LOCK INFO --}}
        @if($lead->status === 'CLOSED')
            <div class="mb-3">
                <span class="badge badge-soft-danger">
                    Lead CLOSED — Follow Up Dikunci
                </span>
            </div>
        @endif

        {{-- FOLLOW UP LIST --}}
        @include('crm.followup._list', [
            'activities' => $lead->activities
        ])
    </div>

</div>

{{-- ======================================================
| CLOSING SECTION
====================================================== --}}
<div class="card card-hover">
    <div class="card-body">
        @include('crm.leads.partials.closing', ['lead' => $lead])
    </div>
</div>

{{-- ======================================================
| MODAL FOLLOW UP (ONLY IF OPEN)
====================================================== --}}
@if($lead->status !== 'CLOSED')
    @include('crm.components.modal-followup', ['lead' => $lead])
@endif

@endsection
