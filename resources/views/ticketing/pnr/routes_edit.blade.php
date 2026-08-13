@extends('layouts.admin')

@section('title','Edit Routes PNR')

@section('content')

<div class="container-narrow">

    {{-- ======================================================
    | PAGE HEADER
    ====================================================== --}}
    <div class="page-header mb-md">
        <div>
            <div class="page-title">
                Edit Flight Routes
            </div>
            <div class="text-xs text-muted">
                PNR:
                <span class="mono fw-semibold text-uppercase">
                    {{ $pnr->pnr_code }}
                </span>
            </div>
        </div>

        <div class="page-actions">
            <a href="{{ route('ticketing.pnr.show', $pnr) }}"
               class="btn btn-secondary btn-sm">
                ← Back
            </a>
        </div>
    </div>

    {{-- ======================================================
    | FORM
    ====================================================== --}}
    <form method="POST"
          action="{{ route('ticketing.pnr.routes.update', $pnr) }}"
          class="stack stack-lg">
        @csrf
        @method('PUT')

        {{-- ROUTES --}}
        <div id="routes-wrapper" class="stack stack-md">

            @foreach($pnr->routes as $i => $route)
            <div class="card route-item">
                <div class="card-body">

                    {{-- ROUTE ID --}}
                    <input type="hidden"
                           name="routes[{{ $i }}][id]"
                           value="{{ $route->id }}">

                    {{-- HEADER --}}
                    <div class="row-between mb-sm">
                        <div class="fw-semibold sector-title">
                            Sector {{ $i + 1 }}
                        </div>

                        <button type="button"
                                class="btn btn-outline-danger btn-sm btn-remove-route">
                            Hapus
                        </button>
                    </div>

                    {{-- BODY --}}
                    <div class="grid-2 gap-md">

                        {{-- LEFT --}}
                        <div class="stack stack-sm">
                            <input type="text"
                                   name="routes[{{ $i }}][origin]"
                                   value="{{ $route->origin }}"
                                   class="form-input text-uppercase"
                                   placeholder="Origin (CGK)"
                                   required>

                            <input type="date"
                                   name="routes[{{ $i }}][departure_date]"
                                   value="{{ optional($route->departure_date)->format('Y-m-d') }}"
                                   class="form-input"
                                   required>

                            <input type="text"
                                   name="routes[{{ $i }}][flight_number]"
                                   value="{{ $route->flight_number }}"
                                   class="form-input"
                                   placeholder="Flight No (GA 980)">
                        </div>

                        {{-- RIGHT --}}
                        <div class="stack stack-sm">
                            <div class="grid-2 gap-sm">
                                <input type="time"
                                       name="routes[{{ $i }}][departure_time]"
                                       value="{{ $route->departure_time }}"
                                       class="form-input"
                                       title="Departure Time">

                                <input type="time"
                                       name="routes[{{ $i }}][arrival_time]"
                                       value="{{ $route->arrival_time }}"
                                       class="form-input"
                                       title="Arrival Time">
                            </div>

                            <select name="routes[{{ $i }}][arrival_day_offset]"
                                    class="form-select">
                                <option value="0" @selected($route->arrival_day_offset == 0)>
                                    Same Day
                                </option>
                                <option value="1" @selected($route->arrival_day_offset == 1)>
                                    +1 Day
                                </option>
                            </select>
                        </div>

                    </div>

                </div>
            </div>
            @endforeach

        </div>

        {{-- ADD ROUTE --}}
        <button type="button"
                id="btn-add-route"
                class="btn btn-outline btn-sm">
            + Tambah Sector
        </button>

        {{-- FOOTER --}}
        <div class="row justify-end gap-sm pt-md">
            <a href="{{ route('ticketing.pnr.show', $pnr) }}"
               class="btn btn-light">
                Cancel
            </a>

            <button type="submit"
                    class="btn btn-primary">
                Simpan Routes
            </button>
        </div>

    </form>

</div>

{{-- ======================================================
| SCRIPT
====================================================== --}}
<script>
let routeIndex = {{ $pnr->routes->count() }};

document.getElementById('btn-add-route').addEventListener('click', () => {

    const wrapper  = document.getElementById('routes-wrapper');
    const sectorNo = wrapper.querySelectorAll('.route-item').length + 1;

    wrapper.insertAdjacentHTML('beforeend', `
    <div class="card route-item">
        <div class="card-body">

            <div class="row-between mb-sm">
                <div class="fw-semibold sector-title">
                    Sector ${sectorNo}
                </div>

                <button type="button"
                        class="btn btn-outline-danger btn-sm btn-remove-route">
                    Hapus
                </button>
            </div>

            <div class="grid-2 gap-md">

                <div class="stack stack-sm">
                    <input type="text"
                           name="routes[${routeIndex}][origin]"
                           class="form-input text-uppercase"
                           placeholder="Origin (CGK)"
                           required>

                    <input type="date"
                           name="routes[${routeIndex}][departure_date]"
                           class="form-input"
                           required>

                    <input type="text"
                           name="routes[${routeIndex}][flight_number]"
                           class="form-input"
                           placeholder="Flight No">
                </div>

                <div class="stack stack-sm">
                    <div class="grid-2 gap-sm">
                        <input type="time"
                               name="routes[${routeIndex}][departure_time]"
                               class="form-input">

                        <input type="time"
                               name="routes[${routeIndex}][arrival_time]"
                               class="form-input">
                    </div>

                    <select name="routes[${routeIndex}][arrival_day_offset]"
                            class="form-select">
                        <option value="0">Same Day</option>
                        <option value="1">+1 Day</option>
                    </select>
                </div>

            </div>

        </div>
    </div>
    `);

    routeIndex++;
});

document.addEventListener('click', e => {
    if (e.target.classList.contains('btn-remove-route')) {
        e.target.closest('.route-item').remove();
        renumberSectors();
    }
});

function renumberSectors() {
    document.querySelectorAll('.sector-title')
        .forEach((el, i) => el.innerText = 'Sector ' + (i + 1));
}
</script>

@endsection
