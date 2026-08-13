@extends('layouts.cabang')

@section('content')
<div class="cabang-dashboard">

    {{-- ===============================
       PAGE HEADER
    =============================== --}}
    <div class="page-header mb-12">
        <div>
            <h1 class="page-title">
                {{ $cards['title'] ?? 'Dashboard Cabang' }}
            </h1>
            <p class="text-muted text-sm">
                Ringkasan data jamaah & aktivitas cabang
            </p>
        </div>
    </div>

    {{-- ===============================
       STATS GRID
    =============================== --}}
    <div class="c-dashboard-grid">
        @forelse($cards['cards'] ?? [] as $card)
            <div class="c-card dense {{ $card['variant'] ?? '' }}">

                <div class="c-card__sub">
                    @if(!empty($card['icon']))
                        <i class="fas {{ $card['icon'] }} me-1 text-muted"></i>
                    @endif
                    {{ $card['label'] ?? '-' }}
                </div>

                <div class="c-card__value">
                    {{ $card['display'] ?? number_format($card['value'] ?? 0) }}
                </div>

            </div>
        @empty
            <div class="text-muted text-sm">
                Tidak ada data statistik
            </div>
        @endforelse
    </div>

    {{-- ===============================
       STATUS PEMBAYARAN
    =============================== --}}
    <div class="c-card has-header-bg header-primary mt-16">
        <div class="c-card__header">
            Status Pembayaran
        </div>

        <div class="c-card__body">
            <div class="chart-box chart-box--sm">
                <canvas
                    id="statusChart"
                    data-lunas="{{ (int) ($statusChart['lunas'] ?? 0) }}"
                    data-belum="{{ (int) ($statusChart['belum'] ?? 0) }}">
                </canvas>
            </div>
        </div>
    </div>

    {{-- ===============================
       JAMAAH PER AGENT
    =============================== --}}
    <div class="c-card has-header-bg mt-16">
        <div class="c-card__header">
            Jamaah per Agent
        </div>

        <div class="c-card__body" style="flex-direction: column; gap: 12px">

            {{-- CHART --}}
            <div class="chart-box chart-box--md">
                <canvas
                    id="agentChart"
                    data-agents='@json($agentsChart ?? [])'>
                </canvas>
            </div>

            {{-- TABLE --}}
            <div class="cabang-table-wrap">
                <table class="cabang-table is-dense">
                    <tbody>
                        @forelse($agentsChart ?? [] as $row)
                            <tr>
                                <td>{{ $row['name'] ?? '-' }}</td>
                                <td class="text-right fw-semibold">
                                    {{ (int) ($row['total'] ?? 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-muted text-center">
                                    Belum ada data agent
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

  /* =========================
     STATUS PEMBAYARAN
  ========================== */
  const statusEl = document.getElementById('statusChart')
  if (statusEl) {

    if (window.statusChartInstance) {
      window.statusChartInstance.destroy()
    }

    const lunas = Number(statusEl.dataset.lunas || 0)
    const belum = Number(statusEl.dataset.belum || 0)

    window.statusChartInstance = new Chart(statusEl, {
      type: 'doughnut',
      data: {
        labels: ['Lunas', 'Belum Lunas'],
        datasets: [{
          data: [lunas, belum],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    })
  }

  /* =========================
     JAMAAH PER AGENT
  ========================== */
  const agentEl = document.getElementById('agentChart')
  if (agentEl) {

    if (window.agentChartInstance) {
      window.agentChartInstance.destroy()
    }

    const agents = JSON.parse(agentEl.dataset.agents || '[]')

    window.agentChartInstance = new Chart(agentEl, {
      type: 'bar',
      data: {
        labels: agents.map(a => a.name),
        datasets: [{
          data: agents.map(a => a.total),
          borderRadius: 8,
          barThickness: 26,
          maxBarThickness: 32
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: {
            grid: { display: false }
          },
          y: {
            beginAtZero: true,
            ticks: { precision: 0 }
          }
        }
      }
    })
  }

})
</script>
@endpush
