<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            height: 60px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #ccc;
        }
        table {
            width: 100%;
            margin-bottom: 10px;
        }
        td {
            padding: 5px 0;
        }
        .right {
            text-align: right;
        }
        .signature {
            margin-top: 30px;
            text-align: right;
        }
        .signature div {
            margin-top: 60px;
            font-weight: bold;
        }
        .box {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <img src="{{ public_path('logo.png') }}" alt="Logo">
        <div class="title">KWITANSI PEMBAYARAN</div>
        <div style="font-size: 12px;">Dicetak pada: {{ $tanggalPrint }}</div>
    </div>

    <!-- JAMAAH & INVOICE INFO -->
    <div class="section-title">Informasi Invoice</div>
    <table>
        <tr>
            <td>No Invoice</td>
            <td>: <strong>{{ $invoice->nomor_invoice }}</strong></td>
        </tr>
        <tr>
            <td>Nama Jamaah</td>
            <td>: {{ $jamaah->nama_lengkap }} ({{ $jamaah->no_id }})</td>
        </tr>
        <tr>
            <td>Total Tagihan</td>
            <td>: Rp {{ number_format($totalTagihan,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Total Terbayar</td>
            <td>: Rp {{ number_format($totalTerbayar,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Sisa Tagihan</td>
            <td>: Rp {{ number_format($sisaTagihan,0,',','.') }}</td>
        </tr>
    </table>

    <!-- DETAIL PEMBAYARAN -->
    <div class="section-title">Detail Pembayaran</div>
    <table class="box">
        <tr>
            <td>Metode</td>
            <td>: {{ strtoupper($payment->metode) }}</td>
        </tr>
        <tr>
            <td>Jumlah Dibayar</td>
            <td>: <strong>Rp {{ number_format($payment->jumlah,0,',','.') }}</strong></td>
        </tr>
        <tr>
            <td>Tanggal Bayar</td>
            <td>: {{ date('d M Y', strtotime($payment->tanggal_bayar)) }}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>: {{ $payment->keterangan ?? '-' }}</td>
        </tr>
    </table>

    <!-- SIGNATURE -->
    <div class="signature">
        <div>Admin Keuangan</div>
    </div>

</body>
</html>
