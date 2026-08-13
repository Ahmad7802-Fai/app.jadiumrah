@extends('layouts.admin')

@section('title','PNR Detail')

@section('content')

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="page-header mb-md">

    <div>
        <div class="page-title mono text-uppercase">
            {{ $pnr->pnr_code }}
        </div>

        <div class="text-xs text-muted">
            Client:
            <strong>{{ $pnr->client->nama ?? '-' }}</strong>
        </div>
    </div>

    <div class="page-actions">

        <a href="{{ route('ticketing.pnr.index') }}"
           class="btn btn-secondary btn-sm">
            ← Back
        </a>

        @can('update', $pnr)
            @if($pnr->status !== 'ISSUED')
                <a href="{{ route('ticketing.pnr.edit', $pnr) }}"
                   class="btn btn-outline btn-sm">
                    Edit PNR
                </a>

                <a href="{{ route('ticketing.pnr.routes.edit', $pnr) }}"
                   class="btn btn-outline btn-sm">
                    Edit Routes
                </a>
            @endif
        @endcan

        @include('ticketing.pnr._status_badge', ['status' => $pnr->status])
    </div>
</div>
{{-- ======================================================
| SUMMARY (HORIZONTAL COMPACT)
====================================================== --}}
<div class="pnr-summary mb-md">

    <div class="summary-card stat-primary">
        <div class="summary-icon">👥</div>
        <div>
            <div class="summary-label">Pax</div>
            <div class="summary-value">{{ $pnr->pax }}</div>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon">💺</div>
        <div>
            <div class="summary-label">Fare / Pax</div>
            <div class="summary-value">
                <x-ticketing.money :value="$pnr->fare_per_pax" />
            </div>
        </div>
    </div>

    <div class="summary-card stat-success">
        <div class="summary-icon">💰</div>
        <div>
            <div class="summary-label">Total Fare</div>
            <div class="summary-value">
                <x-ticketing.money :value="$pnr->total_fare" />
            </div>
        </div>
    </div>

    <div class="summary-card {{ $pnr->invoice_outstanding > 0 ? 'stat-danger' : 'stat-success' }}">
        <div class="summary-icon">⚠️</div>
        <div>
            <div class="summary-label">Outstanding</div>
            <div class="summary-value">
                <x-ticketing.money :value="$pnr->invoice_outstanding" />
            </div>
        </div>
    </div>

</div>

{{-- ======================================================
| META INFORMATION
====================================================== --}}
<div class="card mb-md">
    <div class="card-body-sm stat-grid">

        <div class="card card-compact">
            <div class="text-xxs text-muted">Airline</div>
            <div class="fw-semibold">{{ $pnr->airline_label }}</div>
        </div>

        <div class="card card-compact">
            <div class="text-xxs text-muted">Class</div>
            <div class="fw-semibold">{{ $pnr->airline_class ?? '-' }}</div>
        </div>

        <div class="card card-compact">
            <div class="text-xxs text-muted">Agent</div>
            <div class="fw-semibold">{{ $pnr->agent?->nama ?? '-' }}</div>
        </div>

        <div class="card card-compact">
            <div class="text-xxs text-muted">Seat</div>
            <div class="fw-semibold">{{ $pnr->seat ?? '-' }}</div>
        </div>

    </div>
</div>

{{-- ======================================================
| FLIGHT ROUTES (TIMELINE)
====================================================== --}}
<div class="card mb-md">
    <div class="card-body-sm">

        <div class="fw-semibold text-xs mb-sm">
            ✈ Flight Itinerary
        </div>

        <div class="flight-timeline">

            @forelse($pnr->routes as $route)
                <div class="timeline-item">

                    {{-- LEFT (DOT + LINE) --}}
                    <div class="timeline-marker">
                        <span class="dot"></span>
                        @if(!$loop->last)
                            <span class="line"></span>
                        @endif
                    </div>

                    {{-- CONTENT --}}
                    <div class="timeline-content">

                        <div class="route-head">
                            <div class="route-sector">
                                S{{ $route->sector }}
                            </div>

                            <div class="route-city">
                                {{ strtoupper($route->origin) }}
                                <span class="arrow">→</span>
                                {{ strtoupper($route->destination) }}
                            </div>

                            <div class="route-date">
                                {{ \Carbon\Carbon::parse($route->departure_date)->format('d M Y') }}
                            </div>
                        </div>

                        <div class="route-meta">

                            <span class="meta-item">
                                ✈ {{ $route->flight_number ?? '-' }}
                            </span>

                            @if($route->departure_time)
                                <span class="meta-item">
                                    🕒 {{ substr($route->departure_time, 0, 5) }}
                                </span>
                            @endif

                            @if($route->arrival_time)
                                <span class="meta-item">
                                    → {{ substr($route->arrival_time, 0, 5) }}
                                    @if($route->arrival_day_offset)
                                        <small class="muted">(+{{ $route->arrival_day_offset }}D)</small>
                                    @endif
                                </span>
                            @endif

                        </div>

                    </div>

                </div>
            @empty
                <div class="text-xs text-muted italic">
                    No flight routes defined.
                </div>
            @endforelse

        </div>

    </div>
</div>

{{-- ======================================================
| ALLOCATION HISTORY
====================================================== --}}
@if($pnr->allocations->count())
<div class="card mb-md">
    <div class="card-header">
        <div class="card-title">Allocation History</div>
    </div>

    <div class="card-body p-0">
        <table class="table table-compact table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pnr->allocations as $alloc)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($alloc->allocation_date)->format('d M Y') }}</td>
                    <td class="text-right fw-semibold">
                        <x-ticketing.money :value="$alloc->allocated_amount" />
                    </td>
                    <td>
                        <span class="badge-success">{{ $alloc->status }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ======================================================
| ACTIONS (PRIMARY FLOW)
====================================================== --}}
@can('update', $pnr)
<div class="card">
    <div class="card-body-sm">

        @if($pnr->status === 'ON_FLOW')
            <button class="btn btn-primary btn-lg btn-block"
                    data-modal-open="confirmPnrModal">
                🔒 Confirm PNR
            </button>
        @endif

        @if($pnr->status === 'CONFIRMED' && $pnr->invoice_total == 0)
            <form method="POST"
                  action="{{ route('ticketing.invoice.createFromPnr', $pnr) }}">
                @csrf
                <button class="btn btn-primary btn-block mt-sm">
                    🧾 Create Invoice
                </button>
            </form>
        @endif

        @if($pnr->status === 'ISSUED')
            <div class="text-xs text-muted italic text-center">
                PNR locked (Issued)
            </div>
        @endif

    </div>
</div>
@endcan

{{-- ======================================================
| CONFIRM MODAL
====================================================== --}}
@include('ticketing.pnr._confirm_modal')

@endsection
@push('scripts')
<script>
document.addEventListener('click', function (e) {

    // OPEN MODAL
    const openBtn = e.target.closest('[data-modal-open]');
    if (openBtn) {
        const id = openBtn.getAttribute('data-modal-open');
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('show');
            document.body.classList.add('modal-open');
        }
    }

    // CLOSE MODAL BUTTON
    if (e.target.closest('[data-modal-close]')) {
        const modal = e.target.closest('.ju-modal');
        if (modal) {
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
    }

    // CLICK OVERLAY
    if (e.target.classList.contains('ju-modal')) {
        e.target.classList.remove('show');
        document.body.classList.remove('modal-open');
    }
});
</script>
@endpush
