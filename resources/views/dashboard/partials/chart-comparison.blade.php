{{-- dashboard/partials/chart-comparison.blade.php --}}
<div class="card mt-4">
    <div class="card-header">
        <h4 class="card-title">Perbandingan Bulan Ini vs Bulan Lalu</h4>
    </div>

    <div class="card-body">
        <div class="chart-wrapper">
            <canvas id="comparisonChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('comparisonChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartComparison['labels']),
            datasets: [
                {
                    label: 'Bulan Ini',
                    data: @json($chartComparison['thisMonth']),
                    borderColor: '#4f8cff',
                    backgroundColor: 'rgba(79,140,255,.15)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                },
                {
                    label: 'Bulan Lalu',
                    data: @json($chartComparison['lastMonth']),
                    borderColor: '#ff6b6b',
                    backgroundColor: 'rgba(255,107,107,.15)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // 🔥 KUNCI UTAMA
            resizeDelay: 100,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw || 0;
                            return context.dataset.label + ': Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => 'Rp ' + value.toLocaleString('id-ID')
                    }
                }
            }
        }
    });
});
</script>
