<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:11px;
    color:#333;
    margin:0;
}

.container{
    padding:30px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{
    font-size:18px;
    font-weight:bold;
}

.invoice-title{
    font-size:20px;
    font-weight:bold;
}

.meta{
    margin-top:10px;
}

.table{
    width:100%;
    border-collapse:collapse;
}

.table td{
    padding:4px 0;
}

.line{
    border-top:1px solid #ddd;
    margin:12px 0;
}

.section-title{
    font-weight:bold;
    margin-bottom:6px;
}

.payment-table{
    width:100%;
    border-collapse:collapse;
    margin-top:5px;
}

.payment-table th{
    text-align:left;
    border-bottom:1px solid #ddd;
    padding:5px 0;
}

.payment-table td{
    padding:4px 0;
}

.summary td{
    padding:3px 0;
}

.total{
    font-weight:bold;
    font-size:12px;
}

.qr{
    margin-top:15px;
}

.footer{
    margin-top:20px;
    font-size:9px;
    color:#777;
}

</style>

</head>

<body>

<div class="container">

<!-- HEADER -->

<div class="header">

<div class="logo">
Umrah Core Travel
</div>

<div class="invoice-title">
INVOICE
</div>

</div>

<!-- META -->

<table class="table meta">

<tr>
<td width="25%">Invoice Number</td>
<td>: {{ $booking->invoice_number ?? $booking->booking_code }}</td>
</tr>

<tr>
<td>Booking Code</td>
<td>: {{ $booking->booking_code }}</td>
</tr>

<tr>
<td>Date</td>
<td>: {{ now()->format('d M Y') }}</td>
</tr>

</table>

<div class="line"></div>

<!-- CUSTOMER -->

<div class="section-title">Customer / Jamaah</div>

<ul>

@foreach($booking->jamaahs as $j)

<li>{{ $j->nama_lengkap }}</li>

@endforeach

</ul>

<div class="line"></div>

<!-- PACKAGE -->

<table class="table">

<tr>
<td width="25%">Paket</td>
<td>: {{ $booking->paket->name }}</td>
</tr>

<tr>
<td>Departure</td>
<td>: {{ \Carbon\Carbon::parse($booking->departure->departure_date)->format('d M Y') }}</td>
</tr>

<tr>
<td>Room</td>
<td>: {{ $booking->room_type }}</td>
</tr>

</table>

<div class="line"></div>

<!-- PAYMENT HISTORY -->

@if($booking->payments->count())

<div class="section-title">Payment History</div>

<table class="payment-table">

<tr>
<th width="30%">Date</th>
<th width="40%">Method</th>
<th width="30%">Amount</th>
</tr>

@foreach($booking->payments as $p)

<tr>
<td>
{{ $p->paid_at 
    ? \Carbon\Carbon::parse($p->paid_at)->format('d M Y')
    : '-' 
}}
</td>

<td>
{{ ucfirst($p->method) }}
</td>

<td>
Rp {{ number_format($p->amount,0,',','.') }}
</td>

</tr>

@endforeach

</table>

<div class="line"></div>

@endif


<!-- SUMMARY -->

<table class="table summary">

<tr>
<td width="25%">Total</td>
<td>: Rp {{ number_format($booking->total_amount,0,',','.') }}</td>
</tr>

<tr>
<td>Paid</td>
<td>: Rp {{ number_format($booking->paid_amount,0,',','.') }}</td>
</tr>

<tr class="total">
<td>Remaining</td>
<td>: Rp {{ number_format($booking->total_amount - $booking->paid_amount,0,',','.') }}</td>
</tr>

</table>


<!-- QR -->

<div class="qr">

<img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ url('/verify/'.$booking->booking_code) }}">

</div>

<!-- FOOTER -->

<div class="footer">

Invoice ini sah tanpa tanda tangan.  
Verifikasi booking melalui sistem Umrah Core.

</div>

</div>

</body>
</html>