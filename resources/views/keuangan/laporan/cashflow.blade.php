@extends('layouts.admin')

@section('title', 'Laporan Cashflow')

@section('content')
<div class="page-container page-wide">

    {{-- =====================================================
    | HEADER + FILTER
    ===================================================== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

        <div class="mb-2 mb-md-0">
            <h4 class="fw-bold mb-0">Laporan Arus Kas (Cashflow)</h4>
            <small class="text-muted">
                Periode: {{ $bulanNama }} {{ $tahun }}
            </small>
        </div>

        <form method="GET" class="d-flex flex-wrap gap-2 mt-2 mt-md-0">

            {{-- BACK --}}
            <a href="{{ route('keuangan.laporan.index') }}"
               class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                <span class="btn-text">Kembali</span>
            </a>

            {{-- BULAN --}}
            <select name="bulan" class="form-select form-select-sm">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == $bulan ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endforeach
            </select>

            {{-- TAHUN --}}
            <select name="tahun" class="form-select form-select-sm">
                @foreach(range(date('Y')-5, date('Y')+1) as $y)
                    <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i>
                <span class="btn-text">Tampilkan</span>
            </button>

            <a href="{{ route('keuangan.laporan.cashflow.pdf', request()->query()) }}"
               class="btn btn-outline-danger btn-sm">
                <i class="fas fa-file-pdf"></i>
                <span class="btn-text">PDF</span>
            </a>

        </form>
    </div>

    {{-- =====================================================
    | SUMMARY
    ===================================================== --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 shadow-sm h-100">
                <div class="small text-muted">Total Pemasukan</div>
                <h4 class="fw-bold text-success mb-0">
                    Rp {{ number_format($totalCashIn,0,',','.') }}
                </h4>
                <small class="text-muted">Jamaah + Layanan</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 shadow-sm h-100">
                <div class="small text-muted">Total Pengeluaran</div>
                <h4 class="fw-bold text-danger mb-0">
                    Rp {{ number_format($totalCashOut,0,',','.') }}
                </h4>
                <small class="text-muted">Trip + Vendor + Opex</small>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card p-3 rounded-4 shadow-sm h-100">
                <div class="small text-muted">Saldo Akhir</div>
                <h3 class="fw-bold {{ $netCashflow >= 0 ? 'text-success':'text-danger' }}">
                    Rp {{ number_format($netCashflow,0,',','.') }}
                </h3>
                <small class="text-muted">Pemasukan – Pengeluaran</small>
            </div>
        </div>

    </div>

    {{-- =====================================================
    | DETAIL PEMASUKAN
    ===================================================== --}}
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">

            <h5 class="fw-bold mb-3 text-success">Pemasukan</h5>

            <table class="table table-bordered table-sm align-middle">
                <tbody>
                    <tr>
                        <td>Pemasukan Jamaah</td>
                        <td class="text-end fw-bold text-success">
                            Rp {{ number_format($cashIn['jamaah'],0,',','.') }}
                        </td>
                    </tr>
                    <tr>
                        <td>Pemasukan Layanan</td>
                        <td class="text-end fw-bold text-success">
                            Rp {{ number_format($cashIn['layanan'],0,',','.') }}
                        </td>
                    </tr>
                    <tr class="table-light">
                        <td><strong>Total Pemasukan</strong></td>
                        <td class="text-end fw-bold">
                            Rp {{ number_format($cashIn['total'],0,',','.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

    {{-- =====================================================
    | DETAIL PENGELUARAN
    ===================================================== --}}
    <div class="card shadow-sm rounded-4 mb-5">
        <div class="card-body">

            <h5 class="fw-bold mb-3 text-danger">Pengeluaran</h5>

            <table class="table table-bordered table-sm align-middle">
                <tbody>
                    <tr>
                        <td>Biaya Trip</td>
                        <td class="text-end fw-bold text-danger">
                            Rp {{ number_format($cashOut['trip'],0,',','.') }}
                        </td>
                    </tr>
                    <tr>
                        <td>Biaya Vendor</td>
                        <td class="text-end fw-bold text-danger">
                            Rp {{ number_format($cashOut['vendor'],0,',','.') }}
                        </td>
                    </tr>
                    <tr>
                        <td>Biaya Operasional</td>
                        <td class="text-end fw-bold text-danger">
                            Rp {{ number_format($cashOut['operational'],0,',','.') }}
                        </td>
                    </tr>
                    <tr>
                        <td>Biaya Marketing</td>
                        <td class="text-end fw-bold text-danger">
                            Rp {{ number_format($cashOut['marketing'],0,',','.') }}
                        </td>
                    </tr>
                    <tr class="table-light">
                        <td><strong>Total Pengeluaran</strong></td>
                        <td class="text-end fw-bold">
                            Rp {{ number_format($cashOut['total'],0,',','.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

    {{-- =====================================================
    | TREND 6 BULAN
    ===================================================== --}}
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Trend Cashflow 6 Bulan</h6>
            <canvas id="cashflowTrend" height="120"></canvas>
        </div>
    </div>

</div>

{{-- =====================================================
| CHART SCRIPT
===================================================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('cashflowTrend'), {
    type: 'line',
    data: {
        labels: {!! json_encode($months) !!},
        datasets: [
            {
                label: 'Cash In',
                data: {!! json_encode($trendIn) !!},
                borderColor: '#0A6847',
                borderWidth: 2.8,
                tension: 0.25
            },
            {
                label: 'Cash Out',
                data: {!! json_encode($trendOut) !!},
                borderColor: '#C1121F',
                borderWidth: 2.8,
                tension: 0.25
            }
        ]
    },
    options: {
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
@endsection
