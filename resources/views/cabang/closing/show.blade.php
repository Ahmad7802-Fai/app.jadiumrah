@extends('layouts.cabang')

@section('content')

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="mb-4">
    <a href="{{ route('cabang.leads.show', $lead) }}"
       class="c-btn outline sm mb-6">
        ← Kembali ke Detail Lead
    </a>

    <h1 class="page-title mb-2">
        Dokumen Closing
    </h1>

    <p class="page-subtitle">
        Tampilan read-only dokumen closing (khusus cabang)
    </p>
</div>

{{-- ======================================================
| STATUS BAR
====================================================== --}}
<div class="c-card mb-16">

    <div class="d-flex justify-between align-center">

        <div>
            <div class="text-muted fs-12 mb-2">
                Status Closing
            </div>

            @if($closing->status === 'APPROVED')
                <span class="c-badge success">
                    Closing Approved
                </span>
            @elseif($closing->status === 'PENDING')
                <span class="c-badge warning">
                    Menunggu Approval Pusat
                </span>
            @else
                <span class="c-badge">
                    Draft
                </span>
            @endif
        </div>

        @if($closing->status === 'APPROVED')
            <span class="c-badge success lg">
                CLOSED
            </span>
        @endif

    </div>

</div>

{{-- ======================================================
| INFO LEAD
====================================================== --}}
<div class="c-card mb-16">

    <div class="c-card__header">
        Informasi Lead
    </div>

    <div class="c-info-grid">

        <div class="c-info-item">
            <div class="c-info-label">Nama</div>
            <div class="c-info-value">
                {{ $lead->nama }}
            </div>
        </div>

        <div class="c-info-item">
            <div class="c-info-label">No HP</div>
            <div class="c-info-value">
                {{ $lead->no_hp }}
            </div>
        </div>

        <div class="c-info-item">
            <div class="c-info-label">Email</div>
            <div class="c-info-value">
                {{ $lead->email ?? '-' }}
            </div>
        </div>

        <div class="c-info-item">
            <div class="c-info-label">Cabang</div>
            <div class="c-info-value">
                {{ optional($lead->branch)->nama_cabang ?? '-' }}
            </div>
        </div>

    </div>

</div>

{{-- ======================================================
| RINGKASAN CLOSING
====================================================== --}}
<div class="c-card mb-16">

    <div class="c-card__header">
        Ringkasan Closing
    </div>

    <div class="c-info-grid">

        <div class="c-info-item">
            <div class="c-info-label">Tanggal Pengajuan</div>
            <div class="c-info-value">
                {{ $closing->created_at->format('d M Y H:i') }}
            </div>
        </div>

        @if($closing->approved_at)
        <div class="c-info-item">
            <div class="c-info-label">Disetujui Pada</div>
            <div class="c-info-value">
                {{ $closing->approved_at->format('d M Y H:i') }}
            </div>
        </div>
        @endif

        @if($closing->approved_by)
        <div class="c-info-item">
            <div class="c-info-label">Disetujui Oleh</div>
            <div class="c-info-value">
                {{ optional($closing->approvedBy)->name ?? '-' }}
            </div>
        </div>
        @endif

    </div>

</div>

{{-- ======================================================
| CATATAN CLOSING
====================================================== --}}
@if($closing->catatan)
<div class="c-card mb-16">

    <div class="c-card__header">
        Catatan Closing
    </div>

    <div class="fs-13 text-muted">
        {{ $closing->catatan }}
    </div>

</div>
@endif

{{-- ======================================================
| READ ONLY NOTICE
====================================================== --}}
<div class="c-card warning-soft fs-12">
    🔒 Dokumen ini bersifat <strong>read-only</strong> untuk cabang.
    Seluruh proses approval dan perubahan dilakukan oleh pusat.
</div>

@endsection
