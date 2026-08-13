<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Trip Profit Report</title>
<style>
    body { font-family: sans-serif; font-size: 12px; }
    h2 { margin-bottom: 5px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #444; padding: 6px; }
    th { background: #0A6847; color: white; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .mb-2 { margin-bottom: 10px; }
    .summary-box { margin-top: 20px; border: 1px solid #ccc; padding: 10px; }
</style>
</head>

<body>

    {{-- HEADER --}}
    <h2>Laporan Trip Profit</h2>
    <div class="mb-2">
        Dicetak pada: {{ now()->format('d M Y H:i') }} <br>
        @if($paketId)
            Filter Paket: <strong>{{ $paketList->where('id', $paketId)->first()->nama_paket }}</strong>
        @else
            Semua Paket
        @endif
    </div>

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Kode Trip</th>
                <th>Paket</th>
                <th>Tanggal</th>
                <th>Pendapatan</th>
                <th>Biaya Trip</th>
                <th>Profit</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($items as $i => $row)
            <tr>
                <td class="text-center">{{ $i+1 }}</td>
                <td>{{ $row['kode'] }}</td>
                <td>{{ $row['paket'] }}</td>
                <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d M Y') }}</td>

                <td class="text-right">
                    Rp {{ number_format($row['revenue'],0,',','.') }}
                </td>

                <td class="text-right">
                    Rp {{ number_format($row['trip_cost'],0,',','.') }}
                </td>

                <td class="text-right" style="font-weight:bold;
                    color: {{ $row['profit'] >= 0 ? 'green' : 'red' }}">
                    Rp {{ number_format($row['profit'],0,',','.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
