@extends('layouts.admin')

@section('title', 'Edit Flight Routes')

@section('content')

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="page-header mb-md">
    <div>
        <div class="page-title">
            Edit Flight Routes
        </div>
        <div class="text-xs text-muted">
            PNR {{ $pnr->pnr_code }}
        </div>
    </div>
</div>

<form method="POST"
      action="{{ route('ticketing.pnr.routes.update', $pnr) }}">
    @csrf
    @method('PUT')

    {{-- ======================================================
    | ROUTE EDITOR (CENTERED)
    ====================================================== --}}
    <div class="route-editor-wrap">

        <div id="routes" class="route-grid mb-md">

            @foreach($pnr->routes as $i => $route)
                <div class="card route-card">
                    <div class="card-body-sm">

                        <input type="hidden"
                               name="routes[{{ $i }}][id]"
                               value="{{ $route->id }}">

                        <div class="grid-1 gap-xs mb-sm">
                            <label class="form-label">Origin (Airport)</label>
                            <input type="text"
                                   name="routes[{{ $i }}][origin]"
                                   value="{{ $route->origin }}"
                                   class="form-input uppercase"
                                   placeholder="CGK - JED"
                                   required>
                        </div>

                        <div class="grid-2 gap-sm mb-sm">
                            <div>
                                <label class="form-label">Departure Date</label>
                                <input type="date"
                                       name="routes[{{ $i }}][departure_date]"
                                       value="{{ $route->departure_date->format('Y-m-d') }}"
                                       class="form-input"
                                       required>
                            </div>

                            <div>
                                <label class="form-label">Flight No</label>
                                <input type="text"
                                       name="routes[{{ $i }}][flight_number]"
                                       value="{{ $route->flight_number }}"
                                       class="form-input"
                                       placeholder="SV 818">
                            </div>
                        </div>

                        <button type="button"
                                class="btn btn-light btn-xs text-danger"
                                onclick="removeRoute(this)">
                            ✕ Remove
                        </button>

                    </div>
                </div>
            @endforeach

        </div>

        <button type="button"
                class="btn btn-light btn-sm"
                onclick="addRoute()">
            + Add Sector
        </button>

    </div>

    {{-- ======================================================
    | ACTIONS
    ====================================================== --}}
    <div class="route-editor-wrap mt-md">
        <div class="row-between border-top pt-sm">
            <a href="{{ route('ticketing.pnr.show', $pnr) }}"
               class="btn btn-light">
                Cancel
            </a>

            <button class="btn btn-primary">
                Save Routes
            </button>
        </div>
    </div>

</form>
@endsection

@push('scripts')
<script>
let routeIndex = {{ $pnr->routes->count() }};

function addRoute() {
    const html = `
        <div class="card route-card">
            <div class="card-body-sm">

                <div class="grid-1 gap-xs mb-sm">
                    <label class="form-label">Origin (Airport)</label>
                    <input type="text"
                           name="routes[${routeIndex}][origin]"
                           class="form-input uppercase"
                           placeholder="CGK - JED"
                           required>
                </div>

                <div class="grid-2 gap-sm mb-sm">
                    <div>
                        <label class="form-label">Departure Date</label>
                        <input type="date"
                               name="routes[${routeIndex}][departure_date]"
                               class="form-input"
                               required>
                    </div>

                    <div>
                        <label class="form-label">Flight No</label>
                        <input type="text"
                               name="routes[${routeIndex}][flight_number]"
                               class="form-input"
                               placeholder="SV 818">
                    </div>
                </div>

                <button type="button"
                        class="btn btn-light btn-xs text-danger"
                        onclick="removeRoute(this)">
                    ✕ Remove
                </button>

            </div>
        </div>
    `;

    document.getElementById('routes')
        .insertAdjacentHTML('beforeend', html);

    routeIndex++;
}

function removeRoute(btn) {
    btn.closest('.route-card').remove();
}
</script>
@endpush
@push('scripts')
<script>
let routeIndex = {{ $pnr->routes->count() }};

function addRoute() {
    const html = `
        <div class="card route-card">
            <div class="card-body-sm">

                <div class="grid-1 gap-xs mb-sm">
                    <label class="form-label">Origin (Airport)</label>
                    <input type="text"
                           name="routes[${routeIndex}][origin]"
                           class="form-input uppercase"
                           placeholder="CGK - JED"
                           required>
                </div>

                <div class="grid-2 gap-sm mb-sm">
                    <div>
                        <label class="form-label">Departure Date</label>
                        <input type="date"
                               name="routes[${routeIndex}][departure_date]"
                               class="form-input"
                               required>
                    </div>

                    <div>
                        <label class="form-label">Flight No</label>
                        <input type="text"
                               name="routes[${routeIndex}][flight_number]"
                               class="form-input"
                               placeholder="SV 818">
                    </div>
                </div>

                <button type="button"
                        class="btn btn-light btn-xs text-danger"
                        onclick="removeRoute(this)">
                    ✕ Remove
                </button>

            </div>
        </div>
    `;

    document.getElementById('routes')
        .insertAdjacentHTML('beforeend', html);

    routeIndex++;
}

function removeRoute(btn) {
    btn.closest('.route-card').remove();
}
</script>
@endpush
