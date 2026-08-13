@extends('layouts.admin')

@section('title','Invoice Ticket')

@section('content')

<div class="page page--fluid">

    {{-- ======================================================
    | PAGE HEADER
    ====================================================== --}}
    <div class="page-header mb-md">
        <div>
            <div class="page-title">Invoice Ticket</div>
            <div class="text-xs text-muted">
                Daftar invoice dari seluruh PNR
            </div>
        </div>
    </div>

    {{-- ======================================================
    | FILTER
    ====================================================== --}}
    <div class="filter dashboard-filter mb-md">

        {{-- FILTER HEADER --}}
        <div class="filter-header">
            <div class="filter-title">Filter Invoice</div>
        </div>

        {{-- FILTER FORM --}}
        <form method="GET">

            <div class="filter-body">

                {{-- STATUS --}}
                <div class="filter-item">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="UNPAID" @selected(request('status') === 'UNPAID')>Unpaid</option>
                        <option value="PARTIAL" @selected(request('status') === 'PARTIAL')>Partial</option>
                        <option value="PAID" @selected(request('status') === 'PAID')>Paid</option>
                        <option value="CANCELLED" @selected(request('status') === 'CANCELLED')>Cancelled</option>
                    </select>
                </div>

                {{-- CLIENT --}}
                <div class="filter-item">
                    <label>Client</label>
                    <select name="client_id" class="form-select">
                        <option value="">All Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}"
                                @selected(request('client_id') == $client->id)>
                                {{ $client->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- SEARCH --}}
                <div class="filter-item">
                    <label>Search</label>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control"
                           placeholder="Invoice / PNR">
                </div>

            </div>

            {{-- FILTER ACTIONS --}}
            <div class="filter-actions">
                <button class="btn btn-primary btn-sm">
                    Filter
                </button>

                @if(request()->hasAny(['status','client_id','q']))
                    <a href="{{ route('ticketing.invoice.index') }}"
                       class="btn btn-light btn-sm">
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- ======================================================
| TABLE : INVOICE
====================================================== --}}
<div class="card card-hover">

    {{-- CARD HEADER --}}
    <div class="card-header">
        <div>
            <div class="card-title">Daftar Invoice</div>
            <div class="card-subtitle">
                Total: {{ $invoices->total() }} invoice
            </div>
        </div>
    </div>

    {{-- CARD BODY --}}
    <div class="card-body p-0">
        <div class="table-wrap">
            <table class="table table-compact">

                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Client</th>
                        <th>PNR</th>
                        <th>Tanggal</th>
                        <th class="table-right">Total</th>
                        <th>Status</th>
                        <th class="col-actions table-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($invoices as $inv)
                        <tr>

                            {{-- INVOICE --}}
                            <td class="mono fw-semibold">
                                {{ $inv->invoice_number }}
                            </td>

                            {{-- CLIENT --}}
                            <td>
                                {{ optional($inv->pnr?->client)->nama ?? '-' }}
                            </td>

                            {{-- PNR --}}
                            <td class="mono text-muted">
                                {{ optional($inv->pnr)->pnr_code ?? '-' }}
                            </td>

                            {{-- DATE --}}
                            <td class="text-muted">
                                {{ $inv->created_at->format('d M Y') }}
                            </td>

                            {{-- TOTAL --}}
                            <td class="table-right fw-semibold">
                                @money($inv->total_amount)
                            </td>

                            {{-- STATUS --}}
                            <td>
                                @include('ticketing.invoice._status_badge', [
                                    'status' => $inv->status
                                ])
                            </td>

                            {{-- ACTION --}}
                            <td class="table-right col-actions">
                                <a href="{{ route('ticketing.invoice.show', $inv) }}"
                                   class="btn btn-outline btn-sm">
                                    Detail
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty">
                                📄 Belum ada invoice
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>

    {{-- ======================================================
    | PAGINATION
    ====================================================== --}}
    <div class="mt-md">
        {{ $invoices->links() }}
    </div>

</div>

@endsection
