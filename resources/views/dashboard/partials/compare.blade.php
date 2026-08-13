{{-- dashboard/partials/compare.blade.php --}}
<canvas id="compareChart"></canvas>

<script>
new Chart(document.getElementById('compareChart'), {
    type: 'line',
    data: {
        labels: @json($compare['labels']),
        datasets: [
            { label: 'This Month', data: @json($compare['thisMonth']) },
            { label: 'Last Month', data: @json($compare['lastMonth']) }
        ]
    }
});
</script>
