<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Payment</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px; }
    </style>
</head>
<body>

<h3>Laporan Payment</h3>
<p>Periode: {{ $from }} s/d {{ $to }}</p>

<table>
    <thead>
        <tr>
            <th>Invoice</th>
            <th>PNR</th>
            <th>Tanggal</th>
            <th>Nominal</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $row)
            <tr>
                <td>{{ $row->invoice_number }}</td>
                <td>{{ $row->pnr_code }}</td>
                <td>{{ $row->payment_date }}</td>
                <td align="right">{{ number_format($row->amount) }}</td>
                <td>{{ $row->invoice_status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
