{{-- dashboard/partials/chart.blade.php --}}
<div class="card mt-4">
    <div class="card-header">
        <h4 class="card-title">Ringkasan Bulanan</h4>
    </div>
    <div class="card-body">
        <canvas id="dashboardChart" height="120"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('dashboardChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chart['labels']),
            datasets: [
                {
                    label: 'Bulan Ini',
                    data: @json($chart['thisMonth']),
                },
                {
                    label: 'Bulan Lalu',
                    data: @json($chart['lastMonth']),
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
