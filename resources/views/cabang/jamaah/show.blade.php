@extends('layouts.cabang')

@section('title', 'Detail Jamaah')

@section('content')

{{-- =====================================================
   PAGE HEADER
===================================================== --}}
<div class="page-header mb-16">
    <div>
        <h1 class="page-title">Detail Jamaah</h1>
        <p class="page-subtitle">
            Informasi lengkap jamaah (akses cabang)
        </p>
    </div>

    <div class="d-flex gap-8">
        <a href="{{ route('cabang.jamaah.index') }}"
           class="c-btn outline sm">
            ← Kembali
        </a>

        <a href="{{ route('cabang.jamaah.print.detail', $jamaah->id) }}"
           target="_blank"
           class="c-btn sm">
            🧾 Cetak
        </a>
    </div>
</div>

{{-- =====================================================
   DATA JAMAAH
===================================================== --}}
<div class="c-card mb-16 has-header-bg">
    <div class="c-card__header">
        Data Jamaah
    </div>

    <div class="c-grid cols-3">

        <div>
            <div class="text-muted fs-12">No ID</div>
            <div class="fw-600">{{ $jamaah->no_id }}</div>
        </div>

        <div>
            <div class="text-muted fs-12">Nama</div>
            <div class="fw-600">{{ $jamaah->nama_lengkap }}</div>
        </div>

        <div>
            <div class="text-muted fs-12">No HP</div>
            <div class="fw-600">{{ $jamaah->no_hp }}</div>
        </div>

        <div>
            <div class="text-muted fs-12">Lahir</div>
            <div class="fw-600">
                {{ $jamaah->tempat_lahir }},
                {{ tanggal_indo($jamaah->tanggal_lahir) }}
            </div>
        </div>

        <div>
            <div class="text-muted fs-12">Paket</div>
            <div class="fw-600">
                {{ $jamaah->paket->nama_paket ?? '-' }}
            </div>
        </div>

        <div>
            <div class="text-muted fs-12">Cabang</div>
            <div class="fw-600">
                {{ $jamaah->branch->nama_cabang ?? '-' }}
            </div>
        </div>

        <div>
            <div class="text-muted fs-12">Status</div>
            <span class="c-badge {{ $jamaah->status === 'APPROVED' ? 'success' : 'warning' }}">
                {{ strtoupper($jamaah->status) }}
            </span>
        </div>

    </div>
</div>

{{-- =====================================================
   INPUT PEMBAYARAN
===================================================== --}}
@can('create', [\App\Models\Payments::class, $jamaah])
<div class="c-card mb-16 has-header-bg header-warning">

    <div class="c-card__header">
        Input Pembayaran
    </div>

    @if($hasPendingPayment)
        <div class="c-empty">
            ⚠️ Masih ada pembayaran
            <b>menunggu approval pusat</b>.
            <br>Input pembayaran dikunci sementara.
        </div>
    @else
        @include('cabang.jamaah.partials.payment-form')
    @endif

</div>
@endcan

{{-- =====================================================
   RIWAYAT PEMBAYARAN
===================================================== --}}
<div class="c-card mb-16 has-header-bg">

    <div class="c-card__header">
        Riwayat Pembayaran
    </div>

    @if($jamaah->payments->count())

        <div class="c-table-wrap">
            <table class="c-table is-dense">

                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th class="text-right">Nominal</th>
                        <th>Metode</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($jamaah->payments as $p)
                    <tr>
                        <td data-label="Tanggal">
                            {{ tanggal_indo($p->tanggal_bayar) }}
                        </td>

                        <td data-label="Nominal" class="fw-600 text-right">
                            Rp {{ number_format($p->jumlah) }}
                        </td>

                        <td data-label="Metode">
                            {{ strtoupper($p->metode) }}
                        </td>

                        <td data-label="Status">
                            <span class="c-badge
                                {{ $p->status === 'valid' ? 'success' : 'warning' }}">
                                {{ strtoupper($p->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

    @else
        <div class="c-empty">
            Belum ada pembayaran
        </div>
    @endif

</div>

{{-- =====================================================
   ACTION
===================================================== --}}
@can('update', $jamaah)
<div class="d-flex justify-between mt-16">
    <div></div>
    <a href="{{ route('cabang.jamaah.edit', $jamaah) }}"
       class="c-btn primary sm">
        ✏️ Edit Jamaah
    </a>
</div>
@endcan

@endsection
