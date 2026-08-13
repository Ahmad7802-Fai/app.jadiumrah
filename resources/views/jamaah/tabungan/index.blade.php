@extends('layouts.jamaah')

@section('title','Riwayat Tabungan')

@section('content')

{{-- ================= PAGE HEADER ================= --}}
<div class="j-page-title mb-3">
    <h2>Riwayat Transaksi</h2>
    <p class="text-muted fs-13">
        Catatan setoran dan mutasi tabungan umrah Anda
    </p>
</div>

{{-- ================= EMPTY STATE ================= --}}
@if($transaksi->isEmpty())

    <div class="j-card j-card--soft text-center">
        <div class="fs-13 text-muted">
            Belum ada transaksi tabungan
        </div>
        <div class="fs-12 text-muted mt-1">
            Setiap setoran yang Anda lakukan akan muncul di sini
        </div>
    </div>

@else

{{-- ================= TOTAL SALDO ================= --}}
<div class="j-card mb-3">
    <div class="j-card__label">Total Saldo Tabungan</div>
    <div class="j-card__value">
        Rp {{ number_format($tabungan->saldo ?? 0,0,',','.') }}
    </div>
</div>

{{-- ================= TRANSACTION LIST ================= --}}
<div class="j-history j-history--compact">

@php
    // saldo berjalan dimulai dari saldo akhir (list DESC)
    $runningSaldo = $tabungan->saldo ?? 0;
@endphp

@foreach($transaksi as $row)
    @php
        $isMasuk = $row->jenis === 'TOPUP';
    @endphp

    <div class="j-history-item">

        {{-- DOT --}}
        <div class="j-history-dot {{ $isMasuk ? 'success' : 'danger' }}"></div>

        {{-- CARD --}}
        <div class="j-card j-history-card">

            {{-- NOMINAL + BADGE --}}
            <div class="d-flex justify-between align-center">
                <strong>
                    Rp {{ number_format($row->amount,0,',','.') }}
                </strong>

                <span class="j-badge {{ $isMasuk ? 'success' : 'danger' }}">
                    {{ $isMasuk ? 'MASUK' : 'KELUAR' }}
                </span>
            </div>

            {{-- META --}}
            <div class="j-history-meta">
                {{ $row->created_at->translatedFormat('d M Y • H:i') }}
            </div>

            {{-- KETERANGAN --}}
            <div class="fs-13 text-muted mt-1">
                {{ $row->keterangan ?? 'Transaksi Tabungan' }}
            </div>

            {{-- SALDO BERJALAN --}}
            <div class="fs-12 text-muted mt-1">
                Saldo: Rp {{ number_format($runningSaldo,0,',','.') }}
            </div>

            {{-- BUKTI SETORAN --}}
            @if($isMasuk && $row->buktiSetoran)
                <div class="mt-1">
                    <a href="{{ route('jamaah.tabungan.bukti.show', $row->buktiSetoran->id) }}"
                       target="_blank"
                       class="fs-12 text-primary">
                        Lihat kwitansi
                    </a>
                </div>
            @endif

        </div>
    </div>

    @php
        // saldo mundur (karena list DESC)
        $runningSaldo -= $row->amount;
    @endphp
@endforeach

</div>
@endif

@endsection
