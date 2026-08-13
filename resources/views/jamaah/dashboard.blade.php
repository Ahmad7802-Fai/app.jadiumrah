@extends('layouts.jamaah')

@section('title','Dashboard Jamaah')

@section('content')

@php
    $user = auth('jamaah')->user();

    $lastTopup = \App\Models\TabunganTopup::where('jamaah_id', $user->jamaah_id)
        ->latest()
        ->first();
@endphp

{{-- ================= GREETING ================= --}}
<div class="j-greeting">
    <div class="j-greeting__text">
        Assalamu’alaikum, <strong>{{ $jamaah->nama_lengkap }}</strong>
    </div>

    <div class="j-greeting__sub">
        Semoga Allah mudahkan langkah umrah Anda 🤲
    </div>
</div>


{{-- ================= PROGRAM UMRAH ================= --}}
<div class="j-card">
    <div class="j-card__label">Program Umrah Anda</div>

    <div class="j-row">
        <div class="j-row__value">
            {{ $jamaah->paket_aktif?->nama_paket ?? 'Paket Belum Ditentukan' }}
        </div>

        <span class="j-badge primary">
            {{ statusKeberangkatanLabel($jamaah->status_keberangkatan) }}
        </span>
    </div>

    <div class="j-card__hint" style="margin-top:8px">
        Estimasi Berangkat:
        <strong>
            {{ $jamaah->keberangkatan?->tanggal_berangkat
                ? \Carbon\Carbon::parse($jamaah->keberangkatan->tanggal_berangkat)
                    ->translatedFormat('d F Y')
                : 'Menunggu Penjadwalan'
            }}
        </strong>
    </div>

    @if($jamaah->keberangkatan?->kode_keberangkatan)
        <div class="j-card__hint">
            Kode Keberangkatan:
            <strong>{{ $jamaah->keberangkatan->kode_keberangkatan }}</strong>
        </div>
    @endif
</div>

{{-- ================= SALDO TABUNGAN ================= --}}
<div class="j-card">
    <div class="j-card__label">Saldo Tabungan Umrah</div>

    <div class="j-card__value">
        Rp {{ number_format($tabungan?->saldo ?? 0, 0, ',', '.') }}
    </div>

    @if($tabungan && $tabungan->target_nominal > 0)
        @php
            $progress = min(100, ($tabungan->saldo / $tabungan->target_nominal) * 100);
        @endphp

        <div class="j-progress">
            <div class="j-progress__bar" style="width: {{ $progress }}%"></div>
        </div>

        <div class="j-card__hint">
            Target: Rp {{ number_format($tabungan->target_nominal,0,',','.') }}
        </div>
    @endif
</div>
{{-- ================= PROGRESS PEMBAYARAN ================= --}}
<div class="j-card">
    <div class="j-card__label">Progress Pembayaran</div>

    <div class="j-row">
        <div class="j-row__value">Harga Paket</div>
        <strong>
            Rp {{ number_format($hargaPaket, 0, ',', '.') }}
        </strong>
    </div>

    <div class="j-row" style="margin-top:6px">
        <div class="j-row__value">Sudah Dibayar</div>
        <strong class="text-success">
            Rp {{ number_format($totalBayar, 0, ',', '.') }}
        </strong>
    </div>

    <div class="j-row" style="margin-top:6px">
        <div class="j-row__value">Sisa Pembayaran</div>
        <strong class="text-danger">
            Rp {{ number_format($sisaPembayaran, 0, ',', '.') }}
        </strong>
    </div>

    <div class="j-progress" style="margin-top:12px">
        <div class="j-progress__bar"
             style="width: {{ $progressPembayaran }}%">
        </div>
    </div>

    <div class="j-card__hint" style="margin-top:6px">
        {{ $progressPembayaran }}% pembayaran terpenuhi
    </div>
</div>

{{-- ================= STATUS TABUNGAN ================= --}}
<div class="j-card">
    <div class="j-card__label">Status Tabungan</div>

    <div class="j-row">
        <div class="j-row__value">Status Akun</div>
        <span class="j-badge success">AKTIF</span>
    </div>

    @if($lastTopup)
        <div class="j-row" style="margin-top:10px">
            <div class="j-row__value">
                Top Up Terakhir<br>
                <small class="text-muted">
                    Rp {{ number_format($lastTopup->amount,0,',','.') }}
                </small>
            </div>

            <span class="j-badge {{ strtolower($lastTopup->status) }}">
                {{ $lastTopup->status }}
            </span>
        </div>
    @endif
</div>

{{-- ================= QUICK LINKS ================= --}}
<div class="j-card j-link">
    <a href="{{ route('jamaah.tabungan.index') }}">
        <div>
            <strong>Riwayat Transaksi</strong><br>
            <small class="text-muted">Lihat histori tabungan Anda</small>
        </div>
        <i class="fas fa-chevron-right"></i>
    </a>
</div>

<div class="j-card j-link">
    <a href="{{ route('jamaah.notifications.index') }}">
        <div>
            <strong>Notifikasi</strong><br>
            <small class="text-muted">Informasi & update terbaru</small>
        </div>

        @if($notifUnread > 0)
            <span class="j-badge danger">{{ $notifUnread }}</span>
        @endif
    </a>
</div>

@endsection
