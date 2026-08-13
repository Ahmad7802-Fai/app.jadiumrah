<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Refund</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px; }
    </style>
</head>
<body>

<h3>Laporan Refund</h3>
<p>Periode: {{ $from }} s/d {{ $to }}</p>

<table>
    <thead>
        <tr>
            <th>Invoice</th>
            <th>PNR</th>
            <th>Tanggal Refund</th>
            <th>Nominal</th>
            <th>Status</th>
            <th>Alasan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($refunds as $row)
            <tr>
                <td>{{ $row->invoice_number }}</td>
                <td>{{ $row->pnr_code }}</td>
                <td>{{ $row->refunded_at }}</td>
                <td align="right">{{ number_format($row->amount) }}</td>
                <td>{{ $row->refund_status }}</td>
                <td>{{ $row->reason }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
