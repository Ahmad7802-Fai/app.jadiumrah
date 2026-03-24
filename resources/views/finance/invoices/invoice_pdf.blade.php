<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $booking->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
        .box { border: 1px solid #ddd; padding: 10px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td {
            border: 1px solid #ddd;
            padding: 6px;
        }
        .text-right { text-align: right; }
        .total { font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>

<div class="header">
    <div class="title">INVOICE</div>
    <div>No: {{ $booking->invoice_number }}</div>
    <div>Tanggal: {{ now()->format('d M Y') }}</div>
</div>

<div class="box">
    <strong>Booking Code:</strong> {{ $booking->booking_code ?? '-' }}<br>
    <strong>Paket:</strong> {{ $booking->paket->name ?? '-' }}<br>
    <strong>Keberangkatan:</strong> {{ optional($booking->departure)->departure_date }}
</div>

<h4>Detail Jamaah</h4>

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Room</th>
            <th class="text-right">Harga</th>
        </tr>
    </thead>
    <tbody>
        @foreach($booking->jamaahs as $jamaah)
            <tr>
                <td>{{ $jamaah->name }}</td>
                <td>{{ $jamaah->pivot->room_type }}</td>
                <td class="text-right">
                    Rp {{ number_format($jamaah->pivot->price,0,',','.') }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>

<table>
    <tr>
        <td class="text-right total">
            TOTAL: Rp {{ number_format($booking->total_amount,0,',','.') }}
        </td>
    </tr>
</table>

</body>
</html>