<form method="GET" class="dashboard-filter">
    <div class="filter-row">

        <div class="filter-col">
            <label>Periode</label>
            <input
                type="month"
                name="month"
                value="{{ sprintf('%04d-%02d', $year, $month) }}"
                class="form-control"
            >
        </div>

        <div class="filter-actions">
            <div class="btn-group">
                <button class="btn btn-primary">
                    Filter
                </button>
            </div>
        </div>

    </div>
</form>
