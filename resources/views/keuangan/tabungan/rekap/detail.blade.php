@extends('layouts.admin')

@section('title','Detail Mutasi Tabungan')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">

        <div class="page-header-left">

            {{-- BACK BUTTON --}}
            <a href="{{ route('keuangan.tabungan.rekap.index') }}"
            class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
            </a>
            <br>
            <div>
                <h1 class="page-title">
                    Detail Mutasi Tabungan
                </h1>

                <p class="page-subtitle">
                    {{ $tabungan->jamaah->nama_lengkap }}
                    —
                    <strong>{{ $tabungan->nomor_tabungan }}</strong>
                </p>

                <p class="page-subtitle">
                    Periode:
                    <strong>
                        {{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}
                    </strong>
                </p>
            </div>

        </div>

        <div class="page-actions">
            <a href="{{ route(
                'keuangan.tabungan.rekap.detail.pdf',
                ['tabungan' => $tabungan->id, 'bulan' => $bulan, 'tahun' => $tahun]
            ) }}"
            target="_blank"
            class="btn btn-outline-danger btn-sm">
                PDF Mutasi
            </a>
        </div>

    </div>

    {{-- =====================================================
    | SUMMARY
    ===================================================== --}}
    <div class="stat-grid mb-4">

        <div class="card card-stat card-stat-muted">
            <div class="stat-label">Saldo Awal</div>
            <div class="stat-value">
                Rp {{ number_format($summary['saldo_awal'],0,',','.') }}
            </div>
        </div>

        <div class="card card-stat card-stat-success">
            <div class="stat-label">Total Kredit</div>
            <div class="stat-value">
                Rp {{ number_format($summary['total_kredit'],0,',','.') }}
            </div>
        </div>

        <div class="card card-stat card-stat-danger">
            <div class="stat-label">Total Debit</div>
            <div class="stat-value">
                Rp {{ number_format($summary['total_debit'],0,',','.') }}
            </div>
        </div>

        <div class="card card-stat card-stat-primary">
            <div class="stat-label">Saldo Akhir</div>
            <div class="stat-value">
                Rp {{ number_format($summary['saldo_akhir'],0,',','.') }}
            </div>
        </div>

    </div>

    {{-- =====================================================
    | TABLE MUTASI
    ===================================================== --}}
    <div class="card">

        <div class="table-responsive">
            <table class="table table-striped">

                <thead>
                    <tr>
                        <th width="160">Tanggal</th>
                        <th>Keterangan</th>
                        <th class="table-right" width="140">Debit</th>
                        <th class="table-right" width="140">Kredit</th>
                        <th class="table-right" width="160">Saldo</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($rows as $r)
                    <tr>

                        <td data-label="Tanggal">
                            {{ $r['tanggal']->format('d M Y H:i') }}
                        </td>

                        <td data-label="Keterangan">
                            {{ $r['keterangan'] }}
                        </td>

                        <td data-label="Debit"
                            class="table-right text-danger">
                            {{ $r['debit']
                                ? number_format($r['debit'],0,',','.')
                                : '-' }}
                        </td>

                        <td data-label="Kredit"
                            class="table-right text-success">
                            {{ $r['kredit']
                                ? number_format($r['kredit'],0,',','.')
                                : '-' }}
                        </td>

                        <td data-label="Saldo"
                            class="table-right font-semibold">
                            Rp {{ number_format($r['saldo'],0,',','.') }}
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="table-empty">
                            Tidak ada mutasi pada periode ini.
                        </td>
                    </tr>
                @endforelse
                </tbody>

                {{-- ================= TOTAL ================= --}}
                <tfoot>
                    <tr>
                        <th colspan="2" class="table-right">
                            TOTAL
                        </th>

                        <th class="table-right text-danger">
                            {{ $summary['total_debit']
                                ? number_format($summary['total_debit'],0,',','.')
                                : '-' }}
                        </th>

                        <th class="table-right text-success">
                            {{ $summary['total_kredit']
                                ? number_format($summary['total_kredit'],0,',','.')
                                : '-' }}
                        </th>

                        <th class="table-right font-bold">
                            Rp {{ number_format($summary['saldo_akhir'],0,',','.') }}
                        </th>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>

</div>
@endsection
