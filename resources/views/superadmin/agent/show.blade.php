@extends('layouts.admin')

@section('title', 'Detail Agent')

@section('content')
<div class="page-container">

    {{-- =====================================================
       PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $agent->user->nama }}</h1>
            <p class="page-subtitle">
                {{ $agent->user->email }}
                · {{ $agent->user->branch->nama_cabang ?? '-' }}
            </p>
        </div>

        {{-- FILTER --}}
        <form method="GET" class="d-flex gap-2">
            <select name="month" class="form-select" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected($month == $m)>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>

            <select name="year" class="form-select" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" @selected($year == $y)>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </form>
    </div>

    {{-- =====================================================
       SUMMARY CARDS
    ====================================================== --}}
    <div class="row g-3 mb-4">

        <div class="col-md-6">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="text-muted mb-1">Jamaah Bulan Ini</div>
                    <h3 class="mb-0">{{ $jamaahBulanan }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="text-muted mb-1">Omset Bulan Ini</div>
                    <h3 class="mb-0">
                        Rp {{ number_format($omsetBulanan, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- =====================================================
       CHART OMSET PER BULAN
    ====================================================== --}}
    <div class="card card-hover">
        <div class="card-header">
            Omset Agent per Bulan ({{ $year }})
        </div>

        <div class="card-body">
            <canvas id="omsetChart" height="90"></canvas>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('omsetChart')

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [
            'Jan','Feb','Mar','Apr','Mei','Jun',
            'Jul','Agu','Sep','Okt','Nov','Des'
        ],
        datasets: [{
            label: 'Omset (Rp)',
            data: @json(collect($chartData)->pluck('omset')),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID')
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        return 'Rp ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            }
        }
    }
})
</script>
@endpush
