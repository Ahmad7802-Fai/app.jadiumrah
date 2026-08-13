@extends('layouts.admin')

@section('title', 'Trip Profit')

@section('content')
<div class="page-container page-wide">

    {{-- ================= PAGE HEADER ================= --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Trip Profit</h1>
            <p class="page-subtitle">Analisis laba per keberangkatan</p>
        </div>

        <form method="GET" class="d-flex gap-2 align-items-center">
            <select name="paket_id" class="form-select form-select-sm">
                <option value="">Semua Paket</option>
                @foreach($paketList as $p)
                    <option value="{{ $p->id }}" {{ $paketId == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_paket }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
        </form>
    </div>

    {{-- ================= CHART ================= --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-semibold mb-3">Revenue vs Trip Cost</h6>

            {{-- wrapper biar tinggi terkunci --}}
            <div style="height:260px">
                <canvas id="tripProfitChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card card-hover">
        <div class="card-body p-0">

            <div class="table-wrapper">
                <table class="table table-compact align-middle">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Kode</th>
                            <th>Paket</th>
                            <th>Tanggal</th>
                            <th class="text-end">Jamaah</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">Trip Cost</th>
                            <th class="text-end">Profit</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($keberangkatan as $i => $row)
                        <tr>
                            <td>
                                {{ method_exists($keberangkatan,'firstItem')
                                    ? $keberangkatan->firstItem() + $i
                                    : $i + 1 }}
                            </td>

                            <td class="fw-semibold">{{ $row['kode'] }}</td>

                            <td>{{ $row['paket'] ?? '-' }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($row['tanggal'])->format('d M Y') }}
                            </td>

                            <td class="text-end">{{ number_format($row['jamaah']) }}</td>

                            <td class="text-end fw-semibold">
                                Rp {{ number_format($row['revenue'],0,',','.') }}
                            </td>

                            {{-- TRIP COST CLICKABLE --}}
                            <td class="text-end">
                                @if($row['trip_cost'] > 0)
                                    <a href="{{ route(
                                        'keuangan.trip.expenses.by-keberangkatan',
                                        [$paketId, $row['id']]
                                    ) }}"
                                       class="fw-semibold text-danger text-decoration-none d-inline-flex align-items-center gap-1">
                                        <i class="fas fa-receipt"></i>
                                        Rp {{ number_format($row['trip_cost'],0,',','.') }}
                                    </a>
                                @else
                                    <span class="text-muted">Rp 0</span>
                                @endif
                            </td>

                            <td class="text-end fw-bold {{ $row['profit'] >= 0 ? 'text-success':'text-danger' }}">
                                Rp {{ number_format($row['profit'],0,',','.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Tidak ada data keberangkatan.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        @if(method_exists($keberangkatan,'links'))
            <div class="card-footer">
                {{ $keberangkatan->links() }}
            </div>
        @endif
    </div>

</div>
@endsection


{{-- ================= CHART DATA ================= --}}
@php
    $chartData = $keberangkatan->map(fn ($r) => [
        'id'      => $r['id'],
        'label'   => $r['kode'],
        'revenue' => $r['revenue'],
        'cost'    => $r['trip_cost'],
    ])->values();
@endphp

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const tripData = @json($chartData);
const ctx = document.getElementById('tripProfitChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: tripData.map(i => i.label),
        datasets: [
            {
                label: 'Revenue',
                data: tripData.map(i => i.revenue),
                backgroundColor: '#22c55e',
                borderRadius: 6,
                barThickness: 18
            },
            {
                label: 'Trip Cost',
                data: tripData.map(i => i.cost),
                backgroundColor: '#ef4444',
                borderRadius: 6,
                barThickness: 18
            }
        ]
    },
    options: {
        indexAxis: 'y', // ✅ horizontal
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx =>
                        ctx.dataset.label + ': Rp ' +
                        ctx.raw.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    callback: v => 'Rp ' + v.toLocaleString('id-ID')
                }
            }
        },

        // 🔥 CLICK BAR → DETAIL BIAYA
        onClick: (evt, elements) => {
            if (!elements.length) return;
            const index = elements[0].index;
            const tripId = tripData[index].id;

            window.location.href =
                `/keuangan/trip/{{ $paketId }}/expenses/keberangkatan/${tripId}`;
        }
    }
});
</script>
@endpush
