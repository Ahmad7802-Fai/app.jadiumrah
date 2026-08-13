<div class="card-premium mb-4">

    <form method="GET"
          class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">

        {{-- SEARCH --}}
        <div class="md:col-span-5">
            <label class="form-label">Cari Lead</label>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Nama / No HP / Email"
                class="form-input"
            >
        </div>

        {{-- STATUS --}}
        <div class="md:col-span-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">Semua Status</option>
                <option value="NEW" @selected(request('status')==='NEW')>NEW</option>
                <option value="ACTIVE" @selected(request('status')==='ACTIVE')>ACTIVE</option>
                <option value="CLOSED" @selected(request('status')==='CLOSED')>CLOSED</option>
            </select>
        </div>

        {{-- FILTER BUTTON --}}
        <div class="md:col-span-2">
            <button class="btn-ju btn-w-100">
                🔍 Filter
            </button>
        </div>

        {{-- RESET --}}
        <div class="md:col-span-2">
            <a href="{{ route('crm.leads.index') }}"
               class="btn-gray-soft btn-w-100 text-center">
                Reset
            </a>
        </div>

    </form>

</div>
