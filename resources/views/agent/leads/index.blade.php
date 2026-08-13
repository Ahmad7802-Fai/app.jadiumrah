@extends('layouts.agent')

@section('page-title','Data Lead')
@section('page-subtitle','Kelola & tindak lanjuti calon jamaah')

@section('content')

{{-- ===========================
| PAGE HEADER
=========================== --}}
<div class="page-header">

    <div class="page-header-text">
        <h2 class="page-title">Data Lead</h2>
        <p class="page-subtitle">
            Kelola & tindak lanjuti calon jamaah
        </p>
    </div>

    <div class="page-header-actions">
        <a href="{{ route('agent.leads.create') }}"
           class="btn btn-primary btn-sm">
            + Tambah Lead
        </a>
    </div>

</div>

{{-- ===========================
| MOBILE & TABLET
| controlled by _responsive-content.scss
=========================== --}}
<div class="mobile-list">

    @forelse($leads as $lead)
        @include('agent.leads.partials._card-mobile', compact('lead'))
    @empty
        <div class="card card-empty text-center">
            <div class="text-xl mb-2">📭</div>
            <div class="font-semibold">Belum ada lead</div>
            <div class="text-muted text-sm mt-1">
                Lead yang masuk akan muncul di sini
            </div>

            <a href="{{ route('agent.leads.create') }}"
               class="btn btn-primary btn-sm mt-4">
                + Tambah Lead Pertama
            </a>
        </div>
    @endforelse

</div>

{{-- ===========================
| DESKTOP TABLE
=========================== --}}
<div class="table-wrapper">

    <table class="table">
        <thead>
            <tr>
                <th>Lead</th>
                <th>Status</th>
                <th class="table-right">Aksi</th>
            </tr>
        </thead>

        <tbody>
        @forelse($leads as $lead)

            @php
                $statusBadge = match(strtoupper($lead->status)) {
                    'NEW'       => 'badge badge-soft-info',
                    'PROSPECT'  => 'badge badge-soft-warning',
                    'FOLLOWUP'  => 'badge badge-soft-info',
                    'MEETING'   => 'badge badge-soft-success',
                    'KOMIT'     => 'badge badge-soft-success',
                    'CLOSING'   => 'badge badge-soft-warning',
                    'CLOSED'    => 'badge badge-soft-success',
                    'LOST'      => 'badge badge-soft-danger',
                    default     => 'badge badge-soft-neutral',
                };
            @endphp


            <tr>
                <td>
                    <div class="table-name">{{ $lead->nama }}</div>
                    <div class="table-sub">
                        {{ $lead->no_hp }} · {{ $lead->created_at->format('d M Y') }}
                    </div>

                    @if($lead->isOverdue())
                        <div class="text-xs text-danger font-semibold mt-1">
                            ⚠ Overdue
                        </div>
                    @endif
                </td>

                <td>
                    <span class="{{ $statusBadge }}">
                        {{ strtoupper($lead->status) }}
                    </span>
                </td>

                <td class="table-right">
                    <a href="{{ route('agent.leads.show',$lead) }}"
                       class="btn btn-primary btn-xs">
                        Kelola
                    </a>
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="3" class="table-empty">
                    Belum ada lead
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

</div>

{{-- ===========================
| PAGINATION
=========================== --}}
@if($leads->hasPages())
<div class="agent-pagination">
    {{ $leads->withQueryString()->links() }}
</div>
@endif

@endsection
