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

.title{
font-size:20px;
font-weight:bold;
}

.table{
width:100%;
border-collapse:collapse;
margin-top:10px;
}

.table th{
text-align:left;
border-bottom:1px solid #ddd;
padding:6px 0;
}

.table td{
padding:5px 0;
}

.line{
border-top:1px solid #ddd;
margin:12px 0;
}

.amount{
font-weight:bold;
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

<div class="title">
RECEIPT
</div>

</div>


<!-- BOOKING INFO -->

<table class="table">

<tr>
<td width="25%">Booking Code</td>
<td>: {{ $booking->booking_code }}</td>
</tr>

<tr>
<td>Paket</td>
<td>: {{ $booking->paket->name }}</td>
</tr>

<tr>
<td>Departure</td>
<td>: {{ \Carbon\Carbon::parse($booking->departure->departure_date)->format('d M Y') }}</td>
</tr>

</table>

<div class="line"></div>

<!-- CUSTOMER -->

<b>Customer / Jamaah</b>

<ul>

@foreach($booking->jamaahs as $j)

<li>{{ $j->nama_lengkap }}</li>

@endforeach

</ul>

<div class="line"></div>


<!-- PAYMENT LIST -->

<b>Payment Receipt</b>

<table class="table">

<tr>
<th>Date</th>
<th>Receipt</th>
<th>Type</th>
<th>Method</th>
<th>Amount</th>
</tr>

@foreach($booking->payments as $p)

<tr>

<td>
{{ \Carbon\Carbon::parse($p->paid_at)->format('d M Y') }}
</td>

<td>
{{ $p->receipt_number ?? $p->payment_code }}
</td>

<td>
{{ ucfirst($p->type) }}
</td>

<td>
{{ ucfirst($p->method) }}
</td>

<td class="amount">
Rp {{ number_format($p->amount,0,',','.') }}
</td>

</tr>

@endforeach

</table>

<div class="line"></div>

<!-- TOTAL -->

<table class="table">

<tr>
<td width="25%">Total Paid</td>
<td class="amount">
Rp {{ number_format($booking->paid_amount,0,',','.') }}
</td>
</tr>

</table>


<!-- QR -->

<br>

<img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ url('/verify/'.$booking->booking_code) }}">

<div class="footer">

Receipt ini merupakan bukti pembayaran resmi.  
Verifikasi transaksi melalui sistem Umrah Core.

</div>

</div>

</body>
</html>