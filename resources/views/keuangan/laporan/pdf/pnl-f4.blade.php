<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>PNL {{ $bulanNama }} {{ $tahun }}</title>

<style>
@page { size: 210mm 330mm; margin: 12mm; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 11.5px;
    color: #222;
    margin: 0;
}

:root {
    --green:#0d3b2e;
    --line:#e0e0e0;
    --muted:#666;
}

table { width:100%; border-collapse:collapse; }
td { padding:4px 4px; vertical-align:top; }

.header {
    border:1px solid var(--green);
    border-radius:6px;
    padding:8px;
    margin-bottom:8px;
}

.company-name {
    font-size:10px;
    font-weight:bold;
    color:var(--green);
    margin-top:4px;
}

.subtitle {
    font-size:10.5px;
    color:var(--muted);
}

.meta td {
    font-size:10.5px;
    padding:2px 0;
}

.section {
    border:1px solid var(--green);
    border-radius:6px;
    padding:7px 9px;
    margin-bottom:8px;
}

.section-title {
    font-size:12px;
    font-weight:bold;
    color:var(--green);
    margin-bottom:4px;
}

.line td {
    border-bottom:1px solid var(--line);
}

.right { text-align:right; }

.total {
    font-weight:bold;
    color:var(--green);
}

.negative {
    font-weight:bold;
    color:#b00020;
}

.footer {
    margin-top:10px;
    font-size:9.5px;
    text-align:right;
    color:#555;
}
</style>
</head>

<body>
<div class="page">

{{-- ================= HEADER ================= --}}
<div class="header">
<table>
<tr>

    {{-- LOGO + COMPANY --}}
    <td width="40%">
        @if($company && $company->logo)
            <img src="{{ public_path('storage/'.$company->logo) }}" style="height:36px">
        @endif
        <div class="company-name">{{ $company->name }}</div>
    </td>

    {{-- TITLE --}}
    <td>
        <div style="font-weight:bold;color:var(--green);">
            Laporan Laba Rugi (PNL)
        </div>
        <div class="subtitle">
            Periode {{ $bulanNama }} {{ $tahun }}
        </div>
    </td>

    {{-- META --}}
    <td width="30%">
        <table class="meta">
            <tr><td>No</td><td>: PNL/{{ $tahun }}/{{ str_pad($bulan,2,'0',STR_PAD_LEFT) }}</td></tr>
            <tr><td>Periode</td><td>: {{ $bulanNama }} {{ $tahun }}</td></tr>
            <tr><td>Generated</td><td>: {{ date('d M Y H:i') }}</td></tr>
        </table>
    </td>

</tr>
</table>
</div>

{{-- ================= RINGKASAN ================= --}}
<div class="section">
<div class="section-title">Ringkasan Keuangan</div>
<table>
<tr class="line"><td>Pendapatan Jamaah</td><td class="right">Rp {{ number_format($revenueJamaah,0,',','.') }}</td></tr>
<tr class="line"><td>Pendapatan Layanan</td><td class="right">Rp {{ number_format($revenueLayanan,0,',','.') }}</td></tr>
<tr class="line"><td><strong>Total Pendapatan</strong></td><td class="right total">Rp {{ number_format($totalRevenue,0,',','.') }}</td></tr>

<tr class="line"><td>Biaya Trip</td><td class="right">- Rp {{ number_format($tripExpenses,0,',','.') }}</td></tr>
<tr class="line"><td>Biaya Vendor</td><td class="right">- Rp {{ number_format($vendorExpenses,0,',','.') }}</td></tr>
<tr class="line"><td><strong>Total HPP</strong></td><td class="right negative">- Rp {{ number_format($hpp,0,',','.') }}</td></tr>

<tr class="line"><td>Biaya Operasional</td><td class="right">- Rp {{ number_format($operational,0,',','.') }}</td></tr>
<tr class="line"><td>Biaya Marketing</td><td class="right">- Rp {{ number_format($marketing,0,',','.') }}</td></tr>
</table>
</div>

{{-- ================= LABA RUGI ================= --}}
<div class="section">
<div class="section-title">Perhitungan Laba Rugi</div>
<table>
<tr class="line"><td>Total Pendapatan</td><td class="right">Rp {{ number_format($totalRevenue,0,',','.') }}</td></tr>
<tr class="line"><td>Total HPP</td><td class="right">- Rp {{ number_format($hpp,0,',','.') }}</td></tr>

<tr class="line">
    <td><strong>Laba Kotor</strong></td>
    <td class="right {{ $grossProfit >= 0 ? 'total':'negative' }}">
        Rp {{ number_format($grossProfit,0,',','.') }}
    </td>
</tr>

<tr class="line">
    <td><strong>Laba Bersih</strong></td>
    <td class="right {{ $netProfit >= 0 ? 'total':'negative' }}">
        Rp {{ number_format($netProfit,0,',','.') }}
    </td>
</tr>
</table>
</div>

<div class="footer">
    Dicetak otomatis oleh Sistem Keuangan — {{ $company->name }}
</div>

</div>
</body>
</html>
