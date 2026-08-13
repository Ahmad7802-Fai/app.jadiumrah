<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice</title>

<style>

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:11px;
    color:#333;
}

/* ================= HEADER ================= */

.header{
    border-bottom:2px solid #2e7d32;
    padding-bottom:12px;
    margin-bottom:16px;
}

.header table{
    width:100%;
}

.logo img{
    height:40px;
}

.brand{
    font-size:15px;
    font-weight:bold;
    color:#2e7d32;
}

.tagline{
    font-size:10px;
    color:#777;
}

.qr-header img{
    width:60px;
}

.invoice-right{
    text-align:right;
}

.invoice-title{
    font-size:18px;
    font-weight:bold;
}

.invoice-number{
    font-size:11px;
    color:#777;
}

/* ================= META ================= */

.meta table{
    width:100%;
}

.meta td{
    padding:3px 0;
}

.label{
    width:120px;
    color:#666;
}

/* ================= STATUS ================= */

.status{
    padding:3px 10px;
    border-radius:10px;
    font-size:10px;
}

.status-unpaid{
    background:#fff3cd;
    color:#856404;
}

.status-paid{
    background:#e8f5e9;
    color:#2e7d32;
}

/* ================= SECTION ================= */

.section{
    margin-top:16px;
}

.section-title{
    font-weight:bold;
    margin-bottom:6px;
}

/* ================= TABLE ================= */

.table{
    width:100%;
    border-collapse:collapse;
}

.table th{
    text-align:left;
    border-bottom:1px solid #ddd;
    padding:6px 0;
    font-size:10px;
}

.table td{
    padding:6px 0;
    border-bottom:1px solid #f2f2f2;
}

/* ================= SUMMARY ================= */

.summary{
    margin-top:20px;
}

.summary-box{
    width:260px;
    border:1px solid #e5e5e5;
    padding:10px;
}

.total{
    font-size:15px;
    font-weight:bold;
    color:#2e7d32;
}

/* ================= SIGNATURE ================= */

.signature{
    margin-top:40px;
}

.signature-box{
    width:220px;
    text-align:center;
}

.signature-line{
    border-top:1px solid #ccc;
    margin-top:40px;
}

/* ================= FOOTER ================= */

.footer{
    margin-top:20px;
    font-size:10px;
    text-align:center;
    color:#888;
}

</style>

</head>

<body>


<!-- ================= HEADER ================= -->

<div class="header">

<table width="100%">

<tr>

<!-- LOGO + BRAND -->
<td width="70%">

<table>

<tr>

<td class="logo">
<img src="{{ public_path('logo.png') }}">
</td>

<td>

<div class="brand">
{{ config('app.name') }}
</div>

<div class="tagline">
Layanan perjalanan Umrah terpercaya
</div>

</td>

</tr>

</table>

</td>


<!-- QR + INVOICE -->
<td width="30%" align="right">

<table>

<tr>

<td style="padding-right:10px">

<img src="data:image/png;base64,{{ $qr }}" width="65">

</td>

<td class="invoice-right">

<div class="invoice-title">
INVOICE
</div>

<div class="invoice-number">
{{ $booking->invoice_number }}
</div>

</td>

</tr>

</table>

</td>

</tr>

</table>

</div>

<!-- ================= META ================= -->

<div class="meta">

<table>

<tr>
<td class="label">Booking</td>
<td>: {{ $booking->booking_code }}</td>
</tr>

<tr>
<td class="label">Tanggal</td>
<td>: {{ $booking->created_at->format('d M Y') }}</td>
</tr>

<tr>
<td class="label">Jatuh Tempo</td>
<td>: {{ $booking->created_at->addDays(7)->format('d M Y') }}</td>
</tr>

<tr>
<td class="label">Paket</td>
<td>: {{ $booking->paket->name ?? '-' }}</td>
</tr>

<tr>
<td class="label">Status</td>
<td>

@if($booking->paid_amount >= $booking->total_amount)

<span class="status status-paid">
LUNAS
</span>

@else

<span class="status status-unpaid">
BELUM LUNAS
</span>

@endif

</td>

</tr>

</table>

</div>


<!-- ================= JAMAAH ================= -->

<div class="section">

<div class="section-title">
Daftar Jamaah
</div>

<table class="table">

<tr>
<th width="40">No</th>
<th>Nama Jamaah</th>
</tr>

@foreach($booking->jamaahs as $i => $jamaah)

<tr>
<td>{{ $i+1 }}</td>
<td>{{ $jamaah->nama_lengkap }}</td>
</tr>

@endforeach

</table>

</div>


<!-- ================= BREAKDOWN ================= -->

<div class="section">

<div class="section-title">
Breakdown Biaya Paket
</div>

<table class="table">

<tr>
<td>Harga Paket</td>
<td align="right">
Rp {{ number_format($booking->total_amount,0,',','.') }}
</td>
</tr>

<tr>
<td>DP</td>
<td align="right">
Rp {{ number_format($booking->payments->where('type','dp')->sum('amount'),0,',','.') }}
</td>
</tr>

<tr>
<td>Cicilan</td>
<td align="right">
Rp {{ number_format($booking->payments->where('type','cicilan')->sum('amount'),0,',','.') }}
</td>
</tr>

</table>

</div>


<!-- ================= PEMBAYARAN ================= -->

<div class="section">

<div class="section-title">
Riwayat Pembayaran
</div>

<table class="table">

<tr>
<th width="120">Tanggal</th>
<th width="120">Metode</th>
<th>Jumlah</th>
</tr>

@foreach($booking->payments as $payment)

<tr>
<td>{{ $payment->paid_at->format('d M Y') }}</td>
<td>{{ ucfirst($payment->method) }}</td>
<td>Rp {{ number_format($payment->amount,0,',','.') }}</td>
</tr>

@endforeach

</table>

</div>


<!-- ================= SUMMARY ================= -->

<div class="summary">

<table width="100%">

<tr>

<td width="60%"></td>

<td>

<div class="summary-box">

<table width="100%">

<tr>
<td>Total Paket</td>
<td align="right">
Rp {{ number_format($booking->total_amount,0,',','.') }}
</td>
</tr>

<tr>
<td>Total Dibayar</td>
<td align="right">
Rp {{ number_format($booking->paid_amount,0,',','.') }}
</td>
</tr>

<tr>
<td colspan="2"><hr></td>
</tr>

<tr>
<td><strong>Sisa Tagihan</strong></td>
<td align="right" class="total">

Rp {{ number_format($booking->total_amount - $booking->paid_amount,0,',','.') }}

</td>
</tr>

</table>

</div>

</td>

</tr>

</table>

</div>


<!-- ================= SIGNATURE ================= -->

<div class="signature">

<table width="100%">

<tr>

<td></td>

<td class="signature-box">

<div>Authorized Signature</div>

<div class="signature-line"></div>

<div style="font-size:10px">

{{ config('app.name') }}

</div>

</td>

</tr>

</table>

</div>


<!-- ================= FOOTER ================= -->

<div class="footer">

Invoice ini diterbitkan secara otomatis oleh sistem {{ config('app.name') }}.

</div>

</body>
</html>