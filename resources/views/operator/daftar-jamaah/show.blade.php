@extends('layouts.admin')

@section('title','Detail Jamaah')

@section('content')
<div class="page-jamaah">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Detail Jamaah</h1>
            <p class="text-muted text-sm">
                Informasi lengkap data jamaah
            </p>
        </div>

        {{-- HEADER BOTTOM ACTION --}}
        <div class="desktop-only gap-2">
            <a href="{{ route('operator.daftar-jamaah.print', $item->id) }}"
            class="btn btn-outline-danger btn-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </a>

            <a href="{{ route('operator.daftar-jamaah.edit', $item->id) }}"
            class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>

            <a href="{{ route('operator.daftar-jamaah.index') }}"
            class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- MOBILE ACTION --}}
    <div class="d-md-none d-grid gap-2 mb-3">
        <a href="{{ route('operator.daftar-jamaah.print', $item->id) }}"
           class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf"></i> PDF
        </a>

        <a href="{{ route('operator.daftar-jamaah.edit', $item->id) }}"
           class="btn btn-warning btn-sm">
            <i class="fas fa-edit"></i> Edit
        </a>

        <a href="{{ route('operator.daftar-jamaah.index') }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- ===============================
       PROFILE CARD
    ================================ --}}
    <div class="card card-hover mb-4">
        <div class="card-body">
            <div class="row g-4 align-items-center">

                {{-- FOTO --}}
                <div class="col-md-3 text-center">
                    <img src="{{ $item->foto ? asset('storage/'.$item->foto) : asset('noimage.jpg') }}"
                         class="avatar avatar-xl mb-2">

                    <div class="fw-semibold">{{ $item->nama_lengkap }}</div>
                    <div class="text-muted text-sm">{{ $item->no_id }}</div>

                    <div class="d-flex justify-content-center gap-2 mt-2">
                        <span class="badge badge-soft-primary">
                            {{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                        <span class="badge badge-soft-secondary">
                            {{ $item->usia }} th
                        </span>
                    </div>
                </div>

                {{-- META --}}
                <div class="col-md-9">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <h6 class="section-title">Informasi Pribadi</h6>
                            <table class="table table-compact table-borderless">
                                <tr><th>No ID</th><td>{{ $item->no_id }}</td></tr>
                                <tr><th>NIK</th><td>{{ $item->nik }}</td></tr>
                                <tr><th>No HP</th><td>{{ $item->no_hp }}</td></tr>
                                <tr><th>Nama Ayah</th><td>{{ $item->nama_ayah }}</td></tr>
                                <tr><th>Nama Passport</th><td>{{ $item->nama_passport ?? '-' }}</td></tr>
                                <tr>
                                    <th>TTL</th>
                                    <td>
                                        {{ $item->tempat_lahir }},
                                        {{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d M Y') }}
                                    </td>
                                </tr>
                                <tr><th>Status</th><td>{{ $item->status_pernikahan }}</td></tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="section-title">Informasi Umrah</h6>
                            <table class="table table-compact table-borderless">
                                <tr><th>Paket</th><td><strong>{{ $item->paket }}</strong></td></tr>
                                <tr>
                                    <th>Keberangkatan</th>
                                    <td>
                                        @if($item->keberangkatan)
                                            <span class="badge badge-soft-info">
                                                {{ $item->keberangkatan->kode_keberangkatan }}
                                            </span>
                                            <div class="text-muted text-xs">
                                                {{ date('d M Y', strtotime($item->keberangkatan->tanggal_berangkat)) }}
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kamar</th>
                                    <td>
                                        <span class="badge badge-soft-primary">
                                            {{ strtoupper($item->tipe_kamar) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr><th>Deposit</th><td>Rp {{ number_format($item->deposit) }}</td></tr>
                                <tr><th>Sisa</th><td>Rp {{ number_format($item->sisa) }}</td></tr>
                                <tr><th>Keterangan</th><td>{{ $item->keterangan ?? '-' }}</td></tr>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ===============================
       CABANG & AGENT
    ================================ --}}
    <div class="card card-hover mb-4">
        <div class="card-body">
            <h6 class="section-title">Cabang & Agent</h6>

            <table class="table table-compact table-borderless">
                <tr>
                    <th width="160">Cabang</th>
                    <td>{{ $item->branch?->nama_cabang ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Agent</th>
                    <td>{{ $item->agent?->nama ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ===============================
       INFORMASI KESEHATAN
    ================================ --}}
    <div class="card card-hover mb-5">
        <div class="card-body">
            <h6 class="section-title">Informasi Kesehatan</h6>

            <table class="table table-compact table-borderless">
                <tr><th>Pernah Umroh</th><td>{{ $item->pernah_umroh }}</td></tr>
                <tr><th>Pernah Haji</th><td>{{ $item->pernah_haji }}</td></tr>
                <tr><th>Merokok</th><td>{{ $item->merokok }}</td></tr>
                <tr><th>Penyakit Khusus</th><td>{{ $item->penyakit_khusus }}</td></tr>
                <tr><th>Nama Penyakit</th><td>{{ $item->nama_penyakit ?? '-' }}</td></tr>
                <tr><th>Kursi Roda</th><td>{{ $item->kursi_roda }}</td></tr>
            </table>
        </div>
    </div>
{{-- ===============================
   RIWAYAT PEMBAYARAN
=============================== --}}
<div class="card card-hover mb-5">
    <div class="card-body">

        <h6 class="section-title d-flex align-items-center gap-2">
            <i class="fas fa-receipt"></i>
            Riwayat Pembayaran
        </h6>

        @if($item->payments && $item->payments->count())

            {{-- DESKTOP TABLE --}}
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-compact">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->payments as $p)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d M Y') }}
                                    </td>
                                    <td>{{ strtoupper($p->metode ?? '-') }}</td>
                                    <td>
                                        Rp {{ number_format($p->nominal) }}
                                    </td>
                                    <td>
                                        <span class="badge
                                            {{ $p->status === 'valid'
                                                ? 'badge-soft-success'
                                                : 'badge-soft-warning' }}">
                                            {{ strtoupper($p->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">
                                        {{ $p->keterangan ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MOBILE CARD LIST --}}
            <div class="d-md-none d-flex flex-column gap-2">
                @foreach($item->payments as $p)
                    <div class="border rounded-lg p-sm bg-soft">
                        <div class="d-flex justify-content-between">
                            <strong>
                                Rp {{ number_format($p->nominal) }}
                            </strong>
                            <span class="badge
                                {{ $p->status === 'valid'
                                    ? 'badge-soft-success'
                                    : 'badge-soft-warning' }}">
                                {{ strtoupper($p->status) }}
                            </span>
                        </div>

                        <div class="text-sm text-muted mt-1">
                            {{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d M Y') }}
                            • {{ strtoupper($p->metode ?? '-') }}
                        </div>

                        @if($p->keterangan)
                            <div class="text-xs mt-1">
                                {{ $p->keterangan }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

        @else
            <div class="table-empty">
                Belum ada riwayat pembayaran
            </div>
        @endif

    </div>
</div>

</div>
@endsection
