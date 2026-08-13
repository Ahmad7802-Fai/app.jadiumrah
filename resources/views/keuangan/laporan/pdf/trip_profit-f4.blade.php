<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">

<title>Trip Profit — JadiUmrah</title>

<style>
    @page { margin: 0; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        margin: 0;
        padding: 0;
        color: #222;
        font-size: 11.5px;
    }

    :root {
        --ju-green: #0A6847;
        --ju-red: #C1121F;
        --muted: #666;
        --border: #dcdcdc;
    }

    /* FIX: width aman untuk F4 portrait */
    .page {
        width: 180mm;
        padding: 10mm 12mm;
        box-sizing: border-box;
    }

    /* HEADER */
    .header {
        border: 1px solid var(--ju-green);
        padding: 10px 12px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .brand {
        font-size: 20px;
        font-weight: 900;
        color: var(--ju-green);
        margin-bottom: 3px;
    }
    .muted { color: var(--muted); font-size: 11px; }

    /* TABLE */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
        font-size: 11.5px;
    }
    th, td {
        padding: 5px 6px;
        border-bottom: 1px solid #eee;
    }
    th {
        background: #f9f9f9;
        color: #333;
        font-weight: 700;
        border-bottom: 1px solid var(--border);
    }
    .text-end { text-align:right; }
    .text-green { color:var(--ju-green); font-weight:700; }
    .text-red { color:var(--ju-red); font-weight:700; }

    footer {
        margin-top: 12px;
        text-align: right;
        font-size: 10.5px;
        color: #444;
    }

</style>
</head>
<body>
<div class="page">

    <div class="header">
        <div>
            <div class="brand">jadiumrah.com</div>
            <div class="muted">Laporan Trip Profit</div>
            <div class="muted">Generated: {{ date('d M Y H:i') }}</div>
        </div>
        <div class="muted">
            Laporan ID: TRIPPROFIT/{{ date('Ym') }}<br>
            Halaman 1/1
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="12%">Kode</th>
                <th>Paket</th>
                <th width="18%">Tanggal</th>
                <th width="17%" class="text-end">Revenue</th>
                <th width="17%" class="text-end">Trip Cost</th>
                <th width="17%" class="text-end">Profit</th>
            </tr>
        </thead>

        <tbody>
        @foreach($keberangkatan as $i => $row)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $row->kode_keberangkatan ?? '-' }}</td>
                <td>{{ $row->paket }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal_berangkat)->format('d M Y') }}</td>
                <td class="text-end">Rp {{ number_format($row->revenue,0,',','.') }}</td>
                <td class="text-end">Rp {{ number_format($row->trip_cost,0,',','.') }}</td>
                <td class="text-end">
                    <span class="{{ $row->profit >= 0 ? 'text-green':'text-red' }}">
                        Rp {{ number_format($row->profit,0,',','.') }}
                    </span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <footer>
        PT. JADI UMRAH INDONESIA — Dicetak otomatis
    </footer>

</div>
</body>
</html>
