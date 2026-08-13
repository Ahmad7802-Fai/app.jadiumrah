@extends('layouts.admin')

@section('title','Ticketing – PNR')

@section('content')

{{-- =========================
| PAGE HEADER
========================= --}}
<div class="page-header mb-3">
    <div>
        <h1 class="page-title">Ticket PNR</h1>
        <p class="page-subtitle">
            Daftar PNR tiket
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('ticketing.pnr.create') }}"
           class="btn btn-primary btn-sm">
            + Create PNR
        </a>
    </div>
</div>

{{-- =========================
| TABLE
========================= --}}
<div class="card card-hover">
    <div class="card-body p-0">

        <div class="table-responsive">
            <table class="table table-compact w-full text-sm">

                {{-- TABLE HEAD --}}
                <thead>
                    <tr>
                        <th>PNR</th>
                        <th>Client</th>
                        <th class="text-center">Pax</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>

                {{-- TABLE BODY --}}
                <tbody>
                @forelse($pnrs as $pnr)
                    <tr>

                        {{-- PNR CODE --}}
                        <td class="font-mono text-xs font-semibold">
                            {{ $pnr->pnr_code }}
                        </td>

                        {{-- CLIENT --}}
                        <td>
                            <div class="fw-semibold">
                                {{ optional($pnr->client)->nama ?? '-' }}
                            </div>
                            <div class="text-xs text-muted uppercase">
                                Client
                            </div>

                            @if($pnr->airline_name)
                                <div class="text-xs text-muted mt-1">
                                    {{ $pnr->airline_name }}
                                    @if($pnr->airline_code)
                                        ({{ $pnr->airline_code }})
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- PAX --}}
                        <td class="text-center">
                            {{ $pnr->pax }}
                        </td>

                        {{-- TOTAL --}}
                        <td class="text-right fw-semibold">
                            @money($pnr->total_fare)
                        </td>

                        {{-- STATUS --}}
                        <td class="text-center">
                            @include('ticketing.pnr._status_badge', [
                                'status' => $pnr->status
                            ])
                        </td>

                        {{-- ACTION --}}
                        <td class="text-right">
                            <a href="{{ route('ticketing.pnr.show', $pnr) }}"
                               class="btn btn-outline-secondary btn-xs">
                                Detail
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="table-empty">
                            Tidak ada data PNR
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>

{{-- =========================
| PAGINATION
========================= --}}
@if($pnrs->hasPages())
<div class="mt-4 d-flex justify-content-center">
    {{ $pnrs->links() }}
</div>
@endif

@endsection
