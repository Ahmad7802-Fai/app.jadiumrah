<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Jamaah - {{ $jamaah->nama_lengkap }}</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5px;
            color: #111;
            margin: 20px 24px;
        }

        :root {
            --green: #0A5C36;
            --border: #2e4d3a;
        }

        /* ================= HEADER ================= */
        .header {
            width: 100%;
            border-bottom: 1.2px solid var(--green);
            margin-bottom: 10px;
        }

        .logo {
            width: 90px;
        }

        .doc-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--green);
        }

        .doc-sub {
            font-size: 10px;
            color: #333;
        }

        /* ================= WATERMARK ================= */
        .watermark {
            position: fixed;
            top: 45%;
            left: 20%;
            font-size: 60px;
            opacity: 0.05;
            transform: rotate(-30deg);
            font-weight: bold;
            color: var(--green);
            z-index: -1;
        }

        /* ================= TABLE ================= */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        th, td {
            border: 0.6px solid var(--border);
            padding: 4px 6px;
            vertical-align: top;
        }

        .section-title {
            background: var(--green);
            color: #fff;
            font-weight: bold;
            font-size: 11px;
            padding: 4px 6px;
            border: 1px solid var(--border);
            margin-top: 12px;
            margin-bottom: 4px;
        }

        /* ================= FOTO ================= */
        .photo {
            width: 75px;
            height: 95px;
            object-fit: cover;
            border: 1px solid var(--green);
            border-radius: 3px;
        }

        /* ================= SIGN ================= */
        .sign {
            margin-top: 24px;
            text-align: right;
            font-size: 10px;
        }

        .qr {
            width: 80px;
            margin-top: 6px;
        }

        /* ================= FOOTER ================= */
        .footer {
            position: fixed;
            bottom: 6px;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #444;
        }
    </style>
</head>

<body>

<div class="watermark">jadiumrah.com</div>

{{-- ================= HEADER ================= --}}
<table class="header">
    <tr>
        <td width="20%">
            <img src="{{ public_path('logo.png') }}" class="logo">
        </td>
        <td width="60%" align="center">
            <div class="doc-title">DATA JAMAAH UMRAH</div>
            <div class="doc-sub">PT GLOBALINDO SUKSES MAKSIMA</div>
        </td>
        <td width="20%"></td>
    </tr>
</table>

{{-- ================= IDENTITAS ================= --}}
<div class="section-title">Identitas Jamaah</div>

<table>
    <tr>
        <td rowspan="4" width="90" align="center">
            <img src="{{ $jamaah->foto
                ? public_path('storage/'.$jamaah->foto)
                : public_path('noimage.jpg') }}"
                 class="photo">
        </td>
        <td width="30%"><strong>No ID</strong></td>
        <td>{{ $jamaah->no_id }}</td>
    </tr>
    <tr>
        <td><strong>Nama Lengkap</strong></td>
        <td>{{ $jamaah->nama_lengkap }}</td>
    </tr>
    <tr>
        <td><strong>NIK</strong></td>
        <td>{{ $jamaah->nik }}</td>
    </tr>
    <tr>
        <td><strong>Tempat / Tgl Lahir</strong></td>
        <td>
            {{ $jamaah->tempat_lahir }},
            {{ \Carbon\Carbon::parse($jamaah->tanggal_lahir)->format('d M Y') }}
        </td>
    </tr>
</table>

{{-- ================= UMRAH ================= --}}
<div class="section-title">Informasi Umrah</div>

<table>
    <tr>
        <td width="30%"><strong>Paket</strong></td>
        <td>{{ $jamaah->paket }}</td>
    </tr>
    <tr>
        <td><strong>Keberangkatan</strong></td>
        <td>
            {{ $jamaah->keberangkatan->kode_keberangkatan ?? '-' }}
            @if($jamaah->keberangkatan)
                — {{ \Carbon\Carbon::parse($jamaah->keberangkatan->tanggal_berangkat)->format('d M Y') }}
            @endif
        </td>
    </tr>
    <tr>
        <td><strong>Tipe Kamar</strong></td>
        <td>{{ ucfirst($jamaah->tipe_kamar) }}</td>
    </tr>
    <tr>
        <td><strong>No HP</strong></td>
        <td>{{ $jamaah->no_hp }}</td>
    </tr>
</table>

{{-- ================= TTD ================= --}}
<div class="sign">
    Jakarta, {{ date('d M Y') }} <br><br>
    <strong>PT GLOBALINDO SUKSES MAKSIMA</strong><br>
    @if(!empty($qrCode))
        <img src="{{ $qrCode }}" class="qr">
    @endif
</div>

<div class="footer">
    Dokumen resmi jamaah • jadiumrah.com
</div>

</body>
</html>
