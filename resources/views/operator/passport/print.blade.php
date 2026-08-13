<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Passport Jamaah</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #111;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        td, th {
            padding: 6px;
            border: 1px solid #444;
            vertical-align: top;
        }

        /* ================= HEADER ================= */
        .header {
            width: 100%;
            margin-bottom: 18px;
            border-bottom: 2px solid #0A5C36;
            padding-bottom: 8px;
        }

        .logo {
            height: 60px;
        }

        .company-name {
            font-size: 16px;
            font-weight: 700;
            color: #0A5C36;
        }

        .company-info {
            font-size: 11px;
            color: #333;
        }

        /* ================= TITLE ================= */
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 10px;
            color: #333;
        }

        /* ================= FOOTER ================= */
        .sign {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>

<body>

{{-- ================= HEADER ================= --}}
<table class="header">
    <tr>
        <td width="25%" style="border:none;">
            @if(company()->logo)
                <img src="{{ public_path('storage/'.company()->logo) }}" class="logo">
            @endif
        </td>
        <td width="75%" style="border:none;">
            <div class="company-name">
                {{ company()->name }}
            </div>
            <div class="company-info">
                {!! nl2br(e(company()->address)) !!}<br>
                Telp: {{ company()->phone }}
            </div>
        </td>
    </tr>
</table>

{{-- ================= TITLE ================= --}}
<div class="title">
    FORM PASSPORT JAMAAH
</div>

<div class="subtitle">
    {{ company()->brand_name ?? company()->name }}
</div>

{{-- ================= DATA PASSPORT ================= --}}
<table>
    <tr>
        <th width="30%">Nama Jamaah</th>
        <td>{{ $passport->jamaah->nama_lengkap }}</td>
    </tr>

    <tr>
        <th>Nomor Paspor</th>
        <td>{{ $passport->nomor_paspor ?? '-' }}</td>
    </tr>

    <tr>
        <th>Tanggal Terbit</th>
        <td>
            {{ $passport->tanggal_terbit_paspor
                ? \Carbon\Carbon::parse($passport->tanggal_terbit_paspor)->format('d M Y')
                : '-' }}
        </td>
    </tr>

    <tr>
        <th>Tanggal Habis</th>
        <td>
            {{ $passport->tanggal_habis_paspor
                ? \Carbon\Carbon::parse($passport->tanggal_habis_paspor)->format('d M Y')
                : '-' }}
        </td>
    </tr>

    <tr>
        <th>Tempat Terbit</th>
        <td>{{ $passport->tempat_terbit_paspor ?? '-' }}</td>
    </tr>

    <tr>
        <th>Negara Penerbit</th>
        <td>{{ $passport->negara_penerbit ?? '-' }}</td>
    </tr>

    <tr>
        <th>Alamat Lengkap</th>
        <td>{{ $passport->alamat_lengkap ?? '-' }}</td>
    </tr>

    <tr>
        <th>Rekomendasi</th>
        <td>{{ $passport->rekomendasi_paspor ?? '-' }}</td>
    </tr>

    <tr>
        <th>Tujuan Imigrasi</th>
        <td>{{ $passport->tujuan_imigrasi ?? '-' }}</td>
    </tr>
</table>

{{-- ================= FOOTER / TTD ================= --}}
<div class="sign">
    {{ company()->city ?? 'Jakarta' }}, {{ date('d M Y') }} <br><br>
    <strong>{{ company()->signature_name ?? company()->name }}</strong><br>
    {{ company()->signature_position }}
</div>

</body>
</html>
