@extends('layouts.agent')

@section('page-title','Komisi Agent')
@section('page-subtitle','Ringkasan dan histori komisi Anda')

@section('content')

{{-- =====================================================
| RINGKASAN KOMISI
===================================================== --}}
<section class="kpi-section">
    <h4 class="kpi-section-title">Ringkasan Komisi</h4>

    <div class="card-grid card-grid-stat">
        <div class="card card-stat is-highlight">
            <div class="card-stat-title">Total Komisi</div>
            <div class="card-stat-value">
                Rp {{ number_format($summary['total']) }}
            </div>
        </div>

        <div class="card card-stat">
            <div class="card-stat-title">Pending</div>
            <div class="card-stat-value">
                Rp {{ number_format($summary['pending']) }}
            </div>
        </div>

        <div class="card card-stat">
            <div class="card-stat-title">Siap Dicairkan</div>
            <div class="card-stat-value">
                Rp {{ number_format($summary['available']) }}
            </div>
        </div>

        <div class="card card-stat">
            <div class="card-stat-title">Sudah Dibayar</div>
            <div class="card-stat-value">
                Rp {{ number_format($summary['paid']) }}
            </div>
        </div>
    </div>
</section>

{{-- =====================================================
| ACTION BAR (INLINE CONFIRM)
===================================================== --}}
<div class="flex justify-end mt-4 mb-4 gap-2">

@php
    $agent = auth()->user()->agent ?? null;
    $hasBank = $agent
        && $agent->bank_account_number
        && $agent->bank_name
        && $agent->bank_account_name;
@endphp

@if(!$hasBank)
    <a href="{{ route('agent.profile.edit') }}"
       class="btn btn-warning btn-sm">
        ⚠ Lengkapi Rekening Bank
    </a>

@elseif($summary['available'] > 0 && ! $hasActivePayout)
    <form method="POST"
          action="{{ route('agent.payout.request') }}"
          onsubmit="return confirm(
            'Ajukan pencairan komisi sebesar Rp {{ number_format($summary['available']) }}?\n\nPastikan data rekening sudah benar.'
          )">
        @csrf
<a href="{{ route('agent.payout.confirm') }}"
   class="btn btn-primary btn-sm">
    💸 Ajukan Pencairan
</a>

    </form>

@elseif($hasActivePayout)
    <button class="btn btn-secondary btn-sm" disabled>
        ⏳ Pencairan diproses
    </button>

@else
    <button class="btn btn-secondary btn-sm" disabled>
        💸 Belum ada komisi
    </button>
@endif

</div>

{{-- =====================================================
| HISTORI KOMISI — DESKTOP
===================================================== --}}
<div class="card desktop-only">

    <div class="card-header">
        <h3 class="card-title">Histori Komisi</h3>
    </div>

    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="table">
                <colgroup>
                    <col style="width:40%">
                    <col style="width:12%">
                    <col style="width:18%">
                    <col style="width:15%">
                    <col style="width:15%">
                </colgroup>

                <thead>
                    <tr>
                        <th>Jamaah</th>
                        <th class="table-right">Persen</th>
                        <th class="table-right">Nominal</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($komisi as $k)
                    <tr>
                        <td>
                            <div class="table-name">
                                {{ $k->jamaah->nama_lengkap ?? '-' }}
                            </div>
                            <div class="table-sub">
                                {{ $k->jamaah->no_id ?? '' }}
                            </div>
                        </td>

                        <td class="table-right">
                            {{ number_format($k->komisi_persen,2) }}%
                        </td>

                        <td class="table-right font-semibold">
                            Rp {{ number_format($k->komisi_nominal) }}
                        </td>

                        <td>
                            @switch($k->status)
                                @case(\App\Models\KomisiLogs::STATUS_PENDING)
                                    <span class="badge badge-soft-warning">Pending</span>
                                    @break
                                @case(\App\Models\KomisiLogs::STATUS_AVAILABLE)
                                    <span class="badge badge-soft-primary">Siap</span>
                                    @break
                                @case(\App\Models\KomisiLogs::STATUS_REQUESTED)
                                    <span class="badge badge-soft-info">Proses</span>
                                    @break
                                @case(\App\Models\KomisiLogs::STATUS_PAID)
                                    <span class="badge badge-soft-success">Paid</span>
                                    @break
                            @endswitch
                        </td>

                        <td class="text-sm text-gray-500">
                            {{ $k->created_at->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="table-empty">
                            Belum ada komisi
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- =====================================================
| HISTORI KOMISI — MOBILE
===================================================== --}}
<div class="mobile-only card-grid">

@forelse($komisi as $k)
<div class="card">
    <div class="flex justify-between items-start mb-2">
        <div>
            <div class="font-semibold text-sm">
                {{ $k->jamaah->nama_lengkap ?? '-' }}
            </div>
            <div class="text-xs text-gray-500">
                {{ $k->jamaah->no_id ?? '' }}
            </div>
        </div>

        @switch($k->status)
            @case(\App\Models\KomisiLogs::STATUS_PENDING)
                <span class="badge badge-soft-secondary">Pending</span>
                @break
            @case(\App\Models\KomisiLogs::STATUS_AVAILABLE)
                <span class="badge badge-soft-primary">Siap</span>
                @break
            @case(\App\Models\KomisiLogs::STATUS_REQUESTED)
                <span class="badge badge-soft-info">Proses</span>
                @break
            @case(\App\Models\KomisiLogs::STATUS_PAID)
                <span class="badge badge-soft-success">Paid</span>
                @break
        @endswitch
    </div>

    <div class="text-xs space-y-1">
        <div class="flex justify-between">
            <span>Persen</span>
            <strong>{{ number_format($k->komisi_persen,2) }}%</strong>
        </div>

        <div class="flex justify-between">
            <span>Nominal</span>
            <strong>Rp {{ number_format($k->komisi_nominal) }}</strong>
        </div>

        <div class="text-[10px] text-gray-400 mt-1">
            {{ $k->created_at->format('d M Y') }}
        </div>
    </div>
</div>
@empty
<div class="card p-4 text-center text-sm text-gray-500">
    Belum ada komisi
</div>
@endforelse

</div>

{{-- =====================================================
| PAGINATION
===================================================== --}}
@if($komisi->hasPages())
<div class="agent-pagination mt-4">
    {{ $komisi->links() }}
</div>
@endif

@endsection
