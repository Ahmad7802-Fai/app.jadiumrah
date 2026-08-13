<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Mutasi Tabungan</title>

<style>
@page { margin: 16mm 14mm; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 10px;
    color: #111;
}

/* ================= HEADER ================= */
.header-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
    border-bottom: 1px solid #ccc;
}

.header-table td {
    vertical-align: middle;
    padding-bottom: 8px;
}

.logo {
    width: 60px;
}

.logo img {
    width: 52px;
}

.header-text .title {
    font-size: 14px;
    font-weight: bold;
}

.header-text .subtitle {
    font-size: 10px;
    color: #555;
    margin-top: 2px;
}

.header-text .company {
    font-size: 9px;
    color: #333;
    margin-top: 2px;
}

/* ================= META ================= */
table.meta {
    width: 100%;
    margin: 10px 0 14px;
    font-size: 9px;
}

table.meta td {
    padding: 2px 0;
}

/* ================= TABLE ================= */
table.data {
    width: 100%;
    border-collapse: collapse;
}

table.data th,
table.data td {
    border: 1px solid #ccc;
    padding: 6px 5px;
}

table.data th {
    background: #f3f4f6;
    font-size: 9px;
}

table.data tfoot th {
    background: #f9fafb;
    font-weight: bold;
}

.text-right { text-align: right; }
.text-center { text-align: center; }

.kredit { color: #0a7a3c; }
.debit  { color: #b91c1c; }

/* ================= FOOTER ================= */
.footer {
    margin-top: 14px;
    font-size: 8px;
    color: #555;
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
            <div class="title">MUTASI TABUNGAN UMRAH</div>
            <div class="subtitle">Periode {{ $periode }}</div>
            <div class="company">
                {{ company()->name }}
                @if(company()->brand_name)
                    • {{ company()->brand_name }}
                @endif
            </div>
        </td>
    </tr>
</table>

{{-- ================= META ================= --}}
<table class="meta">
    <tr>
        <td width="30%">Nama Jamaah</td>
        <td>: {{ $tabungan->jamaah->nama_lengkap }}</td>
    </tr>
    <tr>
        <td>Nomor Tabungan</td>
        <td>: {{ $tabungan->nomor_tabungan }}</td>
    </tr>
</table>

{{-- ================= TABLE ================= --}}
<table class="data">
    <thead>
        <tr>
            <th width="18%">Tanggal</th>
            <th>Keterangan</th>
            <th width="14%" class="text-right">Debit</th>
            <th width="14%" class="text-right">Kredit</th>
            <th width="14%" class="text-right">Saldo</th>
        </tr>
    </thead>

    <tbody>
        @php
            $saldo = $saldoAwal;
            $totalDebit = 0;
            $totalKredit = 0;
        @endphp

        @foreach($mutasi as $m)
            @php
                $isDebit  = in_array($m->jenis, ['TARIK','TRANSFER_INVOICE']);
                $isKredit = $m->jenis === 'TOPUP';

                if ($isKredit) {
                    $saldo += $m->amount;
                    $totalKredit += $m->amount;
                }

                if ($isDebit) {
                    $saldo -= $m->amount;
                    $totalDebit += $m->amount;
                }
            @endphp
            <tr>
                <td>{{ $m->created_at->format('d M Y H:i') }}</td>
                <td>{{ $m->keterangan }}</td>
                <td class="text-right debit">
                    {{ $isDebit ? number_format($m->amount,0,',','.') : '-' }}
                </td>
                <td class="text-right kredit">
                    {{ $isKredit ? number_format($m->amount,0,',','.') : '-' }}
                </td>
                <td class="text-right">
                    {{ number_format($saldo,0,',','.') }}
                </td>
            </tr>
        @endforeach
    </tbody>

    {{-- ================= TOTAL ================= --}}
    <tfoot>
        <tr>
            <th colspan="2" class="text-right">TOTAL</th>
            <th class="text-right debit">
                {{ $totalDebit ? number_format($totalDebit,0,',','.') : '-' }}
            </th>
            <th class="text-right kredit">
                {{ $totalKredit ? number_format($totalKredit,0,',','.') : '-' }}
            </th>
            <th class="text-right">
                {{ number_format($saldo,0,',','.') }}
            </th>
        </tr>
    </tfoot>
</table>

{{-- ================= FOOTER ================= --}}
<div class="footer">
    Dokumen ini merupakan mutasi resmi tabungan jamaah
    {{ company()->brand_name ?? company()->name }}.<br>
    Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}.
</div>

</body>
</html>
