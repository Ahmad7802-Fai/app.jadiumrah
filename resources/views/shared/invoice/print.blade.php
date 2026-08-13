<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice — {{ $invoice->nomor_invoice }}</title>

    <style>
        @page { size: A4 portrait; margin: 10mm 9mm; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9.5px;
            color: #1d1d1d;
            margin: 0;
        }

        .card {
            border: 1px solid #c9d8d3;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .title {
            font-weight: 700;
            color: #0c5b45;
            margin-bottom: 4px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #cbd7d3;
            padding: 4px 5px;
        }

        th {
            background: #0c5b45;
            color: #fff;
            font-weight: bold;
            font-size: 9px;
        }

        .noborder td {
            border: none !important;
            padding: 2px 0 !important;
        }

        .footer {
            text-align: right;
            margin-top: 18px;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>

{{-- ========================= HEADER ========================= --}}
<div class="card">
    <table width="100%">
        <tr valign="top">

            {{-- LEFT --}}
            <td width="40%" style="border:none;">
                <img src="{{ public_path('logo.png') }}" style="height:55px; margin-bottom:4px;">
                <div style="font-size:13px; font-weight:700; color:#0c5b45;">
                    {{ $company['name'] }}
                </div>
                <div style="font-size:8.8px; line-height:12px; color:#444;">
                    {!! nl2br(e($company['address'])) !!}
                </div>
            </td>

            {{-- MIDDLE --}}
            <td width="35%" style="border:none; font-size:9px;">
                <table class="noborder">
                    <tr>
                        <td><strong>No Invoice</strong></td>
                        <td>: {{ $invoice->nomor_invoice }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal</strong></td>
                        <td>: {{ date('d M Y', strtotime($invoice->tanggal)) }}</td>
                    </tr>
                </table>
            </td>

            {{-- RIGHT --}}
            <td width="25%" style="border:none; text-align:right;">
                <img
                    src="data:image/svg+xml;base64,{{ $qrCode }}"
                    style="width:80px; height:80px;"
                >
            </td>

        </tr>
    </table>
</div>

{{-- ========================= DATA JAMAAH ========================= --}}
<div class="card">
    <div class="title">Data Jamaah</div>

    <table class="noborder">
        <tr valign="top">
            <td width="53%">
                <div style="font-weight:700; font-size:10.5px; color:#0c5b45;">
                    {{ $jamaah->nama_lengkap }}
                </div>
                <div style="font-size:8.8px; line-height:12px;">
                    No ID : {{ $jamaah->no_id }}<br>
                    Paket : {{ $jamaah->paketMaster->nama_paket ?? '-' }}<br>
                    Kamar : {{ ucfirst($jamaah->tipe_kamar) ?? '-' }}
                </div>
            </td>

            <td width="47%">
                <table class="noborder">
                    <tr><td>Total</td><td><b>Rp {{ number_format($invoice->total_tagihan) }}</b></td></tr>
                    <tr><td>Terbayar</td><td><b>Rp {{ number_format($invoice->total_terbayar) }}</b></td></tr>
                    <tr><td>Sisa</td><td><b>Rp {{ number_format($invoice->sisa_tagihan) }}</b></td></tr>
                    <tr><td>Status</td><td><b>{{ strtoupper($invoice->status) }}</b></td></tr>
                </table>
            </td>
        </tr>
    </table>
</div>

{{-- ========================= RINCIAN TAGIHAN ========================= --}}
<div class="card">
    <div class="title">Rincian Tagihan</div>

    <table>
        <thead>
            <tr>
                <th>Tgl Daftar</th>
                <th>Nama</th>
                <th>Paket</th>
                <th>Total</th>
                <th>Terbayar</th>
                <th>Sisa</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ date('d M Y', strtotime($jamaah->created_at)) }}</td>
                <td>{{ $jamaah->nama_lengkap }}</td>
                <td>{{ $jamaah->paketMaster->nama_paket ?? '-' }}</td>
                <td>Rp {{ number_format($invoice->total_tagihan) }}</td>
                <td>Rp {{ number_format($invoice->total_terbayar) }}</td>
                <td>Rp {{ number_format($invoice->sisa_tagihan) }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ========================= HISTORI PEMBAYARAN ========================= --}}
<div class="card">
    <div class="title">Histori Pembayaran</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Metode</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($history as $i => $row)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ date('d M Y H:i', strtotime($row->tanggal_bayar)) }}</td>
                <td>{{ strtoupper($row->metode) }}</td>
                <td>Rp {{ number_format($row->jumlah) }}</td>
                <td>{{ $row->keterangan ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ========================= CATATAN ========================= --}}
{{-- ========================= CATATAN ========================= --}}
<div class="card">
    <div class="title">Catatan</div>

    <div style="font-size:8.8px; line-height:12px; color:#555; margin-bottom:6px;">
        Dokumen ini merupakan bukti transaksi resmi dan sah.
    </div>

    @if(!empty($company['bank']))
        <div style="font-size:9px; font-weight:700; color:#0c5b45; margin-bottom:3px;">
            Informasi Rekening Pembayaran
        </div>

        <table style="font-size:8.5px; line-height:12px;">
            <tr>
                <td width="70">Bank</td>
                <td>: {{ $company['bank']['name'] }}</td>
            </tr>
            <tr>
                <td>No. Rek</td>
                <td>: {{ $company['bank']['number'] }}</td>
            </tr>
            <tr>
                <td>Atas Nama</td>
                <td>: <strong>{{ $company['bank']['owner'] }}</strong></td>
            </tr>
        </table>
    @endif
</div>



{{-- ========================= FOOTER ========================= --}}
<div class="footer">
    {{ date('d F Y') }}<br>
    <strong>{{ $company['name'] }}</strong>
    @if(!empty($company['website']))
        <br>{{ $company['website'] }}
    @endif
</div>

</body>
</html>
