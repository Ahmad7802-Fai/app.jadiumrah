{{-- dashboard/partials/header.blade.php --}}
<div class="dashboard-header">
    <h2>{{ $title ?? 'Dashboard' }}</h2>

    <form method="GET" class="filter-row">
        <input type="month"
               name="periode"
               value="{{ sprintf('%04d-%02d', $year, $month) }}">
        <button class="btn btn-primary">Filter</button>
    </form>
</div>
