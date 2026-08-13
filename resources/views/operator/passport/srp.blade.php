<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Surat Rekomendasi Paspor</title>

<style>
    body {
        font-family: 'Times New Roman', serif;
        margin: 15px 35px;
        font-size: 14px;
        line-height: 1.42;
        position: relative;
    }

    table { width: 100%; border-collapse: collapse; }

    .header-table td { vertical-align: top; }

    .kop-title {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        letter-spacing: .3px;
        padding-top: 5px;
        text-transform: uppercase;
    }

    .line {
        border-bottom: 1.8px solid #444;
        margin-top: 6px;
        margin-bottom: 10px;
    }

    .watermark {
        position: absolute;
        top: 32%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.06;
        z-index: -1;
    }

    .info-table td {
        padding: 3px 0;
        vertical-align: top;
        font-size: 14px;
    }

    .info-left { width: 170px; }

    .alamat-block {
        display: block;
        margin-top: -2px;
        line-height: 1.42;
    }

    .qr {
        width: 90px;
        margin-bottom: 5px;
        opacity: .94;
    }

    .ttd {
        margin-top: 35px;
        text-align: right;
    }

    .footer {
        font-size: 11px;
        text-align: center;
        margin-top: 25px;
        padding-top: 5px;
        border-top: 1px solid #555;
        letter-spacing: .2px;
    }
</style>
</head>
<body>

{{-- WATERMARK --}}
@if(company()->logo_bw)
    <img src="{{ public_path('storage/'.company()->logo_bw) }}" class="watermark" width="330">
@endif

{{-- HEADER --}}
<table class="header-table">
    <tr>
        <td width="22%">
            @if(company()->logo)
                <img src="{{ public_path('storage/'.company()->logo) }}" width="90">
            @endif
        </td>

        <td width="56%" class="kop-title">
            SURAT REKOMENDASI PEMBUATAN PASPOR
            <div class="line"></div>
        </td>

        <td width="22%" align="right">
            @if(company()->logo_invoice)
                <img src="{{ public_path('storage/'.company()->logo_invoice) }}" width="90">
            @endif
        </td>
    </tr>
</table>

{{-- NOMOR --}}
<table style="margin-bottom: 8px;">
    <tr>
        <td style="width: 80px;">Nomor</td>
        <td>: {{ $nomorSurat }}</td>
    </tr>
    <tr>
        <td>Hal</td>
        <td>: Permohonan Rekomendasi Pembuatan Paspor</td>
    </tr>
</table>

{{-- TUJUAN IMIGRASI --}}
<p style="margin-top: 6px;">
    Kepada Yth,<br>
    <strong>Kepala Kantor Imigrasi {{ ucwords(strtolower($passport->tujuan_imigrasi)) }}</strong><br>
    di<br>
    <strong>{{ strtoupper($passport->tujuan_imigrasi) }}</strong>
</p>

<p>Assalamu’alaikum Warahmatullahi Wabarakatuh</p>
<p>Dengan hormat,</p>

<p>
    Dengan ini kami pihak <strong>{{ company()->name }}</strong> memberikan rekomendasi kepada:
</p>

{{-- BIODATA JAMAAH --}}
<table class="info-table">
    <tr>
        <td class="info-left">Nama</td>
        <td>: {{ $passport->jamaah->nama_lengkap }}</td>
    </tr>

    <tr>
        <td class="info-left">NIK</td>
        <td>: {{ $passport->jamaah->nik }}</td>
    </tr>

    <tr>
        <td class="info-left">Tempat / Tgl Lahir</td>
        <td>
            : {{ $passport->jamaah->tempat_lahir }},
            {{ \Carbon\Carbon::parse($passport->jamaah->tanggal_lahir)->translatedFormat('d F Y') }}
        </td>
    </tr>

    <tr>
        <td class="info-left" style="padding-top: 5px;">Alamat</td>
        <td>:
            <span class="alamat-block">
                {{ $passport->alamat_lengkap ?? '-' }}<br>
                Kec. {{ $passport->kecamatan ?? '-' }}, {{ $passport->kota ?? '-' }}<br>
                {{ $passport->provinsi ?? '-' }} {{ $passport->kode_pos ?? '' }}
            </span>
        </td>
    </tr>
</table>

<br>

<p>
    Untuk keperluan pembuatan paspor guna perjalanan ibadah Umrah,
    kami menjamin bahwa yang bersangkutan akan mematuhi seluruh peraturan keimigrasian
    dan kembali ke Indonesia setelah ibadah selesai.
</p>

<p>
    Demikian surat rekomendasi ini dibuat untuk dapat digunakan sebagaimana mestinya.
</p>

<p>Wassalamu’alaikum Warahmatullahi Wabarakatuh</p>

{{-- TTD --}}
<div class="ttd">
    {{ company()->city ?? 'Jakarta' }},
    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br><br>

    <img src="data:image/png;base64,{{ $qrCode }}" class="qr"><br>

    <strong>{{ company()->signature_name }}</strong><br>
    {{ company()->signature_position }}
</div>

{{-- FOOTER --}}
<div class="footer">
    {{ company()->name }} — Kantor Pusat<br>
    {!! nl2br(e(company()->address)) !!}<br>
    Telp: {{ company()->phone }}
    @if(company()->email)
        · Email: {{ company()->email }}
    @endif
    @if(company()->website)
        · Website: {{ company()->website }}
    @endif
</div>

</body>
</html>
