<form method="GET" class="agent-filter">

    <div class="agent-filter-row">

        <div class="agent-filter-item">
            <label>Cari Lead</label>
            <input type="text"
                   name="q"
                   value="{{ request('q') }}"
                   placeholder="Nama / No HP / Email">
        </div>

        <div class="agent-filter-item">
            <label>Status</label>
            <select name="status">
                <option value="">Semua Status</option>
                <option value="NEW" @selected(request('status')==='NEW')>NEW</option>
                <option value="ACTIVE" @selected(request('status')==='ACTIVE')>ACTIVE</option>
                <option value="CLOSING" @selected(request('status')==='CLOSING')>CLOSING</option>
                <option value="CLOSED" @selected(request('status')==='CLOSED')>CLOSED</option>
            </select>
        </div>

        <div class="agent-filter-action">
            <button class="btn-primary btn-sm">
                Filter
            </button>

            <a href="{{ route('agent.leads.index') }}"
               class="btn-link">
                Reset
            </a>
        </div>

    </div>
</form>
