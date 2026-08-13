<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $bukti->nomor_bukti }}</title>

<style>
@page { margin: 13mm 14mm; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 9.6px;
    color: #111;
    line-height: 1.45;
}

/* ================= HEADER ================= */
.header {
    margin-bottom: 6px;
}

.header img {
    width: 50px;
    height: auto;
    display: block;
}

.company-name {
    margin-top: 4px;
    font-size: 12.8px;
    font-weight: 700;
    letter-spacing: .3px;
    text-transform: uppercase;
}

.company-meta {
    margin-top: 2px;
    font-size: 8.5px;
    color: #555;
    line-height: 1.4;
}

.divider {
    border-bottom: 1px solid #000;
    margin: 8px 0 10px;
}

/* ================= TITLE ================= */
.title {
    text-align: center;
    font-size: 12.5px;
    font-weight: 700;
    letter-spacing: .4px;
    margin-bottom: 2px;
}

.subtitle {
    text-align: center;
    font-size: 8.7px;
    color: #555;
    margin-bottom: 10px;
}

/* ================= TABLE ================= */
table {
    width: 100%;
    border-collapse: collapse;
}

td {
    padding: 2.5px 2px;
    vertical-align: top;
}

.label {
    width: 42%;
    color: #555;
}

.value {
    font-weight: 600;
}

/* ================= AMOUNT ================= */
.amount-box {
    margin: 10px 0 8px;
    padding: 7px 9px;
    border: 1px solid #000;
    text-align: center;
}

.amount-box .text {
    font-size: 8.6px;
    color: #555;
    letter-spacing: .3px;
}

.amount-box .amount {
    font-size: 15.5px;
    font-weight: 700;
    margin-top: 3px;
}

/* ================= SIGN ================= */
.signature {
    margin-top: 16px;
    width: 40%;
    text-align: center;
}

.signature .name {
    margin-top: 38px;
    font-weight: 700;
    font-size: 9.6px;
}

.signature .role {
    font-size: 8.4px;
    color: #555;
}

/* ================= FOOTER ================= */
.footer {
    margin-top: 14px;
    font-size: 8.1px;
    color: #555;
    border-top: 1px dashed #aaa;
    padding-top: 6px;
}

.hash {
    margin-top: 4px;
    font-size: 7.6px;
    word-break: break-all;
    color: #777;
}
</style>
</head>

<body>

{{-- ================= HEADER ================= --}}
<div class="header">

    @if(company()->logo)
        <img src="{{ public_path('storage/'.company()->logo) }}">
    @endif

    <div class="company-name">
        {{ company()->name }}
    </div>

    <div class="company-meta">
        {!! nl2br(e(company()->address)) !!}
        @if(company()->phone)
            <br>Telp: {{ company()->phone }}
        @endif
        @if(company()->website)
            <br>{{ company()->website }}
        @endif
    </div>

</div>

<div class="divider"></div>

{{-- ================= TITLE ================= --}}
<div class="title">KWITANSI SETORAN TABUNGAN UMRAH</div>
<div class="subtitle">Tanda Terima Resmi Dana Jamaah</div>

{{-- ================= DATA ================= --}}
<table>
    <tr>
        <td class="label">Nomor Kwitansi</td>
        <td class="value">: {{ $bukti->nomor_bukti }}</td>
    </tr>
    <tr>
        <td class="label">Tanggal Setoran</td>
        <td class="value">
            : {{ \Carbon\Carbon::parse($bukti->tanggal_setoran)->format('d F Y') }}
        </td>
    </tr>
    <tr>
        <td class="label">Nama Jamaah</td>
        <td class="value">: {{ $bukti->jamaah->nama_lengkap ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Nomor Tabungan</td>
        <td class="value">: {{ $bukti->tabungan->nomor_tabungan ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Saldo Sebelum Setoran</td>
        <td class="value">
            : Rp {{ number_format(
                ($bukti->tabunganTransaksi->saldo_setelah ?? 0) - ($bukti->nominal ?? 0),
                0, ',', '.'
            ) }}
        </td>
    </tr>
    <tr>
        <td class="label">Saldo Setelah Setoran</td>
        <td class="value">
            : Rp {{ number_format(
                $bukti->tabunganTransaksi->saldo_setelah ?? 0,
                0, ',', '.'
            ) }}
        </td>
    </tr>
</table>

{{-- ================= AMOUNT ================= --}}
<div class="amount-box">
    <div class="text">TELAH DITERIMA DANA SEBESAR</div>
    <div class="amount">
        Rp {{ number_format($bukti->nominal, 0, ',', '.') }}
    </div>
</div>

<table>
    <tr>
        <td class="label">Disetujui Oleh</td>
        <td class="value">: {{ $bukti->approver->name ?? 'Admin Keuangan' }}</td>
    </tr>
    <tr>
        <td class="label">Tanggal Persetujuan</td>
        <td class="value">
            : {{ \Carbon\Carbon::parse($bukti->approved_at)->format('d F Y H:i') }}
        </td>
    </tr>
</table>

{{-- ================= SIGN ================= --}}
<div class="signature">
    <div class="name">({{ $bukti->approver->name ?? 'Admin Keuangan' }})</div>
    <div class="role">Keuangan</div>
</div>

{{-- ================= FOOTER ================= --}}
<div class="footer">
    Dokumen ini adalah <strong>kwitansi resmi</strong> tanda terima dana oleh
    {{ company()->brand_name ?? company()->name }}.
    Dokumen ini <strong>bukan bukti pelunasan paket umrah</strong>
    dan tidak dapat dipindahtangankan.
    <div class="hash">
        Hash Dokumen: {{ $bukti->hash }}
    </div>
</div>

</body>
</html>
