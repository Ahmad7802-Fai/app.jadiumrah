{{-- resources/views/keuangan/dashboard.blade.php --}}
@extends('layouts.admin')

@section('content')

<style>
/* =============================================
   GLOBAL + CONTAINER
============================================= */
:root{
  --accent: #0d8150;
  --muted: #6b7280;
  --card-bg: #ffffff;
  --shadow: 0 8px 25px rgba(0,0,0,0.05);
}

.container-premium {
  max-width: 1400px;
  margin: 0 auto;
  padding: 24px;
}

/* =============================================
   PREMIUM CARDS
============================================= */
.cards-grid {
  display: grid;
  gap: 18px;
  grid-template-columns: repeat(auto-fit, minmax(260px,1fr));
}

/* Card wrapper */
.dashboard-card {
  position: relative;
  padding: 22px 22px 26px 22px;
  border-radius: 20px;
  background: var(--card-bg);
  border: 1px solid rgba(0,0,0,0.05);
  box-shadow: var(--shadow);
  transition: 0.25s ease;
  overflow: hidden;
}

.dashboard-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 14px 32px rgba(0,0,0,0.09);
}

/* Card text */
.title {
  font-size: 14px;
  font-weight: 600;
  color: var(--muted);
  max-width: 80%;
  line-height: 1.4;
  margin-bottom: 6px;
}

.value {
  font-size: 28px;
  font-weight: 800;
  color: var(--accent);
  line-height: 1.2;
}

/* Icon */
.icon-box {
  position: absolute;
  top: 14px;
  right: 14px;

  width: 46px;
  height: 46px;

  display: flex;
  align-items: center;
  justify-content: center;

  background: rgba(13,129,80,0.12);
  color: var(--accent);

  border-radius: 14px;
  border: 1px solid rgba(13,129,80,0.15);
  box-shadow: 0 6px 14px rgba(13,129,80,0.15);
  backdrop-filter: blur(6px);

  font-size: 19px;
}

/* Progress bar */
.progress {
  margin-top: 8px;
  height: 12px;
  border-radius: 10px;
  background: #eef2ee;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  border-radius: 10px;
}

/* Glow states */
.glow-red    { box-shadow: 0 0 18px rgba(255,0,0,0.16) !important; }
.glow-orange { box-shadow: 0 0 18px rgba(255,140,0,0.14) !important; }
.glow-yellow { box-shadow: 0 0 18px rgba(255,200,0,0.12) !important; }
.glow-green  { box-shadow: 0 0 18px rgba(0,200,120,0.12) !important; }

/* =============================================
   CHARTS
============================================= */
.chart-card {
  border-radius: 20px;
  padding: 24px;
  background: var(--card-bg);
  border: 1px solid rgba(0,0,0,0.05);
  box-shadow: var(--shadow);
}

.chart-container {
  width: 100%;
  min-height: 320px;
}

/* =============================================
   RESPONSIVE
============================================= */
@media(max-width: 576px){
  .dashboard-card {
    padding: 18px 16px 22px 16px;
  }

  .icon-box {
    width: 36px;
    height: 36px;
    font-size: 14px;
    border-radius: 10px;
    top: 10px;
    right: 10px;
  }

  .title { font-size: 12.5px; max-width: 70%; }
  .value { font-size: 22px; }
}
</style>


<div class="container-premium">

    {{-- =========================================
         HEADER
    ========================================== --}}
    {{-- <div class="mb-4">
        <h3 class="fw-bold mb-1">Dashboard Keuangan</h3>
        <div style="color:var(--muted); font-size:14px;">
            Ringkasan pembayaran, tagihan, dan perbandingan bulan.
        </div>
    </div> --}}


    {{-- =========================================
         PREMIUM CARDS
    ========================================== --}}
    <div class="cards-grid mb-4">
        @foreach($cards as $c)
            @php
                $glow = $c['glow'] ?? '';
                $isProgress = !empty($c['progress']);
                if ($isProgress) {
                    $v = $c['value'];
                    $progressColor =
                        $v <= 20 ? '#e63946' :
                        ($v <= 50 ? '#f4c542' :
                        ($v <= 80 ? '#1fa463' : '#0d8150'));
                }
            @endphp

            <div class="dashboard-card {{ $glow }}">
                <div class="icon-box">
                    <i class="fas {{ $c['icon'] }}"></i>
                </div>

                <div class="title">{{ $c['label'] }}</div>

                @if($isProgress)
                    <div style="font-weight:800;font-size:22px;color:{{ $progressColor }}">
                        {{ $c['value'] }}%
                    </div>
                    <div class="progress">
                        <div class="progress-bar"
                             style="width: {{ $c['value'] }}%; background: {{ $progressColor }};">
                        </div>
                    </div>
                @else
                    <div class="value" data-counter="{{ $c['value'] }}">
                        {{ number_format($c['value'],0,',','.') }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>


    {{-- =========================================
         CHARTS
    ========================================== --}}
    <div class="row g-4">

        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <h5 class="mb-3">Statistik Total</h5>
                <div id="chartTotal" class="chart-container"></div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <h5 class="mb-3">Distribusi Paket</h5>
                <div id="chartPie" class="chart-container"></div>
            </div>
        </div>

        <div class="col-12">
            <div class="chart-card">
                <h5 class="mb-3">Perbandingan Bulan Ini vs Bulan Lalu</h5>
                <div id="chartCompare" class="chart-container"></div>
            </div>
        </div>

    </div>

</div>

@endsection



{{-- =====================================================
     JAVASCRIPT (Charts + Counter)
===================================================== --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
(function(){
  'use strict';

  function isEl(id){ return document.querySelector(id) !== null; }
  function toNum(v){
    if (v === null || v === undefined || v === '') return 0;
    const n = Number(String(v).replace(/[^0-9\.\-]/g,'')); 
    return Number.isFinite(n) ? n : 0;
  }

  // Smooth counter animation
  document.querySelectorAll('[data-counter]').forEach(el=>{
    const target = toNum(el.dataset.counter);
    let cur = 0;
    function tick(){
      cur += (target - cur) * 0.12;
      if (Math.abs(target - cur) < 1) cur = target;
      el.textContent = new Intl.NumberFormat('id-ID').format(Math.floor(cur));
      if (cur !== target) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  });


  /* =============================
       API URLs
  ============================= */
  const CHART_URL   = "{{ route('keuangan.dashboard.chart') }}";
  const COMP_URL    = "{{ route('dashboard.chart-comparison') }}";


  /* =============================
       CHART 1 + PIE
  ============================= */
  fetch(CHART_URL)
    .then(r => r.json())
    .then(raw => {
      const labels = raw.labels || [];
      const values = (raw.values || []).map(toNum);

      const COLORS = ["#0d8150","#1fa463","#f4c542","#e63946"];

      // TOTAL BAR
      if (isEl('#chartTotal') && values.length){
        new ApexCharts(document.querySelector('#chartTotal'), {
          chart:{ type:'bar', height:320, toolbar:{ show:false } },
          series:[{ name:'Total', data:values }],
          xaxis:{ categories:labels },
          plotOptions:{ bar:{ borderRadius:8, columnWidth:'45%' }},
          colors: COLORS
        }).render();
      }

      // PIE
      if (isEl('#chartPie') && values.length){
        new ApexCharts(document.querySelector('#chartPie'), {
          chart:{ type:'pie', height:320 },
          series: values,
          labels: labels,
          colors: COLORS,
          legend:{ position:'right' }
        }).render();
      }
    });


  /* =============================
       CHART COMPARISON
  ============================= */
  fetch(COMP_URL)
    .then(r => r.json())
    .then(raw => {
      const labels    = raw.labels || [];
      const thisMonth = (raw.thisMonth || []).map(toNum);
      const lastMonth = (raw.lastMonth || []).map(toNum);

      if (!labels.length) return;

      new ApexCharts(document.querySelector('#chartCompare'), {
        chart:{ type:'bar', height:360, toolbar:{ show:false }},
        series:[
          { name:'Bulan Ini', data:thisMonth },
          { name:'Bulan Lalu', data:lastMonth }
        ],
        xaxis:{ categories:labels },
        plotOptions:{ bar:{ columnWidth:'40%' }},
        colors:["#0d8150","#f4c542"],
        legend:{ position:'top' }
      }).render();
    });

})();
</script>
@endpush
