<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Biaya Keberangkatan — {{ $paket->nama_paket }}</title>

<style>
@page { margin: 14mm 14mm; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 10.8px;
    color: #1f2937;
}

/* ================= COLOR ================= */
:root {
    --primary: #425678;
    --border: #cbd5e1;
    --muted: #6b7280;
    --danger: #b91c1c;
}

/* ================= HEADER ================= */
.header {
    display: table;
    width: 100%;
    border-bottom: 2px solid var(--primary);
    padding-bottom: 8px;
    margin-bottom: 14px;
}

.header .logo {
    display: table-cell;
    width: 80px;
    vertical-align: middle;
}

.header .logo img {
    max-width: 70px;
}

.header .company {
    display: table-cell;
    vertical-align: middle;
    padding-left: 10px;
}

.header .company .name {
    font-size: 15px;
    font-weight: 700;
    color: var(--primary);
}

.header .company .meta {
    font-size: 9.5px;
    color: var(--muted);
    line-height: 1.4;
}

/* ================= TITLE ================= */
.title {
    text-align: center;
    font-size: 14px;
    font-weight: 700;
    margin: 8px 0 12px;
    letter-spacing: .4px;
}

/* ================= INFO ================= */
.info {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
    font-size: 10.5px;
}

.info td {
    padding: 3px 4px;
}

.info .label {
    width: 150px;
    color: var(--muted);
}

/* ================= DATA TABLE ================= */
table.data {
    width: 100%;
    border-collapse: collapse;
}

table.data th,
table.data td {
    border: 1px solid var(--border);
    padding: 5px 6px;
    font-size: 10.5px;
}

table.data th {
    background: var(--primary);
    color: #fff;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 9.8px;
}

.text-right { text-align: right; }

/* ================= TOTAL ================= */
.total {
    margin-top: 10px;
    font-size: 11.5px;
    font-weight: 700;
    text-align: right;
}

.total span {
    color: var(--danger);
}

/* ================= SIGN ================= */
.signature {
    margin-top: 36px;
    text-align: right;
    font-size: 10.5px;
}

.signature .name {
    margin-top: 46px;
    font-weight: 700;
}
</style>
</head>
<body>

{{-- ================= HEADER ================= --}}
<div class="header">
    <div class="logo">
        @if(company()?->logo)
            <img src="{{ public_path('storage/'.company()->logo) }}">
        @endif
    </div>

    <div class="company">
        <div class="name">
            {{ company()->name }}
        </div>
        <div class="meta">
            {!! nl2br(e(company()->address)) !!}<br>
            {{ company()->phone }} · {{ company()->email }}<br>
            {{ company()->website }}
        </div>
    </div>
</div>

{{-- ================= TITLE ================= --}}
<div class="title">
    LAPORAN BIAYA KEBERANGKATAN
</div>

{{-- ================= INFO ================= --}}
<table class="info">
    <tr>
        <td class="label">Nama Paket</td>
        <td>: {{ $paket->nama_paket }}</td>
    </tr>
    <tr>
        <td class="label">Hotel</td>
        <td>: {{ $paket->hotel_mekkah }} — {{ $paket->hotel_madinah }}</td>
    </tr>
    <tr>
        <td class="label">Total Jamaah</td>
        <td>: <strong>{{ $totalJamaah }} Orang</strong></td>
    </tr>
    <tr>
        <td class="label">Tanggal Laporan</td>
        <td>: {{ now()->translatedFormat('d F Y') }}</td>
    </tr>
</table>

{{-- ================= TABLE ================= --}}
<table class="data">
    <thead>
        <tr>
            <th width="35">#</th>
            <th>Kategori</th>
            <th width="85">Tanggal</th>
            <th>Catatan</th>
            <th width="120" class="text-right">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $row->kategori }}</td>
            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
            <td>{{ $row->catatan ?: '-' }}</td>
            <td class="text-right">
                Rp {{ number_format($row->jumlah,0,',','.') }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center; color:#6b7280;">
                Belum ada data biaya
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- ================= TOTAL ================= --}}
<div class="total">
    TOTAL PENGELUARAN :
    <span>
        Rp {{ number_format($totalPengeluaran,0,',','.') }}
    </span>
</div>

{{-- ================= SIGN ================= --}}
<div class="signature">
    {{ company()->city ?? 'Jakarta' }},
    {{ now()->translatedFormat('d F Y') }}<br>

    <div class="name">
        {{ company()->signature_name ?? 'Finance Manager' }}<br>
        {{ company()->signature_position ?? 'Finance Manager' }}
    </div>
</div>

</body>
</html>
