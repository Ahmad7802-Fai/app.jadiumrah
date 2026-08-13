<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice {{ $invoice->invoice_number }}</title>

<style>
/* ===============================
   GLOBAL
================================ */
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 9.3px;
    color: #111;
    line-height: 1.25;
}

strong { font-weight: 700; }

.text-muted { color: #666; }
.text-right { text-align: right; }
.text-center { text-align: center; }

hr {
    border: none;
    border-top: 0.6px solid #ccc;
    margin: 8px 0;
}

/* ===============================
   HEADER
================================ */
.header-table {
    width: 100%;
    margin-bottom: 6px;
}

.logo {
    height: 50px;
}

.invoice-title {
    font-size: 15px;
    font-weight: bold;
}

/* ===============================
   TABLE
================================ */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 5px;
}

.table th,
.table td {
    border: 0.6px solid #d0d0d0;
    padding: 4px 5px;
    vertical-align: top;
}

.table th {
    background: #f6f7f9;
    font-weight: 600;
    font-size: 9px;
}

/* ===============================
   DESCRIPTION
================================ */
.description {
    white-space: pre-line;
    line-height: 1.22;
    padding-top: 3px;
    padding-bottom: 3px;
}

/* ===============================
   ROUTE
================================ */
.route-box {
    margin-top: 4px;
    font-size: 8.8px;
}

.route-title {
    font-weight: 600;
    color: #555;
    margin-bottom: 2px;
}

.route-lines {
    font-family: DejaVu Sans Mono;
}

/* ===============================
   SUMMARY
================================ */
.summary {
    width: 100%;
    margin-top: 4px;
    font-size: 9px;
}

.summary td {
    padding: 2px 4px;
}

/* ===============================
   BADGE
================================ */
.badge {
    display: inline-block;
    padding: 2px 6px;
    font-size: 8.5px;
    font-weight: 700;
    border-radius: 3px;
}

.badge-paid {
    background: #e6f4ea;
    color: #1e7e34;
}

.badge-outstanding {
    background: #fdecea;
    color: #b02a37;
}

/* ===============================
   FOOTER
================================ */
.footer {
    margin-top: 12px;
    font-size: 8.5px;
    color: #777;
}
</style>
</head>

<body>

{{-- ===============================
| HEADER
================================ --}}
<table class="header-table">
<tr>
    <td width="60%" valign="top">
        <img src="{{ realpath(public_path('images/logohasantour1.png')) }}"
             class="logo"><br>
        <strong>HASAN TOUR & TRAVEL</strong><br>
        <span class="text-muted">Official Umrah & Travel Partner</span>
    </td>

    <td width="40%" align="right" valign="top">
        <div class="invoice-title">INVOICE</div>
        <div class="text-muted">
            No : {{ $invoice->invoice_number }}<br>
            Date : {{ $invoice->created_at->format('d M Y') }}
        </div>
    </td>
</tr>
</table>

<hr>

{{-- ===============================
| CLIENT INFO
================================ --}}
<table width="100%" style="margin-bottom:4px;">
<tr>
    <td width="40%">
        <strong>Client</strong><br>
        {{ optional($invoice->pnr?->client)->nama ?? '-' }}
    </td>
    <td width="30%">
        <strong>PNR</strong><br>
        {{ optional($invoice->pnr)->pnr_code ?? '-' }}
    </td>
    <td width="30%">
        <strong>Pax</strong><br>
        {{ optional($invoice->pnr)->pax ?? 0 }}
    </td>
</tr>
</table>

{{-- ===============================
| ITEMS
================================ --}}
<table class="table">
<thead>
<tr>
    <th>Description</th>
    <th class="text-center" width="55">Qty</th>
    <th class="text-right" width="85">Unit</th>
    <th class="text-right" width="105">Subtotal</th>
</tr>
</thead>
<tbody>

@foreach($invoice->items as $item)
<tr>
    <td class="description">
        {{ $item->description }}

        @if(!empty($routeLines))
        <div class="route-box">
            <div class="route-title">Route</div>
            <div class="route-lines">
                {{ $routeLines }}
            </div>
        </div>
        @endif
    </td>

    <td class="text-center">{{ $item->qty }}</td>
    <td class="text-right">@money($item->unit_price)</td>
    <td class="text-right">@money($item->subtotal)</td>
</tr>
@endforeach

</tbody>
</table>

{{-- ===============================
| TOTAL SUMMARY
================================ --}}
<table class="summary">
<tr>
    <td width="70%"></td>
    <td width="15%">
        Total<br>
        Paid<br>
        Outstanding
    </td>
    <td width="15%" class="text-right">
        @money($invoice->total_amount)<br>

        <span class="badge badge-paid">
            @money($invoice->paid_amount)
        </span><br>

        <span class="badge badge-outstanding">
            @money($invoice->outstanding_amount)
        </span>
    </td>
</tr>
</table>

{{-- ===============================
| PAYMENT HISTORY
================================ --}}
@if($invoice->payments->count())
<div style="margin-top:8px">
    <strong>Payment History</strong>
    <table class="table">
        <thead>
        <tr>
            <th width="80">Date</th>
            <th width="80">Method</th>
            <th>Bank</th>
            <th class="text-right" width="105">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->payments as $p)
        <tr>
            <td>{{ $p->payment_date->format('d M Y') }}</td>
            <td>{{ strtoupper($p->method) }}</td>
            <td>{{ $p->bank ?? '-' }}</td>
            <td class="text-right">@money($p->amount)</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ===============================
| FOOTER
================================ --}}
<div class="footer">
    Status:
    <strong>{{ strtoupper($invoice->status) }}</strong><br>
    Generated at {{ now()->format('d M Y H:i') }}
</div>

</body>
</html>
