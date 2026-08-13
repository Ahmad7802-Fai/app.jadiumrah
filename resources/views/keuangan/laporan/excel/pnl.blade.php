<table>
    <tr>
        <th colspan="2">Laporan Laba Rugi (PNL)</th>
    </tr>
    <tr>
        <td>Periode</td>
        <td>{{ $from }} - {{ $to }}</td>
    </tr>

    <tr>
        <th>Pendapatan</th>
        <th>Jumlah</th>
    </tr>
    <tr>
        <td>Pembayaran Jamaah</td>
        <td>{{ $revenues }}</td>
    </tr>

    <tr>
        <th>Pengeluaran</th>
        <th></th>
    </tr>
    <tr>
        <td>Pengeluaran Trip</td>
        <td>{{ $tripExpenses }}</td>
    </tr>
    <tr>
        <td>Pengeluaran Operasional</td>
        <td>{{ $operational }}</td>
    </tr>
</table>
