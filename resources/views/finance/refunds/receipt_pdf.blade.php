<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Refund Receipt</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { margin-bottom: 30px; }
        .title { font-size: 20px; font-weight: bold; }
        .meta { margin-top: 5px; color: #555; }
        .section { margin-top: 25px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px 0; }
        .total {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            margin-top: 50px;
            font-size: 11px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="title">REFUND RECEIPT</div>
    <div class="meta">
        No: {{ $refund->refund_receipt_number }}<br>
        Tanggal: {{ $refund->approved_at->format('d M Y') }}
    </div>
</div>

<div class="section">
    <strong>Data Booking</strong>
    <table>
        <tr>
            <td width="150">Booking Code</td>
            <td>: {{ $refund->booking->booking_code ?? '-' }}</td>
        </tr>
        <tr>
            <td>Paket</td>
            <td>: {{ $refund->booking->paket->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Cabang</td>
            <td>: {{ $refund->booking->branch->name ?? '-' }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <strong>Detail Refund</strong>
    <table>
        <tr>
            <td width="150">Payment</td>
            <td>: {{ $refund->payment->payment_code }}</td>
        </tr>
        <tr>
            <td>Jumlah Refund</td>
            <td>: Rp {{ number_format($refund->amount,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Alasan</td>
            <td>: {{ $refund->reason ?? '-' }}</td>
        </tr>
    </table>

    <div class="total">
        Total Refund: Rp {{ number_format($refund->amount,0,',','.') }}
    </div>
</div>

<div class="footer">
    Dokumen ini di-generate otomatis oleh sistem.<br>
    Tidak memerlukan tanda tangan basah.
</div>

</body>
</html>