@extends('layouts.admin')

@section('title', 'Dashboard Sales')

@section('content')
<div class="container-fluid py-3">

    {{-- ================= HEADER ================= --}}
    <h4 class="fw-bold text-ju-green mb-4">Dashboard Sales</h4>

    {{-- ================= SUMMARY CARDS ================= --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <div class="fw-semibold text-muted small">Total Leads</div>
                <div class="fs-3 fw-bold text-ju-green">{{ $totalLeads }}</div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <div class="fw-semibold text-muted small">Follow Up Hari Ini</div>
                <div class="fs-3 fw-bold">{{ $followUpDueToday }}</div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <div class="fw-semibold text-muted small">Follow Up Terlambat</div>
                <div class="fs-3 fw-bold text-danger">{{ $followUpOverdue }}</div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <div class="fw-semibold text-muted small">Total Closing</div>
                <div class="fs-4 fw-bold text-primary">
                    Rp {{ number_format($closingTotal,0,',','.') }}
                </div>
            </div>
        </div>

    </div>

    {{-- ================= PIPELINE ================= --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 p-3 h-100">
                <h6 class="fw-bold mb-3">Distribusi Pipeline</h6>
                <canvas id="pipelineChart" height="120"></canvas>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 p-3 h-100">
                <h6 class="fw-bold mb-3">Top 5 Stages</h6>

                <ul class="list-unstyled m-0">
                    @foreach ($pipelineChart->sortByDesc('total')->take(5) as $row)
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="fw-semibold text-capitalize">
                                {{ $row['label'] }}
                            </span>
                            <span class="badge bg-ju-green px-3 py-2">
                                {{ $row['total'] }}
                            </span>
                        </li>
                    @endforeach
                </ul>

            </div>
        </div>

    </div>

    {{-- ================= MONTHLY CLOSING ================= --}}
    <div class="card shadow-sm border-0 rounded-4 p-3 mb-5">
        <h6 class="fw-bold mb-3">Closing Per Bulan</h6>
        <canvas id="closingChart" height="130"></canvas>
    </div>

</div>

{{-- ================= DATA PREP ================= --}}
@php
$monthMap = [
    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
    5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
    9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
];

$closingLabels = [];
$closingValues = [];

foreach ($monthlyClosing as $row) {
    $closingLabels[] = $monthMap[$row->month] ?? $row->month;
    $closingValues[] = (float) $row->total;
}
@endphp
{{-- ================= RECENT CLOSING ================= --}}
<div class="card shadow-sm border-0 rounded-4 p-3 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Recent Closing</h6>
        <span class="text-muted small">Klik baris untuk detail</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="text-muted small">
                <tr>
                    <th>Lead</th>
                    <th>Agent</th>
                    <th class="text-end">Nominal DP</th>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($recentClosings as $c)
                    <tr
                        class="closing-row"
                        onclick="window.location='{{ route('crm.closing.show', $c->id) }}'"
                        style="cursor:pointer"
                    >
                        {{-- LEAD --}}
                        <td>
                            <div class="fw-semibold">
                                {{ $c->lead->nama ?? '-' }}
                            </div>
                            <div class="text-muted small">
                                #{{ $c->lead_id }}
                            </div>
                        </td>

                        {{-- AGENT --}}
                        <td>
                            {{ optional($c->agent)->nama ?? '-' }}
                        </td>

                        {{-- NOMINAL --}}
                        <td class="text-end fw-semibold text-ju-green">
                            Rp {{ number_format($c->nominal_dp,0,',','.') }}
                        </td>

                        {{-- TANGGAL --}}
                        <td class="text-center text-muted">
                            {{ optional($c->closed_at)->format('d M Y') }}
                        </td>

                        {{-- STATUS --}}
                        <td class="text-center">
                            <span class="badge bg-success px-3 py-2">
                                APPROVED
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Belum ada closing
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ================= ROW HOVER STYLE ================= --}}
<style>
    .closing-row:hover {
        background-color: #f9fafb;
    }
</style>

{{-- ================= CHARTJS ================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* ================= PIPELINE CHART ================= */
new Chart(document.getElementById('pipelineChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($pipelineChart->pluck('label')) !!},
        datasets: [{
            data: {!! json_encode($pipelineChart->pluck('total')) !!},
            backgroundColor: [
                '#d1fae5', '#bbf7d0', '#86efac',
                '#4ade80', '#22c55e', '#16a34a', '#9ca3af'
            ],
            borderRadius: 6
        }]
    },
    options: {
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.parsed.y + ' Leads'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

/* ================= MONTHLY CLOSING ================= */
new Chart(document.getElementById('closingChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($closingLabels) !!},
        datasets: [{
            data: {!! json_encode($closingValues) !!},
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22,163,74,0.15)',
            fill: true,
            borderWidth: 2,
            tension: 0.4,
            pointRadius: 4
        }]
    },
    options: {
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx =>
                        'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value =>
                        'Rp ' + value.toLocaleString('id-ID')
                }
            }
        }
    }
});
</script>
@endsection
