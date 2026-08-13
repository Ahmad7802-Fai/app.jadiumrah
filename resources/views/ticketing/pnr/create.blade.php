@extends('layouts.admin')

@section('title', 'Create PNR')

@section('content')

{{-- ===============================
| PAGE HEADER
=============================== --}}
<div class="page-header mb-3 d-flex justify-between items-center">
    <div>
        <h1 class="page-title">Create PNR</h1>
        <p class="page-subtitle">Input data PNR ticketing</p>
    </div>

    <div>
        <a href="{{ route('ticketing.pnr.index') }}"
           class="btn btn-outline-secondary btn-sm">
            ← Back
        </a>
    </div>
</div>

<form method="POST" action="{{ route('ticketing.pnr.store') }}">
@csrf

{{-- ===============================
| PNR INFORMATION
=============================== --}}
<div class="card card-hover mb-3">
    <div class="card-header">PNR Information</div>

    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

            <div>
                <label class="form-label">PNR Code *</label>
                <input type="text"
                       name="pnr_code"
                       class="form-control text-uppercase"
                       required>
            </div>

            <div>
                <label class="form-label">Airline *</label>
                <select name="airline_code"
                        id="airline_select"
                        class="form-select"
                        required>
                    <option value="">-- pilih airline --</option>
                    @foreach($airlines as $airline)
                        <option value="{{ $airline->code }}"
                                data-name="{{ $airline->name }}">
                            {{ $airline->code }} — {{ $airline->name }}
                        </option>
                    @endforeach
                </select>

                <input type="hidden" name="airline_name" id="airline_name">

                <button type="button"
                        class="btn btn-link btn-xs mt-1"
                        onclick="openAddAirline()">
                    + Airline belum ada?
                </button>
            </div>

            <div>
                <label class="form-label">Class</label>
                <select name="airline_class" class="form-select">
                    <option value="ECONOMY">Economy</option>
                    <option value="BUSINESS">Business</option>
                    <option value="FIRST">First</option>
                </select>
            </div>

            <div>
                <label class="form-label">Client *</label>
                <select name="client_id" class="form-select" required>
                    <option value="">-- pilih client --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">
                            {{ $client->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">-- optional --</option>
                    <option value="ticketing">Ticketing</option>
                    <option value="umroh">Umroh</option>
                    <option value="group">Group</option>
                </select>
            </div>

        </div>
    </div>
</div>

{{-- ===============================
| PRICING
=============================== --}}
<div class="card card-hover mb-3">
    <div class="card-header">Pricing</div>

    <div class="card-body">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="form-label">Pax *</label>
                <input type="number"
                       id="pax"
                       name="pax"
                       class="form-control"
                       value="1" min="1">
            </div>

            <div>
                <label class="form-label">Fare / Pax *</label>
                <input type="number"
                       id="fare"
                       name="fare_per_pax"
                       class="form-control"
                       value="0">
            </div>

            <div>
                <label class="form-label">Balance</label>
                <input type="text"
                       id="balance"
                       class="form-control bg-gray-100"
                       readonly>
            </div>
        </div>
    </div>
</div>

{{-- ===============================
| FLIGHT ROUTES
=============================== --}}
<div class="card card-hover mb-3">
    <div class="card-header">
        <span>✈ Flight Routes</span>

        <button type="button"
                id="btn-add-route"
                class="btn btn-outline btn-sm">
            + Add Sector
        </button>
    </div>

    <div class="card-body">

        <div class="route-editor-wrap">

            <div id="routes-wrapper" class="route-grid">

                {{-- SECTOR 1 --}}
                <div class="route-card">
                    <div class="card-body-sm">

                        <div class="d-flex justify-between items-center mb-2">
                            <div class="fw-semibold sector-title">
                                Sector 1
                            </div>
                        </div>

                        <div class="space-y-2">

                            <div>
                                <label class="form-label">Origin (Airport)</label>
                                <input type="text"
                                       name="routes[0][origin]"
                                       class="form-control text-uppercase"
                                       placeholder="CGK - JED"
                                       required>
                            </div>

                            <div>
                                <label class="form-label">Departure Date</label>
                                <input type="date"
                                       name="routes[0][departure_date]"
                                       class="form-control"
                                       required>
                            </div>

                            <div>
                                <label class="form-label">Flight No</label>
                                <input type="text"
                                       name="routes[0][flight_number]"
                                       class="form-control"
                                       placeholder="SV 818">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="form-label">Depart</label>
                                    <input type="time"
                                           name="routes[0][departure_time]"
                                           class="form-control">
                                </div>

                                <div>
                                    <label class="form-label">Arrive</label>
                                    <input type="time"
                                           name="routes[0][arrival_time]"
                                           class="form-control">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Arrival Offset</label>
                                <select name="routes[0][arrival_day_offset]"
                                        class="form-select">
                                    <option value="0">Same Day</option>
                                    <option value="1">+1 Day</option>
                                </select>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

{{-- ===============================
| ACTION
=============================== --}}
<div class="d-flex justify-end gap-2">
    <a href="{{ route('ticketing.pnr.index') }}"
       class="btn btn-outline-secondary">
        Cancel
    </a>
    <button class="btn btn-primary">
        Save PNR
    </button>
</div>

</form>

{{-- ===============================
| ADD AIRLINE MODAL
=============================== --}}
<div class="modal fade" id="addAirlineModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Airline</h5>
      </div>
      <div class="modal-body space-y-2">
        <div>
            <label class="form-label">Kode Airline</label>
            <input type="text"
                   id="new_airline_code"
                   class="form-control text-uppercase">
        </div>
        <div>
            <label class="form-label">Nama Airline</label>
            <input type="text"
                   id="new_airline_name"
                   class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary"
                data-bs-dismiss="modal">
            Cancel
        </button>
        <button class="btn btn-primary"
                onclick="useNewAirline()">
            Gunakan
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
/* ======================================================
 | PRICING
 ====================================================== */
function calculatePricing() {
    const pax  = Number(document.getElementById('pax')?.value || 0);
    const fare = Number(document.getElementById('fare')?.value || 0);
    const balance = document.getElementById('balance');

    if (!balance) return;

    balance.value = (pax * fare).toLocaleString('id-ID');
}

/* ======================================================
 | FLIGHT ROUTES (MATCH EDIT ROUTES + SCSS)
 ====================================================== */
const routesWrapper = document.getElementById('routes-wrapper');
const addRouteBtn   = document.getElementById('btn-add-route');

let routeIndex = routesWrapper
    ? routesWrapper.querySelectorAll('.route-card').length
    : 0;

/**
 * Build route card
 */
function buildRouteCard(index) {
    return `
    <div class="route-card route-item">
        <div class="card-body-sm">

            <div class="d-flex justify-between items-center mb-2">
                <div class="fw-semibold sector-title">
                    Sector ${index + 1}
                </div>

                ${index > 0 ? `
                <button type="button"
                        class="btn btn-xs btn-outline-danger btn-remove-route">
                    ✕
                </button>` : ''}
            </div>

            <div class="space-y-2">

                <div>
                    <label class="form-label">Origin (Airport)</label>
                    <input type="text"
                           name="routes[${index}][origin]"
                           class="form-control text-uppercase"
                           placeholder="CGK - JED"
                           required>
                </div>

                <div>
                    <label class="form-label">Departure Date</label>
                    <input type="date"
                           name="routes[${index}][departure_date]"
                           class="form-control"
                           required>
                </div>

                <div>
                    <label class="form-label">Flight No</label>
                    <input type="text"
                           name="routes[${index}][flight_number]"
                           class="form-control"
                           placeholder="SV 818">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="form-label">Depart</label>
                        <input type="time"
                               name="routes[${index}][departure_time]"
                               class="form-control">
                    </div>

                    <div>
                        <label class="form-label">Arrive</label>
                        <input type="time"
                               name="routes[${index}][arrival_time]"
                               class="form-control">
                    </div>
                </div>

                <div>
                    <label class="form-label">Arrival Offset</label>
                    <select name="routes[${index}][arrival_day_offset]"
                            class="form-select">
                        <option value="0">Same Day</option>
                        <option value="1">+1 Day</option>
                    </select>
                </div>

            </div>
        </div>
    </div>
    `;
}

/**
 * Add sector
 */
addRouteBtn?.addEventListener('click', () => {
    if (!routesWrapper) return;

    routesWrapper.insertAdjacentHTML(
        'beforeend',
        buildRouteCard(routeIndex)
    );

    routeIndex++;
});

/**
 * Remove sector
 */
document.addEventListener('click', e => {
    if (!e.target.classList.contains('btn-remove-route')) return;

    const card = e.target.closest('.route-item');
    if (!card) return;

    card.remove();
    reindexRoutes();
});

/**
 * Reindex sector & input names
 */
function reindexRoutes() {
    if (!routesWrapper) return;

    const cards = routesWrapper.querySelectorAll('.route-item');

    cards.forEach((card, i) => {
        card.querySelector('.sector-title').innerText = `Sector ${i + 1}`;

        card.querySelectorAll('[name]').forEach(input => {
            input.name = input.name.replace(/routes\[\d+]/, `routes[${i}]`);
        });
    });

    routeIndex = cards.length;
}

/* ======================================================
 | AIRLINE
 ====================================================== */
function syncAirlineName() {
    const select = document.getElementById('airline_select');
    const hidden = document.getElementById('airline_name');

    if (!select || !hidden) return;

    hidden.value =
        select.options[select.selectedIndex]?.dataset.name || '';
}

function openAddAirline() {
    new bootstrap.Modal(
        document.getElementById('addAirlineModal')
    ).show();
}

function useNewAirline() {
    const code = new_airline_code.value.trim().toUpperCase();
    const name = new_airline_name.value.trim();

    if (!code || !name) {
        alert('Kode & Nama airline wajib diisi');
        return;
    }

    const opt = new Option(`${code} — ${name}`, code, true, true);
    opt.dataset.name = name;

    airline_select.append(opt);
    airline_name.value = name;

    bootstrap.Modal
        .getInstance(addAirlineModal)
        .hide();
}

/* ======================================================
 | INIT
 ====================================================== */
document.addEventListener('DOMContentLoaded', () => {
    ['pax', 'fare'].forEach(id =>
        document.getElementById(id)
            ?.addEventListener('input', calculatePricing)
    );

    calculatePricing();

    airline_select?.addEventListener('change', syncAirlineName);
    syncAirlineName();
});
</script>
@endpush
