@extends('layouts.admin')

@section('content')

<div class="dashboard-container">

    {{-- ================= HEADER ================= --}}
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title">Dashboard Keuangan</h1>
            <p class="page-subtitle">
                Ringkasan pendapatan & pengeluaran keseluruhan
            </p>
        </div>

        <div class="page-actions">

            {{-- FILTER --}}
            <form method="GET" class="d-flex gap-2">
                <input type="month"
                       name="periode"
                       value="{{ $periode }}"
                       class="form-control">

                <button class="btn btn-primary">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
            </form>

            {{-- EXPORT --}}
            <a href="{{ route('keuangan.laporan.pnl.excel', request()->query()) }}"
               class="btn btn-success">
                <i class="fas fa-file-excel"></i>
                Excel PNL
            </a>

            <a href="{{ route('keuangan.laporan.cashflow.pdf', request()->query()) }}"
               class="btn btn-danger">
                <i class="fas fa-file-pdf"></i>
                PDF Cashflow
            </a>
        </div>
    </div>

    {{-- ================= SUMMARY ================= --}}
    <div class="stat-grid mb-4">

        <div class="card card-stat card-stat-success card-hover">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Pendapatan Jamaah</div>
                <div class="stat-value">
                    Rp {{ number_format($pendapatanJamaah, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="card card-stat card-stat-success card-hover">
            <div class="stat-icon">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Pendapatan Layanan</div>
                <div class="stat-value">
                    Rp {{ number_format($pendapatanLayanan, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="card card-stat card-stat-danger card-hover">
            <div class="stat-icon">
                <i class="fas fa-sack-dollar"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Pengeluaran</div>
                <div class="stat-value">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="card card-stat {{ $labaBersih >= 0 ? 'card-stat-success' : 'card-stat-danger' }} card-hover">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Laba Bersih</div>
                <div class="stat-value">
                    Rp {{ number_format($labaBersih, 0, ',', '.') }}
                </div>
            </div>
        </div>

    </div>

    {{-- ================= EXPENSE BREAKDOWN ================= --}}
    <div class="stat-grid mb-4">

        <div class="card card-stat card-stat-danger">
            <div class="stat-icon">
                <i class="fas fa-plane-departure"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Biaya Trip</div>
                <div class="stat-value">
                    Rp {{ number_format($biayaTrip, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="card card-stat card-stat-danger">
            <div class="stat-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Operasional</div>
                <div class="stat-value">
                    Rp {{ number_format($biayaOperasional, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="card card-stat card-stat-danger">
            <div class="stat-icon">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Marketing</div>
                <div class="stat-value">
                    Rp {{ number_format($biayaMarketing, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="card card-stat card-stat-danger">
            <div class="stat-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Vendor</div>
                <div class="stat-value">
                    Rp {{ number_format($biayaVendor, 0, ',', '.') }}
                </div>
            </div>
        </div>

    </div>

    {{-- ================= SHORTCUT ================= --}}
    <div class="dashboard-cards mb-4">

        <a href="{{ route('keuangan.laporan.trip-profit') }}" class="card card-hover">
            <div class="card-body">
                <strong>Trip Profit</strong>
                <div class="text-muted small">Per keberangkatan</div>
            </div>
        </a>

        <a href="{{ route('keuangan.laporan.pnl') }}" class="card card-hover">
            <div class="card-body">
                <strong>Laba Rugi</strong>
                <div class="text-muted small">PNL per periode</div>
            </div>
        </a>

        <a href="{{ route('keuangan.laporan.cashflow') }}" class="card card-hover">
            <div class="card-body">
                <strong>Cashflow</strong>
                <div class="text-muted small">Arus kas masuk & keluar</div>
            </div>
        </a>

    </div>

    {{-- ================= TREND ================= --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Trend 6 Bulan</h3>
        </div>
        <div class="card-body">
            <div class="chart-wrapper">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('trendChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($months),
        datasets: [
            {
                label: 'Pendapatan',
                data: @json($trendRevenue),
                borderWidth: 3,
                tension: 0.3
            },
            {
                label: 'Pengeluaran',
                data: @json($trendExpense),
                borderWidth: 3,
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
@endpush
