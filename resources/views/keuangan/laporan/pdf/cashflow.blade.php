<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Cashflow</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 20px 35px;
            font-size: 12px;
            color: #222;
        }

        h2, h4, h3 {
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header .title {
            font-size: 22px;
            font-weight: bold;
            color: #0A6847;
        }

        .sub-title {
            font-size: 13px;
            color: #444;
            margin-top: 3px;
        }

        .section-title {
            background: #0A6847;
            color: white;
            padding: 6px 10px;
            font-size: 13px;
            margin-top: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table th {
            background: #EEE;
            padding: 6px;
            border: 1px solid #CCC;
            font-size: 12px;
        }

        table td {
            padding: 6px;
            border: 1px solid #CCC;
            font-size: 12px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .success { color: #0A6847; }
        .danger { color: #C0392B; }
        .summary-box {
            margin-top: 15px;
            padding: 10px;
            background: #F5F5F5;
            border: 1px solid #DDD;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #777;
        }
    </style>
</head>

<body>

    {{-- ========================== HEADER ============================= --}}
    <div class="header">
        <div class="title">LAPORAN ARUS KAS (CASHFLOW)</div>
        <div class="sub-title">
            Periode: {{ date('d M Y', strtotime($from)) }} — {{ date('d M Y', strtotime($to)) }}
        </div>
    </div>

    {{-- ========================== SUMMARY ============================= --}}
    <div class="summary-box">
        <table width="100%">
            <tr>
                <td><strong>Total Pemasukan</strong></td>
                <td class="text-right success">
                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td><strong>Total Pengeluaran</strong></td>
                <td class="text-right danger">
                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td><strong>Saldo Akhir</strong></td>
                <td class="text-right {{ $saldo >= 0 ? 'success' : 'danger' }}">
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>


    {{-- ========================== PEMASUKAN ============================= --}}
    <div class="section-title">PEMASUKAN</div>

    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="20%">Tanggal</th>
                <th>Sumber</th>
                <th width="25%" class="text-right">Jumlah</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($cashIn as $i => $r)
            <tr>
                <td class="text-center">{{ $i+1 }}</td>
                <td>{{ date('d M Y', strtotime($r->tanggal)) }}</td>
                <td>{{ $r->sumber ?? 'Pembayaran Jamaah' }}</td>
                <td class="text-right success">
                    Rp {{ number_format($r->jumlah, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Tidak ada data pemasukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>


    {{-- ========================== PENGELUARAN ============================= --}}
    <div class="section-title">PENGELUARAN</div>

    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="20%">Tanggal</th>
                <th>Kategori</th>
                <th width="25%" class="text-right">Jumlah</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($cashOut as $i => $r)
            <tr>
                <td class="text-center">{{ $i+1 }}</td>
                <td>{{ date('d M Y', strtotime($r->tanggal)) }}</td>
                <td>{{ $r->kategori }}</td>
                <td class="text-right danger">
                    Rp {{ number_format($r->jumlah, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Tidak ada data pengeluaran.</td>
            </tr>
            @endforelse
        </tbody>
    </table>


    {{-- ========================== FOOTER ============================= --}}
    <div class="footer">
        Dicetak otomatis oleh Sistem Keuangan — {{ date('d M Y H:i') }}
    </div>

</body>
</html>
