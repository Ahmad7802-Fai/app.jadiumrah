<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Cashflow {{ $bulanNama }} {{ $tahun }}</title>

<style>
@page {
    size: 210mm 330mm;
    margin: 12mm;
}

@font-face {
    font-family: Arial;
    font-style: normal;
    font-weight: normal;
}

@font-face {
    font-family: Arial;
    font-style: normal;
    font-weight: bold;
}

* {
    font-family: Arial !important;
}

body {
    margin: 0;
    color: #222;
    font-size: 12.5px;
}


:root {
    --green:#0d3b2e;
    --line:#ddd;
    --muted:#666;
}

h1,h2,h3,h4,h5 {
    margin: 0;
    padding: 0;
}

.page {
    width: 100%;
}

/* ================= HEADER ================= */
.header {
    border: 1px solid var(--green);
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 10px;
}

.brand {
    font-size: 15px;
    font-weight: 800;
    color: var(--green);
    margin-top: 5px;
}

.subtitle {
    font-size: 11px;
    color: var(--muted);
}

.meta {
    width: 100%;
    font-size: 11px;
    margin-top: 6px;
}

.meta td {
    padding: 2px 0;
}

/* ================= SECTION ================= */
.section {
    border: 1px solid var(--green);
    border-radius: 6px;
    padding: 8px 10px;
    margin-bottom: 10px;
    page-break-inside: avoid;
}

.section-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--green);
    margin-bottom: 6px;
}

/* ================= TABLE ================= */
table {
    width: 100%;
    border-collapse: collapse;
}

td {
    padding: 4px 2px;
    border-bottom: 1px solid var(--line);
}

td:last-child {
    text-align: right;
}

.total {
    font-weight: 700;
    color: var(--green);
}

.negative {
    color: #b00020;
    font-weight: 700;
}

/* ================= FOOTER ================= */
.footer {
    margin-top: 12px;
    font-size: 10px;
    text-align: right;
    color: #555;
}
</style>
</head>

<body>
<div class="page">

<!-- ================= HEADER ================= -->
<div class="header">

    <table width="100%">
        <tr>
            <td width="65%">
                <div style="display:flex;align-items:center;gap:10px;">
                    @if($company && $company->logo)
                        <img
                            src="{{ public_path('storage/'.$company->logo) }}"
                            style="height:42px"
                        >
                    @endif

                    <div>
                        <div class="brand">
                            {{ $company->name }}
                        </div>
                        <div class="subtitle">
                            Laporan Arus Kas — {{ $bulanNama }} {{ $tahun }}
                        </div>
                    </div>
                </div>
            </td>

            <td width="35%">
                <table class="meta">
                    <tr>
                        <td>No. Laporan</td>
                        <td>: CASH/{{ $tahun }}/{{ str_pad($bulan,2,'0',STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td>Periode</td>
                        <td>: {{ $bulanNama }} {{ $tahun }}</td>
                    </tr>
                    <tr>
                        <td>Generated</td>
                        <td>: {{ date('d M Y H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>

<!-- ================= RINGKASAN ================= -->
<div class="section">
    <div class="section-title">Ringkasan Cashflow</div>
    <table>
        <tr>
            <td>Total Cash In</td>
            <td>Rp {{ number_format($totalCashIn,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Total Cash Out</td>
            <td class="negative">- Rp {{ number_format($totalCashOut,0,',','.') }}</td>
        </tr>
        <tr>
            <td><strong>Net Cashflow</strong></td>
            <td class="{{ $netCashflow >= 0 ? 'total' : 'negative' }}">
                {{ $netCashflow >= 0
                    ? 'Rp '.number_format($netCashflow,0,',','.')
                    : '- Rp '.number_format(abs($netCashflow),0,',','.') }}
            </td>
        </tr>
    </table>
</div>

<!-- ================= CASH IN ================= -->
<div class="section">
    <div class="section-title">Detail Cash In</div>
    <table>
        <tr>
            <td>Pemasukan Jamaah</td>
            <td>Rp {{ number_format($cashIn['jamaah'],0,',','.') }}</td>
        </tr>
        <tr>
            <td>Pemasukan Layanan</td>
            <td>Rp {{ number_format($cashIn['layanan'],0,',','.') }}</td>
        </tr>
        <tr>
            <td><strong>Total Cash In</strong></td>
            <td class="total">Rp {{ number_format($cashIn['total'],0,',','.') }}</td>
        </tr>
    </table>
</div>

<!-- ================= CASH OUT ================= -->
<div class="section">
    <div class="section-title">Detail Cash Out</div>
    <table>
        <tr>
            <td>Biaya Trip</td>
            <td>Rp {{ number_format($cashOut['trip'],0,',','.') }}</td>
        </tr>
        <tr>
            <td>Biaya Vendor</td>
            <td>Rp {{ number_format($cashOut['vendor'],0,',','.') }}</td>
        </tr>
        <tr>
            <td>Biaya Operasional</td>
            <td>Rp {{ number_format($cashOut['operational'],0,',','.') }}</td>
        </tr>
        <tr>
            <td>Biaya Marketing</td>
            <td>Rp {{ number_format($cashOut['marketing'],0,',','.') }}</td>
        </tr>
        <tr>
            <td><strong>Total Cash Out</strong></td>
            <td class="total">Rp {{ number_format($cashOut['total'],0,',','.') }}</td>
        </tr>
    </table>
</div>

<div class="footer">
    Dicetak otomatis oleh Sistem Keuangan — {{ date('d M Y H:i') }}
</div>

</div>
</body>
</html>
