<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kwitansi — {{ $invoice->nomor_invoice }}</title>

    <style>
        @page { size: A4 portrait; margin: 10mm 9mm; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9.5px;
            color: #425678;
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
            color: #425678;
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
            background: #425678;
            color: #fff;
            font-weight: bold;
        }

        .noborder td {
            border: none !important;
            padding: 2px 0 !important;
        }

        .logo { height: 55px; }

        .footer {
            text-align: right;
            margin-top: 18px;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
@php
    // Ambil rekening default untuk invoice
    $bank = companyBank('invoice');
@endphp

{{-- ========================= HEADER ========================= --}}
<div class="card">
    <table width="100%">
        <tr valign="top">

            {{-- LEFT --}}
            <td width="40%" style="border:none;">
                @if(company()->logo_invoice)
                    <img src="{{ public_path('storage/'.company()->logo_invoice) }}" class="logo"><br>
                @endif

                <div style="font-size:13px; font-weight:700; color: #425678;">
                    {{ company()->brand_name ?? company()->name }}
                </div>

                <div style="font-size:8.8px; line-height:12px; color:#444;">
                    {!! nl2br(e(company()->address)) !!}
                </div>
            </td>

            {{-- MIDDLE --}}
            <td width="35%" style="border:none; font-size:9px;">
                <table class="noborder">
                    <tr>
                        <td style="font-weight:bold;">No Invoice</td>
                        <td>: {{ $invoice->nomor_invoice }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Tanggal</td>
                        <td>: {{ date('d M Y', strtotime($invoice->tanggal)) }}</td>
                    </tr>
                </table>
            </td>

            {{-- RIGHT --}}
            <td width="25%" style="text-align:right; border:none;">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}"
                     style="width:80px; height:80px;">
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
                <strong style="font-size:10.5px; color: #425678;">
                    {{ $jamaah->nama_lengkap }}
                </strong><br>

                <span style="font-size:8.8px;">
                    No ID : {{ $jamaah->no_id }}<br>
                    Paket : {{ $jamaah->paketMaster->nama_paket ?? '-' }}<br>
                    Kamar : {{ ucfirst($jamaah->tipe_kamar) ?? '-' }}
                </span>
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

{{-- ========================= CATATAN & REKENING ========================= --}}
<div class="card">
    <div class="title">Catatan</div>

    <div style="font-size:8.8px; line-height:13px;">
        {{ company()->invoice_footer }}
    </div>

    <br>

    <div style="font-size:9px; font-weight:700; color: #425678;">
        Rekening Pembayaran Resmi
    </div>

    <table class="noborder" style="font-size:8.5px; margin-top:4px;">
        @if($bank)
            <tr>
                <td width="28%">Bank</td>
                <td>: {{ $bank->bank_name }}</td>
            </tr>
            <tr>
                <td>No. Rekening</td>
                <td>: {{ $bank->account_number }}</td>
            </tr>
            <tr>
                <td>Atas Nama</td>
                <td>: <strong>{{ $bank->account_name }}</strong></td>
            </tr>
        @else
            <tr>
                <td colspan="2" style="color:#b91c1c; font-style:italic;">
                    Rekening pembayaran belum dikonfigurasi oleh admin
                </td>
            </tr>
        @endif
    </table>
</div>

{{-- ========================= FOOTER ========================= --}}
<div class="footer">
    {{ date('d F Y') }}<br><br>
    <strong>{{ company()->signature_name }}</strong><br>
    {{ company()->signature_position }}<br>
    {{ company()->name }}
</div>

</body>
</html>
