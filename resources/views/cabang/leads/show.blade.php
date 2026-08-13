@extends('layouts.cabang')

@section('title', 'Detail Lead')

@section('content')

{{-- =====================================================
   PAGE HEADER
===================================================== --}}
<div class="page-header mb-16">
    <div>
        <a href="{{ route('cabang.leads.index') }}"
           class="c-btn outline sm mb-6">
            ← Kembali
        </a>

        <h1 class="page-title">Detail Lead</h1>
        <p class="page-subtitle">
            Informasi lead & riwayat follow up
        </p>
    </div>
</div>

{{-- =====================================================
   LEAD INFO
===================================================== --}}
<div class="c-card has-header-bg mb-16">

    <div class="c-card__header">
        Informasi Lead
    </div>

    <div class="c-card__body">

        <div class="c-grid auto">

            {{-- Nama --}}
            <div class="c-info-item">
                <div class="c-info-label">Nama</div>
                <div class="c-info-value">
                    {{ $lead->nama }}
                </div>
            </div>

            {{-- No HP --}}
            <div class="c-info-item">
                <div class="c-info-label">No HP</div>
                <div class="c-info-value">
                    {{ $lead->no_hp }}
                </div>
            </div>

            {{-- Email --}}
            <div class="c-info-item">
                <div class="c-info-label">Email</div>
                <div class="c-info-value">
                    {{ $lead->email ?? '-' }}
                </div>
            </div>

            {{-- Sumber --}}
            <div class="c-info-item">
                <div class="c-info-label">Sumber</div>
                <div class="c-info-value">
                    {{ optional($lead->sumber)->nama_sumber ?? '-' }}
                </div>
            </div>

            {{-- Channel --}}
            <div class="c-info-item">
                <div class="c-info-label">Channel</div>
                <div class="c-info-value">
                    <span class="c-badge info">
                        {{ ucfirst($lead->channel) }}
                    </span>
                </div>
            </div>

            {{-- Status --}}
            <div class="c-info-item">
                <div class="c-info-label">Status</div>
                <div class="c-info-value">
                    @if($lead->status === 'NEW')
                        <span class="c-badge warning">NEW</span>
                    @elseif($lead->status === 'ACTIVE')
                        <span class="c-badge success">ACTIVE</span>
                    @else
                        <span class="c-badge danger">CLOSED</span>
                    @endif
                </div>
            </div>

            {{-- Agent --}}
            <div class="c-info-item">
                <div class="c-info-label">Agent</div>
                <div class="c-info-value">
                    {{ optional($lead->agent)->nama ?? '-' }}
                </div>
            </div>

        </div>

    </div>
</div>

{{-- =====================================================
   FOLLOW UP
===================================================== --}}
<div class="c-card has-header-bg mb-16">

    {{-- HEADER --}}
    <div class="c-card__header d-flex justify-between align-center">
        <span>Riwayat Follow Up</span>

        @if($lead->status !== 'CLOSED')
            <button
            type="button"
            class="c-btn primary sm"
            onclick="openModal('followupModal')">
            + Follow Up
            </button>
        @endif
    </div>

    {{-- BODY --}}
    <div class="c-card__body p-0">

        @if($lead->activities->isEmpty())
            <div class="c-empty">
                Belum ada follow up
            </div>
        @else
            <div class="c-table-wrap">
                <table class="c-table is-dense">
                    <thead>
                        <tr>
                            <th>Aktivitas</th>
                            <th>Hasil</th>
                            <th>Next Action</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($lead->activities as $act)
                        <tr>
                            <td class="fw-600">
                                {{ strtoupper($act->aktivitas) }}
                            </td>

                            <td>
                                {{ $act->hasil }}
                            </td>

                            <td>
                                {{ $act->next_action ?: '–' }}
                            </td>

                            <td class="fs-12 text-muted">
                                {{ $act->created_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>

{{-- =====================================================
   MODAL FOLLOW UP
===================================================== --}}
@include('cabang.leads._modal-followup', ['lead' => $lead])

{{-- =====================================================
   CLOSING (READ ONLY)
===================================================== --}}
@php
    $closing = $lead->closing;
@endphp

<div class="c-card has-header-bg mb-16">

    <div class="c-card__header d-flex justify-between align-center">
        <span>Closing</span>

        @if($lead->status === 'CLOSED')
            <span class="c-badge danger">CLOSED</span>
        @elseif($closing)
            <span class="c-badge warning">DIAJUKAN</span>
        @else
            <span class="c-badge">BELUM</span>
        @endif
    </div>

    <div class="c-card__body">

        @if($lead->status === 'CLOSED' && $closing)
            <p class="fs-13 mb-8">
                Closing telah <strong>disetujui</strong>.
                Lead ini terkunci dan siap diproses.
            </p>

            <a href="{{ route('cabang.closing.show', $closing) }}"
               class="c-btn outline sm">
                📄 Lihat Dokumen Closing
            </a>

        @elseif($closing)
            <p class="fs-13 mb-8">
                Closing sedang <strong>menunggu approval pusat</strong>.
            </p>

            <a href="{{ route('cabang.closing.show', $closing) }}"
               class="c-btn outline sm">
                Lihat Detail Closing
            </a>

        @else
            <p class="fs-13 text-muted">
                Closing belum dapat diajukan.
            </p>
        @endif

    </div>
</div>

{{-- =====================================================
   FOOTER ACTION
===================================================== --}}
@if($lead->status !== 'CLOSED')
<div class="d-flex gap-8">
    <a href="{{ route('cabang.leads.edit', $lead) }}"
       class="c-btn outline">
        ✏️ Edit Lead
    </a>
</div>
@endif

@endsection
