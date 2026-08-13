<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekap Tabungan {{ $periodeText }}</title>

<style>
@page { margin: 14mm 12mm; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 9.5px;
    color: #111;
}

/* ================= HEADER ================= */
.header-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}

.header-table td {
    vertical-align: middle;
}

.logo {
    width: 60px;
}

.logo img {
    width: 52px;
}

.header-text .title {
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .4px;
}

.header-text .subtitle {
    font-size: 9px;
    color: #555;
    margin-top: 2px;
}

.header-text .company {
    font-size: 9px;
    color: #333;
    margin-top: 3px;
}

/* ================= SUMMARY ================= */
.summary {
    margin: 8px 0 10px;
    border: 1px solid #d8d8d8;
    padding: 6px 8px;
}

.summary table {
    width: 100%;
    border-collapse: collapse;
    font-size: 9px;
}

.summary td {
    padding: 3px 4px;
    white-space: nowrap;
}

/* ================= TABLE ================= */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #cfcfcf;
    padding: 4px 4px;
}

th {
    background: #f2f2f2;
    font-size: 8.8px;
    text-transform: uppercase;
}

td {
    font-size: 9px;
}

tfoot td {
    font-weight: 700;
    background: #fafafa;
}

.text-right { text-align: right; }
.text-center { text-align: center; }

/* ================= FOOTER ================= */
.footer {
    margin-top: 10px;
    font-size: 8px;
    color: #555;
    border-top: 1px dashed #ccc;
    padding-top: 6px;
    text-align: center;
}
</style>
</head>
<body>

{{-- ================= HEADER ================= --}}
<table class="header-table">
    <tr>
        {{-- LOGO --}}
        <td class="logo">
            @if(company()->logo)
                <img src="{{ public_path('storage/'.company()->logo) }}">
            @endif
        </td>

        {{-- TEXT --}}
        <td class="header-text">
            <div class="title">REKAP BULANAN TABUNGAN UMRAH</div>
            <div class="subtitle">Periode {{ $periodeText }}</div>
            <div class="company">
                {{ company()->name }}
                @if(company()->brand_name)
                    • {{ company()->brand_name }}
                @endif
            </div>
        </td>
    </tr>
</table>

{{-- ================= SUMMARY ================= --}}
<div class="summary">
    <table>
        <tr>
            <td>Saldo Awal</td>
            <td class="text-right">Rp {{ number_format($summary['saldo_awal'],0,',','.') }}</td>

            <td>Total Top Up</td>
            <td class="text-right">Rp {{ number_format($summary['topup'],0,',','.') }}</td>

            <td>Total Debit</td>
            <td class="text-right">Rp {{ number_format($summary['debit'],0,',','.') }}</td>

            <td>Saldo Akhir</td>
            <td class="text-right">
                <strong>Rp {{ number_format($summary['saldo_akhir'],0,',','.') }}</strong>
            </td>
        </tr>
    </table>
</div>

{{-- ================= TABLE ================= --}}
<table>
    <thead>
        <tr>
            <th width="28">#</th>
            <th>Nama Jamaah</th>
            <th width="90">No. Tabungan</th>
            <th class="text-right">Saldo Awal</th>
            <th class="text-right">Top Up</th>
            <th class="text-right">Debit</th>
            <th class="text-right">Saldo Akhir</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekap as $i => $r)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $r['jamaah']->nama_lengkap ?? '-' }}</td>
            <td>{{ $r['tabungan']->nomor_tabungan }}</td>
            <td class="text-right">Rp {{ number_format($r['saldo_awal'],0,',','.') }}</td>
            <td class="text-right">Rp {{ number_format($r['total_topup'],0,',','.') }}</td>
            <td class="text-right">Rp {{ number_format($r['total_debit'],0,',','.') }}</td>
            <td class="text-right">
                <strong>Rp {{ number_format($r['saldo_akhir'],0,',','.') }}</strong>
            </td>
        </tr>
        @endforeach
    </tbody>

    {{-- ================= TOTAL ================= --}}
    <tfoot>
        <tr>
            <td colspan="3" class="text-center">TOTAL</td>
            <td class="text-right">Rp {{ number_format($summary['saldo_awal'],0,',','.') }}</td>
            <td class="text-right">Rp {{ number_format($summary['topup'],0,',','.') }}</td>
            <td class="text-right">Rp {{ number_format($summary['debit'],0,',','.') }}</td>
            <td class="text-right">
                Rp {{ number_format($summary['saldo_akhir'],0,',','.') }}
            </td>
        </tr>
    </tfoot>
</table>

{{-- ================= FOOTER ================= --}}
<div class="footer">
    Dokumen ini dihasilkan otomatis oleh sistem keuangan
    {{ company()->brand_name ?? company()->name }}.<br>
    Digunakan untuk keperluan internal, audit, dan pelaporan resmi.
</div>

</body>
</html>
