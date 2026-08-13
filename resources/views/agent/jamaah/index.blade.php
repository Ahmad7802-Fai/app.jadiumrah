@extends('layouts.agent')

@section('page-title','Jamaah Saya')
@section('page-subtitle','Daftar jamaah hasil closing Anda')

@section('content')

{{-- ===========================
| PAGE HEADER
=========================== --}}
<div class="page-header">

    <div class="page-header-text">
        <h2 class="page-title">Daftar Jamaah</h2>
        <p class="page-subtitle">Jamaah hasil closing Anda</p>
    </div>

    <div class="page-header-actions">
        <a href="{{ route('agent.jamaah.create') }}"
           class="btn btn-primary btn-sm">
            + Tambah Jamaah
        </a>
    </div>

</div>

{{-- ===========================
| MOBILE / TABLET
=========================== --}}
<div class="card-list d-lg-none">

@forelse($jamaah as $j)
@php
    $harga = $j->harga_disepakati ?? $j->harga_default ?? 0;
    $totalBayar = $j->payments
        ->whereIn('status',['pending','valid'])
        ->sum('jumlah');
    $sisa = max($harga - $totalBayar, 0);

    $tipeClass = match($j->tipe_jamaah) {
        'reguler'  => 'badge badge-soft-info',
        'tabungan' => 'badge badge-soft-success',
        'cicilan'  => 'badge badge-soft-warning',
        default    => 'badge badge-soft-neutral',
    };
@endphp

<div class="card">

    <div class="card-stack">

        {{-- HEADER --}}
        <div class="card-row">
            <div>
                <div class="font-semibold">
                    {{ $j->nama_lengkap }}
                </div>
                <div class="text-xs text-muted">
                    {{ $j->no_id }}
                </div>
            </div>

            <span class="badge {{ $tipeClass }} badge--sm">
                {{ ucfirst($j->tipe_jamaah) }}
            </span>
        </div>

        {{-- INFO --}}
        <div class="card-stack text-sm">

            <div class="card-row">
                <span class="text-muted">Paket</span>
                <strong>{{ $j->nama_paket ?? '-' }}</strong>
            </div>

            <div class="card-row">
                <span class="text-muted">Harga</span>
                <strong>{{ $harga ? number_format($harga) : '-' }}</strong>
            </div>

            <div class="card-row">
                <span class="text-muted">Sisa</span>
                <strong>{{ $harga ? number_format($sisa) : '-' }}</strong>
            </div>

        </div>

        {{-- STATUS + ACTION --}}
        <div class="card-row">

            <div>
                @if($j->status !== 'approved')
                    <span class="badge badge-soft-primary badge--sm">Menunggu</span>
                @elseif($harga <= 0)
                    <span class="badge badge-soft-warning badge--sm">Harga?</span>
                @elseif($totalBayar === 0)
                    <span class="badge badge-soft-warning badge--sm">Belum Bayar</span>
                @elseif($sisa > 0)
                    <span class="badge badge-soft-warning badge--sm">Belum Lunas</span>
                @else
                    <span class="badge badge-soft-success badge--sm">Lunas</span>
                @endif
            </div>

            <a href="{{ route('agent.jamaah.show', $j->id) }}"
               class="btn btn-primary btn-xs">
                Detail
            </a>

        </div>

    </div>

</div>

@empty
<div class="card text-center text-sm text-muted">
    Belum ada jamaah.
</div>
@endforelse

</div>

{{-- ===========================
| DESKTOP VIEW
=========================== --}}
<div class="table-wrapper desktop-only">

<table class="table">

    <thead>
        <tr>
            <th>Jamaah</th>
            <th>Paket</th>
            <th>Tipe</th>
            <th class="table-right">Harga</th>
            <th class="table-right">Sisa</th>
            <th>Status</th>
            <th class="table-right">Aksi</th>
        </tr>
    </thead>

    <tbody>
    @forelse($jamaah as $j)
    @php
        $harga = $j->harga_disepakati ?? $j->harga_default ?? 0;
        $totalBayar = $j->payments
            ->whereIn('status',['pending','valid'])
            ->sum('jumlah');
        $sisa = max($harga - $totalBayar, 0);

        $tipeBadge = match($j->tipe_jamaah) {
            'reguler'  => 'badge badge-soft-info',
            'tabungan' => 'badge badge-soft-success',
            'cicilan'  => 'badge badge-soft-warning',
            default    => 'badge badge-soft-neutral',
        };
    @endphp

    <tr>
        <td>
            <div class="table-name">{{ $j->nama_lengkap }}</div>
            <div class="table-sub">{{ $j->no_id }}</div>
        </td>

        <td>{{ $j->nama_paket ?? '-' }}</td>

        <td>
            <span class="badge {{ $tipeBadge }}">
                {{ ucfirst($j->tipe_jamaah) }}
            </span>
        </td>

        <td class="table-right">
            {{ $harga ? number_format($harga) : '-' }}
        </td>

        <td class="table-right">
            {{ $harga ? number_format($sisa) : '-' }}
        </td>

        <td>
            @if($j->status !== 'approved')
                <span class="badge badge-soft-success">Menunggu</span>
            @elseif($harga <= 0)
                <span class="badge badge-soft-warning">Harga?</span>
            @elseif($totalBayar === 0)
                <span class="badge badge-soft-warning">Belum Bayar</span>
            @elseif($sisa > 0)
                <span class="badge badge-soft-warning">Belum Lunas</span>
            @else
                <span class="badge badge-soft-success">Lunas</span>
            @endif
        </td>

        <td class="table-right">
            <a href="{{ route('agent.jamaah.show',$j->id) }}"
               class="btn btn-primary btn-xs">
                Detail
            </a>
        </td>
    </tr>

    @empty
    <tr>
        <td colspan="7" class="table-empty">
            Belum ada jamaah.
        </td>
    </tr>
    @endforelse
    </tbody>

</table>

</div>

{{-- ===========================
| PAGINATION
=========================== --}}
@if($jamaah->hasPages())
<div class="agent-pagination">
    {{ $jamaah->links() }}
</div>
@endif

@endsection

@push('scripts')
<script>
function toggleWaMenu(id) {
    document.querySelectorAll('[id^="wa-menu-"]').forEach(el => {
        if (el.id !== 'wa-menu-' + id) {
            el.classList.add('hidden');
        }
    });

    const menu = document.getElementById('wa-menu-' + id);
    menu.classList.toggle('hidden');
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('.relative')) {
        document.querySelectorAll('[id^="wa-menu-"]').forEach(el => {
            el.classList.add('hidden');
        });
    }
});
</script>
@endpush
