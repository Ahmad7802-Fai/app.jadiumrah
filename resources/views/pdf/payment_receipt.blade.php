<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; }
        .row { margin-bottom: 6px; }
    </style>
</head>
<body>

<h1>BUKTI PEMBAYARAN</h1>

<div class="row">
<strong>PNR:</strong> {{ $payment->invoice->pnr->pnr_code }}
</div>

<div class="row">
<strong>Invoice:</strong> {{ $payment->invoice->invoice_number }}
</div>

<div class="row">
<strong>Tanggal:</strong> {{ $payment->payment_date }}
</div>

<div class="row">
<strong>Jumlah:</strong> @money($payment->amount)
</div>

<div class="row">
<strong>Status:</strong> {{ $payment->status }}
</div>

</body>
</html>
