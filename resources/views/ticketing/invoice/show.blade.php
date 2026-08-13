@extends('layouts.admin')

@section('title','Invoice Detail')

@section('content')
<div class="page page--narrow">

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="page-header mb-md">
    <div>
        <a href="{{ route('ticketing.invoice.index') }}"
           class="text-sm text-muted">
            ← Back to Invoice List
        </a>

        <div class="page-title mt-xs">
            Invoice
            <span class="mono">{{ $invoice->invoice_number }}</span>
        </div>
    </div>

    <div class="page-actions">
        <a href="{{ route('ticketing.invoice.pdf', $invoice) }}"
           target="_blank"
           class="btn btn-outline btn-sm">
            🧾 Invoice PDF
        </a>
    </div>
</div>

{{-- ======================================================
| STATUS SUMMARY
====================================================== --}}
<div class="card mb-md">
    <div class="card-body">
        <div class="stat-grid">

            <div class="card-stat card-stat-primary">
                <div class="stat-label">Status</div>
                @include('ticketing.invoice._status_badge', ['status' => $invoice->status])
            </div>

            <div class="card-stat">
                <div class="stat-label">Total</div>
                <div class="stat-value">@money($invoice->total_amount)</div>
            </div>

            <div class="card-stat-success">
                <div class="stat-label">Net Paid</div>
                <div class="stat-value">@money($invoice->net_paid)</div>
            </div>

            <div class="card-stat-danger">
                <div class="stat-label">Outstanding</div>
                <div class="stat-value">@money($invoice->outstanding_amount)</div>
            </div>

        </div>
    </div>
</div>

{{-- ======================================================
| PNR INFORMATION
====================================================== --}}
<div class="card mb-md">
    <div class="card-header">
        <div class="card-title">PNR Information</div>
    </div>

    <div class="card-body">
        <div class="info-grid">

            <div class="info-item">
                <div class="label">PNR Code</div>
                <div class="value mono">{{ $invoice->pnr?->pnr_code ?? '-' }}</div>
            </div>

            <div class="info-item">
                <div class="label">Client</div>
                <div class="value">{{ $invoice->pnr?->client?->nama ?? '-' }}</div>
            </div>

            <div class="info-item">
                <div class="label">Pax</div>
                <div class="value">{{ $invoice->pnr?->pax ?? 0 }}</div>
            </div>

            <div class="info-item">
                <div class="label">Invoice Date</div>
                <div class="value">{{ $invoice->created_at->format('d M Y') }}</div>
            </div>

        </div>
    </div>
</div>

{{-- ======================================================
| ✈ ROUTE DETAILS (WAJIB)
====================================================== --}}
{{-- @if($invoice->pnr && $invoice->pnr->routes->count())
<div class="card mb-md">
    <div class="card-header">
        <div class="card-title">✈ Route Details</div>
    </div>

    <div class="card-body p-0">
        <table class="table table-compact table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Sector</th>
                    <th>Route</th>
                    <th>Date</th>
                    <th>Flight</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->pnr->routes as $r)
                <tr>
                    <td class="text-center">S{{ $r->sector }}</td>
                    <td class="fw-semibold">
                        {{ $r->origin }} → {{ $r->destination }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($r->departure_date)->format('d M Y') }}
                    </td>
                    <td>{{ $r->flight_number ?? '-' }}</td>
                    <td class="text-muted">
                        @if($r->departure_time)
                            {{ substr($r->departure_time,0,5) }}
                        @endif
                        @if($r->arrival_time)
                            → {{ substr($r->arrival_time,0,5) }}
                            @if($r->arrival_day_offset)
                                (+{{ $r->arrival_day_offset }}D)
                            @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif --}}

{{-- ======================================================
| INVOICE ITEMS
====================================================== --}}
<div class="card mb-md">
    <div class="card-header">
        <div class="card-title">Invoice Items</div>
    </div>

    <div class="card-body p-0">
        <table class="table table-compact table-bordered">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center" width="80">Qty</th>
                    <th class="text-right" width="140">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>
                        {{-- ITEM DESCRIPTION --}}
                        <div class="fw-semibold">
                            {{ $item->description }}
                        </div>

                        {{-- ROUTE DETAIL --}}
                        @if(!empty($routeLines))
                        <div class="mt-1 text-xs text-muted"
                             style="line-height:1.35;">
                            <div class="fw-semibold text-gray-600">
                                Route
                            </div>

                            <div class="font-mono">
                                {!! nl2br(e($routeLines)) !!}
                            </div>
                        </div>
                        @endif
                    </td>

                    <td class="text-center align-top">
                        {{ $item->qty }}
                    </td>

                    <td class="text-right fw-semibold align-top">
                        @money($item->subtotal)
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ======================================================
| PAYMENT HISTORY
====================================================== --}}
@if($invoice->payments->count())
<div class="card mb-md">
    <div class="card-header">
        <div class="card-title">Payment History</div>
    </div>

    <div class="card-body p-0">
        <table class="table table-compact table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-center">Method</th>
                    <th class="text-center">Bank</th>
                    <th class="text-right">Amount</th>
                    <th class="text-center">Receipt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $pay)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') }}</td>
                    <td class="text-center fw-semibold">{{ $pay->method }}</td>
                    <td class="text-center">{{ $pay->bank ?? '-' }}</td>
                    <td class="text-right fw-semibold">@money($pay->amount)</td>
                    <td class="text-center">
                        @if($pay->receipt_file)
                            <a href="{{ asset('storage/'.$pay->receipt_file) }}"
                               target="_blank">View</a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ======================================================
| REFUND HISTORY
====================================================== --}}
@if($invoice->refunds->count())
<div class="card mb-md">
    <div class="card-header">
        <div class="card-title">Refund History</div>
    </div>

    <div class="card-body p-0">
        <table class="table table-compact table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Amount</th>
                    <th class="text-center">Status</th>
                    <th>Reason</th>
                    <th class="text-center">Approved By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->refunds as $refund)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($refund->refunded_at)->format('d M Y') }}</td>
                    <td class="text-right fw-semibold text-danger">@money($refund->amount)</td>
                    <td class="text-center">{{ $refund->approval_status }}</td>
                    <td>{{ $refund->reason ?? '-' }}</td>
                    <td class="text-center">{{ $refund->approved_by ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ======================================================
| PAYMENT ACTION
====================================================== --}}
@can('pay', $invoice)
@if(in_array($invoice->status, ['UNPAID','PARTIAL']))
<div class="card card-soft">
    <div class="card-header">
        <div class="card-title">Payment</div>
    </div>

    <div class="card-body">
        <form method="POST"
              action="{{ route('ticketing.payment.store', $invoice) }}"
              enctype="multipart/form-data"
              class="form-grid">
            @csrf

            <input type="number" name="amount"
                   max="{{ $invoice->outstanding_amount }}"
                   class="form-input" required>

            <select name="method" class="form-input" required>
                <option value="">-- pilih --</option>
                <option value="TRANSFER">Transfer</option>
                <option value="CASH">Cash</option>
                <option value="VA">Virtual Account</option>
            </select>

            <select name="bank" class="form-input">
                <option value="">-</option>
                <option>BCA</option>
                <option>MANDIRI</option>
                <option>BRI</option>
                <option>BNI</option>
            </select>

            <input type="file" name="receipt" class="form-input">

            <button class="btn btn-primary btn-sm">Pay</button>
        </form>
    </div>
</div>
@endif
@endcan

</div>
@endsection
