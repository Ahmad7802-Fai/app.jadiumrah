@extends('layouts.admin')

@section('title', 'Edit PNR')

@section('content')

<div class="container-narrow">

    {{-- ======================================================
    | PAGE HEADER
    ====================================================== --}}
    <div class="page-header mb-md">
        <div>
            <div class="page-title mono text-uppercase">
                Edit PNR
            </div>
            <div class="text-xs text-muted">
                {{ $pnr->pnr_code }}
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
    | CARD
    ====================================================== --}}
    <div class="card">
        <div class="card-body">

            <form method="POST"
                  action="{{ route('ticketing.pnr.update', $pnr) }}"
                  class="stack stack-lg">

                @csrf
                @method('PUT')

                {{-- ===============================
                | PNR INFORMATION
                =============================== --}}
                <section>
                    <h3 class="section-title">PNR Information</h3>

                    <div class="grid-3 gap-md">

                        {{-- PNR CODE --}}
                        <div class="form-group">
                            <label class="form-label">PNR Code</label>
                            <input type="text"
                                   value="{{ $pnr->pnr_code }}"
                                   class="form-input is-disabled"
                                   readonly>
                        </div>

                        {{-- AIRLINE --}}
                        <div class="form-group">
                            <label class="form-label">Airline</label>
                            <input type="text"
                                   value="{{ $pnr->airline_code }} — {{ $pnr->airline_name }}"
                                   class="form-input is-disabled"
                                   readonly>
                        </div>

                        {{-- CLASS --}}
                        <div class="form-group">
                            <label class="form-label">Class</label>
                            <select name="airline_class"
                                    class="form-select">
                                <option value="ECONOMY"  @selected($pnr->airline_class === 'ECONOMY')>Economy</option>
                                <option value="BUSINESS" @selected($pnr->airline_class === 'BUSINESS')>Business</option>
                                <option value="FIRST"    @selected($pnr->airline_class === 'FIRST')>First</option>
                            </select>
                        </div>

                        {{-- CLIENT --}}
                        <div class="form-group">
                            <label class="form-label">Client *</label>
                            <select name="client_id"
                                    class="form-select"
                                    required>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}"
                                        @selected($pnr->client_id === $client->id)>
                                        {{ $client->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </section>

                <hr>

                {{-- ===============================
                | PRICING
                =============================== --}}
                <section>
                    <h3 class="section-title">Pricing</h3>

                    <div class="grid-4 gap-md">

                        <div class="form-group">
                            <label class="form-label">Pax *</label>
                            <input type="number"
                                   name="pax"
                                   id="pax"
                                   min="1"
                                   class="form-input"
                                   value="{{ $pnr->pax }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Fare / Pax *</label>
                            <input type="number"
                                   name="fare_per_pax"
                                   id="fare"
                                   class="form-input"
                                   value="{{ $pnr->fare_per_pax }}">
                        </div>

                        {{-- <div class="form-group">
                            <label class="form-label">Deposit / Pax *</label>
                            <input type="number"
                                   name="deposit_per_pax"
                                   id="deposit"
                                   class="form-input"
                                   value="{{ $pnr->deposit_per_pax }}">
                        </div> --}}

                        <div class="form-group">
                            <label class="form-label">Balance</label>
                            <input type="text"
                                   id="balance"
                                   class="form-input is-disabled"
                                   readonly>
                        </div>

                    </div>
                </section>

                <hr>

                {{-- ===============================
                | ROUTES
                =============================== --}}
                <section>
                    <h3 class="section-title mb-xs">Flight Routes</h3>

                    <p class="text-sm text-muted mb-sm">
                        Flight sector hanya bisa diedit selama status
                        <strong>ON FLOW</strong>.
                    </p>

                    <a href="{{ route('ticketing.pnr.routes.edit', $pnr) }}"
                       class="btn btn-outline btn-sm">
                        Edit Flight Routes
                    </a>
                </section>

                <hr>

                {{-- ===============================
                | ACTIONS
                =============================== --}}
                <div class="row justify-end gap-sm">
                    <a href="{{ route('ticketing.pnr.show', $pnr) }}"
                       class="btn btn-light">
                        Cancel
                    </a>

                    <button class="btn btn-primary">
                        Update PNR
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
/* ===============================
 | CALCULATE BALANCE
 =============================== */
function calculate() {
    const pax     = Number(document.getElementById('pax')?.value || 0);
    const fare    = Number(document.getElementById('fare')?.value || 0);
    const deposit = Number(document.getElementById('deposit')?.value || 0);

    const totalFare    = pax * fare;
    const totalDeposit = pax * deposit;
    const balance      = Math.max(0, totalFare - totalDeposit);

    const balanceInput = document.getElementById('balance');
    if (balanceInput) {
        balanceInput.value = balance.toLocaleString('id-ID');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    ['pax', 'fare', 'deposit'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', calculate);
    });

    calculate(); // initial
});
</script>
@endpush
