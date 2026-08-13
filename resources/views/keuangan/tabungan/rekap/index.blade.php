@extends('layouts.admin')

@section('title','Rekap Tabungan Umrah')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">

        <div>
            <h1 class="page-title">
                Rekap Bulanan Tabungan Umrah
            </h1>
            <p class="page-subtitle">
                Periode:
                <strong>
                    {{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}
                </strong>
            </p>
        </div>

        <div class="page-actions">

            {{-- STATUS CLOSING --}}
            @if($isLocked)
                <span class="badge badge-muted">
                    🔒 Bulan Ditutup
                </span>

                @if(auth()->user()->role === 'SUPERADMIN')
                    <form method="POST"
                          action="{{ route('keuangan.tabungan.closing.open') }}">
                        @csrf
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <button class="btn btn-warning btn-sm"
                                onclick="return confirm('Buka kembali bulan ini?')">
                            🔓 Buka Bulan
                        </button>
                    </form>
                @endif
            @else
                <form method="POST"
                      action="{{ route('keuangan.tabungan.closing') }}">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Tutup bulan ini? Setelah ditutup, data tidak bisa diubah.')">
                        🔒 Tutup Bulan
                    </button>
                </form>
            @endif

        </div>
    </div>


    {{-- =====================================================
    | FILTER
    ===================================================== --}}
    <form method="GET" class="filter filter-compact mb-4">

        <div class="filter-body">

            <div class="filter-item">
                <label>Bulan</label>
                <select name="bulan" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan==$m?'selected':'' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="filter-item">
                <label>Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $tahun==$y?'selected':'' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="filter-item">
                <label>Nama Jamaah</label>
                <input type="text"
                       name="q"
                       value="{{ $q }}"
                       class="form-control"
                       placeholder="Cari jamaah...">
            </div>

        </div>

        <div class="filter-actions">
            <button class="btn btn-primary btn-sm">
                Tampilkan
            </button>

            <a href="{{ route('keuangan.tabungan.rekap.index') }}"
               class="btn btn-secondary btn-sm">
                Reset
            </a>

            <a href="{{ route('keuangan.tabungan.rekap.pdf', compact('bulan','tahun','q')) }}"
               target="_blank"
               class="btn btn-outline-danger btn-sm">
                PDF
            </a>

            <a href="{{ route('keuangan.tabungan.rekap.excel', compact('bulan','tahun','q')) }}"
               class="btn btn-outline-success btn-sm">
                Excel
            </a>
        </div>

    </form>

    {{-- =====================================================
    | SUMMARY
    ===================================================== --}}
    <div class="stat-grid mb-4">

        <div class="card card-stat card-stat-muted">
            <div class="stat-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Saldo Awal</div>
                <div class="stat-value">
                    Rp {{ number_format($summary['saldo_awal'],0,',','.') }}
                </div>
            </div>
        </div>

        <div class="card card-stat card-stat-success">
            <div class="stat-icon">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Top Up</div>
                <div class="stat-value">
                    Rp {{ number_format($summary['topup'],0,',','.') }}
                </div>
            </div>
        </div>

        <div class="card card-stat card-stat-danger">
            <div class="stat-icon">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Debit</div>
                <div class="stat-value">
                    Rp {{ number_format($summary['debit'],0,',','.') }}
                </div>
            </div>
        </div>

        <div class="card card-stat card-stat-primary">
            <div class="stat-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Saldo Akhir</div>
                <div class="stat-value">
                    Rp {{ number_format($summary['saldo_akhir'],0,',','.') }}
                </div>
            </div>
        </div>

    </div>

    {{-- =====================================================
    | TABLE
    ===================================================== --}}
    <div class="card">

        <div class="table-responsive">
            <table class="table table-striped">

                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th>Nama Jamaah</th>
                        <th>No. Tabungan</th>
                        <th class="table-right">Saldo Awal</th>
                        <th class="table-right">Top Up</th>
                        <th class="table-right">Debit</th>
                        <th class="table-right">Saldo Akhir</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($rekap as $i => $r)
                    <tr onclick="location.href='{{ route('keuangan.tabungan.rekap.detail',$r['tabungan']->id) }}?bulan={{ $bulan }}&tahun={{ $tahun }}'"
                        style="cursor:pointer">

                        <td data-label="#"> {{ $i+1 }} </td>
                        <td data-label="Jamaah">
                            {{ $r['jamaah']->nama_lengkap }}
                        </td>
                        <td data-label="No Tabungan" class="font-semibold">
                            {{ $r['tabungan']->nomor_tabungan }}
                        </td>

                        <td data-label="Saldo Awal" class="table-right">
                            Rp {{ number_format($r['saldo_awal'],0,',','.') }}
                        </td>

                        <td data-label="Top Up" class="table-right text-success">
                            Rp {{ number_format($r['total_topup'],0,',','.') }}
                        </td>

                        <td data-label="Debit" class="table-right text-danger">
                            Rp {{ number_format($r['total_debit'],0,',','.') }}
                        </td>

                        <td data-label="Saldo Akhir" class="table-right font-bold">
                            Rp {{ number_format($r['saldo_akhir'],0,',','.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="table-empty">
                            Tidak ada data tabungan pada periode ini.
                        </td>
                    </tr>
                @endforelse
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="3" class="table-right">TOTAL</th>
                        <th class="table-right">
                            Rp {{ number_format($summary['saldo_awal'],0,',','.') }}
                        </th>
                        <th class="table-right text-success">
                            Rp {{ number_format($summary['topup'],0,',','.') }}
                        </th>
                        <th class="table-right text-danger">
                            Rp {{ number_format($summary['debit'],0,',','.') }}
                        </th>
                        <th class="table-right">
                            Rp {{ number_format($summary['saldo_akhir'],0,',','.') }}
                        </th>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>

</div>
@endsection
