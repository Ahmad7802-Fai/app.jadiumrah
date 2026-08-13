<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        table { width:100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border:1px solid #ccc; padding:6px; }
        th { background:#f5f5f5; }
        .right { text-align:right; }
    </style>
</head>
<body>

<h1>INVOICE</h1>

<p>
<strong>Invoice:</strong> {{ $invoice->invoice_number }}<br>
<strong>PNR:</strong> {{ $invoice->pnr->pnr_code }}<br>
<strong>Tanggal:</strong> {{ $invoice->invoice_date }}
</p>

<table>
<thead>
<tr>
    <th>Description</th>
    <th>Qty</th>
    <th class="right">Subtotal</th>
</tr>
</thead>
<tbody>
@foreach($invoice->items as $item)
<tr>
    <td>{{ $item->description }}</td>
    <td>{{ $item->qty }}</td>
    <td class="right">@money($item->subtotal)</td>
</tr>
@endforeach
</tbody>
</table>

<p class="right">
<strong>Total:</strong> @money($invoice->total_amount)<br>
<strong>Paid:</strong> @money($invoice->paid_amount)<br>
<strong>Outstanding:</strong> @money($invoice->outstanding_amount)
</p>

</body>
</html>
