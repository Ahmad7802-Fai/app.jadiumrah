<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi Pembayaran #{{ $payment->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; color:#222; font-size:13px; }
        .header { display:flex; align-items:center; justify-content:space-between; }
        .company { text-align:left; }
        .title { text-align:center; font-weight:700; font-size:18px; margin-top:8px; margin-bottom:16px; }
        table { width:100%; border-collapse:collapse; margin-bottom:12px;}
        .t td { padding:6px; vertical-align:top; }
        .line { border-top:1px solid #ddd; margin:12px 0; }
        .right { text-align:right; }
        .big { font-size:16px; font-weight:700; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">
            <strong>PT. Globalindo Sukses Maksima</strong><br>
            Jl. Contoh Alamat No. 123, Kota — Telp. (031) 000000
        </div>
        <div>
            <img src="{{ public_path('path/to/logo-gsm.png') }}" style="height:60px;">
        </div>
    </div>

    <div class="title">KWITANSI PEMBAYARAN</div>

    <table class="t">
        <tr><td width="160">No Kwitansi</td><td>: KW/{{ $payment->id }}/{{ date('Y') }}</td></tr>
        <tr><td>Tanggal</td><td>: {{ optional($payment->tanggal_bayar)->format('d M Y H:i') }}</td></tr>
        <tr><td>Nama</td><td>: {{ $payment->jamaah->nama_lengkap ?? '-' }} ({{ $payment->jamaah->no_id ?? '' }})</td></tr>
        <tr><td>Invoice</td><td>: {{ $payment->invoice->nomor_invoice ?? '-' }}</td></tr>
        <tr><td>Metode</td><td>: {{ ucfirst($payment->metode) }}</td></tr>
        <tr><td>Jumlah</td><td>: Rp {{ number_format($payment->jumlah) }}</td></tr>
        <tr><td>Keterangan</td><td>: {{ $payment->keterangan ?? '-' }}</td></tr>
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td class="right" width="60%">
                <div class="big">Rp {{ number_format($payment->jumlah) }}</div>
                <div class="small text-muted">Terbilang: {{-- optionally inject terbilang helper --}}</div>
            </td>
            <td style="text-align:center;">
                <div>Malang, {{ date('d M Y') }}</div>
                <div style="margin-top:40px;"><strong>AHMAD FAIZI KAMIL</strong><br>Direktur Utama</div>
            </td>
        </tr>
    </table>

</body>
</html>
