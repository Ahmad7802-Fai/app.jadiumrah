<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kwitansi #{{ $invoice->no_invoice }}</title>

<style>
@page { size: A4 portrait; margin: 10mm 12mm; }

body {
    font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
    font-size: 10px;
    color: #222;
}

/* ================= BOX ================= */
.box {
    border: 1px solid #425678;
    border-radius: 6px;
    padding: 10px 12px;
    margin-bottom: 8px;
}

/* ================= TITLES ================= */
.section-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #425678;
    margin-bottom: 4px;
}

/* ================= HEADER ================= */
.logo {
    height: 36px;
    margin-bottom: 4px;
}

.header-info {
    font-size: 9.5px;
    line-height: 13px;
}

/* ================= INFO BOX ================= */
.invbox {
    border: 1px solid #425678;
    border-radius: 6px;
    padding: 6px;
    font-size: 9.5px;
}

.invbox td {
    border: none;
    padding: 2px 3px;
}

/* ================= TABLE ================= */
table.compact {
    width: 100%;
    border-collapse: collapse;
}

table.compact th,
table.compact td {
    border: 1px solid #cfd6e0;
    padding: 5px 4px;
    font-size: 9.5px;
}

table.compact th {
    background-color: #425678;
    color: #ffffff;
    font-weight: 700;
    text-align: center;
}

/* ================= BADGES ================= */
.badge {
    padding: 1px 6px;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 600;
}

.approved { background:#d1fae5; color:#065f46; }
.pending  { background:#fef3c7; color:#92400e; }
.rejected { background:#fee2e2; color:#7f1d1d; }

/* ================= FOOTER ================= */
footer {
    margin-top: 18px;
    text-align: right;
    font-size: 9.5px;
}
</style>
</head>

<body>

@php
    $bank = companyBank('invoice');
    $paid = $invoice->payments->where('status','approved')->sum('amount');
    $sisa = $invoice->amount - $paid;
@endphp

{{-- ================= HEADER ================= --}}
<div class="box">
<table width="100%">
<tr valign="top">

<td width="55%">
    @if(company()->logo_invoice)
        <img src="{{ public_path('storage/'.company()->logo_invoice) }}" class="logo">
    @endif

    <div class="header-info">
        <strong style="font-size:12px;color:#425678;">
            {{ company()->brand_name ?? company()->name }}
        </strong><br>
        {!! nl2br(e(company()->address)) !!}
    </div>
</td>

<td width="45%" align="right">

    {!! QrCode::size(65)->generate($invoice->no_invoice) !!}

    <table class="invbox" width="100%" style="margin-top:4px;">
        <tr>
            <td>No</td>
            <td><strong>{{ $invoice->no_invoice }}</strong></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>{{ $invoice->created_at->format('d M Y') }}</td>
        </tr>
        <tr>
            <td>Tempo</td>
            <td>{{ $invoice->due_date?->format('d M Y') }}</td>
        </tr>
    </table>

</td>
</tr>
</table>
</div>

{{-- ================= CLIENT ================= --}}
<div class="box">
<div class="section-title">Informasi Client</div>

<table>
<tr>
    <td width="28%">Nama Client</td>
    <td>: <strong>{{ $invoice->transaksi->client->nama }}</strong></td>
</tr>
<tr>
    <td>Layanan</td>
    <td>: {{ $invoice->transaksi->notes ?? '-' }}</td>
</tr>
<tr>
    <td>Status</td>
    <td>:
        <strong>
        @if($sisa <= 0) Lunas
        @elseif($paid > 0) Parsial
        @else Belum Dibayar
        @endif
        </strong>
    </td>
</tr>
</table>
</div>

{{-- ================= RINCIAN ================= --}}
<div class="section-title">Rincian Layanan</div>

<table class="compact">
<thead>
<tr>
    <th>Deskripsi</th>
    <th width="40">Qty</th>
    <th width="40">Hari</th>
    <th width="90">Harga</th>
    <th width="110">Subtotal</th>
</tr>
</thead>
<tbody>
@foreach($invoice->transaksi->items as $it)
<tr>
    <td>{{ $it->item->nama_item }}</td>
    <td align="center">{{ $it->qty }}</td>
    <td align="center">{{ $it->days ?: '-' }}</td>
    <td align="right">Rp {{ number_format($it->harga,0,',','.') }}</td>
    <td align="right"><strong>Rp {{ number_format($it->subtotal,0,',','.') }}</strong></td>
</tr>
@endforeach
</tbody>
</table>

{{-- ================= TOTAL ================= --}}
<div class="box" style="margin-top:8px;">
    Total Tagihan : <strong>Rp {{ number_format($invoice->amount,0,',','.') }}</strong><br>
    Total Dibayar : <strong>Rp {{ number_format($paid,0,',','.') }}</strong><br>
    Sisa Pembayaran : <strong>Rp {{ number_format($sisa,0,',','.') }}</strong>
</div>

{{-- ================= CATATAN & BANK ================= --}}
<div class="box">
<div class="section-title">Catatan</div>
<div>{{ company()->invoice_footer }}</div>

<br>

<strong style="color:#425678;">Rekening Resmi:</strong>
<table>
@if($bank)
<tr><td width="110">Bank</td><td>: {{ $bank->bank_name }}</td></tr>
<tr><td>No Rekening</td><td>: {{ $bank->account_number }}</td></tr>
<tr><td>Atas Nama</td><td>: <strong>{{ $bank->account_name }}</strong></td></tr>
@else
<tr>
    <td colspan="2" style="color:#b91c1c;font-style:italic;">
        Rekening pembayaran belum dikonfigurasi
    </td>
</tr>
@endif
</table>
</div>

{{-- ================= FOOTER ================= --}}
<footer>
{{ now()->format('d F Y') }}<br><br>
<strong>{{ company()->signature_name }}</strong><br>
{{ company()->signature_position }}<br>
{{ company()->name }}
</footer>

</body>
</html>
