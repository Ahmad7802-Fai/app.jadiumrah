<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Jamaah - {{ $jamaah->nama_lengkap }}</title>

<style>
@page { margin: 14mm 14mm; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 9px;
    color: #111;
}

:root {
    --green: #425678;
    --border: #425678;
}

/* ================= HEADER ================= */
.header {
    width: 100%;
    border-bottom: 1.2px solid var(--green);
    margin-bottom: 6px;
}

.logo { 
    width: 100px; 
    height: auto;
}

.title {
    font-size: 12px;
    font-weight: 800;
    color: var(--green);
}

.subtitle {
    font-size: 8.6px;
    color: #333;
}

/* ================= SECTION ================= */
.section {
    margin-top: 6px;
}

.section-title {
    background: var(--green);
    color: #fff;
    font-size: 9.2px;
    font-weight: bold;
    padding: 3px 5px;
    margin-bottom: 2px;
}

/* ================= TABLE ================= */
table {
    width: 100%;
    border-collapse: collapse;
}

td {
    border: 0.5px solid var(--border);
    padding: 2px 4px;
    vertical-align: top;
    font-size: 8.8px;
}

.label {
    width: 32%;
    color: #333;
}

.value {
    font-weight: 600;
}

/* ================= PHOTO ================= */
.photo {
    width: 65px;
    height: 85px;
    object-fit: cover;
    border: 1px solid var(--green);
}

/* ================= FOOTER ================= */
.footer {
    margin-top: 8px;
    font-size: 7.8px;
    text-align: center;
    color: #444;
}
</style>
</head>

<body>

{{-- ================= HEADER ================= --}}
<table class="header">
<tr>
    <td width="20%" style="border:none">
        @if(company()->logo)
            <img src="{{ public_path('storage/'.company()->logo) }}" class="logo">
        @endif
    </td>
    <td width="60%" align="center" style="border:none">
        <div class="title">DATA JAMAAH UMRAH</div>
        <div class="subtitle">{{ company()->name }}</div>
    </td>
    <td width="20%" style="border:none"></td>
</tr>
</table>

{{-- ================= IDENTITAS ================= --}}
<div class="section">
<div class="section-title">IDENTITAS JAMAAH</div>

<table>
<tr>
    <td rowspan="5" width="70" align="center">
        <img src="{{ $jamaah->foto
            ? public_path('storage/'.$jamaah->foto)
            : public_path('noimage.jpg') }}" class="photo">
    </td>
    <td class="label">No ID</td>
    <td class="value">{{ $jamaah->no_id }}</td>
</tr>
<tr>
    <td class="label">Nama Lengkap</td>
    <td class="value">{{ $jamaah->nama_lengkap }}</td>
</tr>
<tr>
    <td class="label">Nama Paspor</td>
    <td class="value">{{ $jamaah->nama_passport ?? '-' }}</td>
</tr>
<tr>
    <td class="label">NIK</td>
    <td class="value">{{ $jamaah->nik }}</td>
</tr>
<tr>
    <td class="label">Jenis Kelamin</td>
    <td class="value">{{ $jamaah->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
</tr>
</table>
</div>

{{-- ================= DATA PRIBADI ================= --}}
<div class="section">
<div class="section-title">DATA PRIBADI</div>

<table>
<tr>
    <td class="label">Tempat / Tgl Lahir</td>
    <td class="value">
        {{ $jamaah->tempat_lahir }},
        {{ \Carbon\Carbon::parse($jamaah->tanggal_lahir)->format('d M Y') }}
        ({{ $jamaah->usia }} th)
    </td>
</tr>
<tr>
    <td class="label">Status Pernikahan</td>
    <td class="value">{{ ucfirst($jamaah->status_pernikahan) }}</td>
</tr>
<tr>
    <td class="label">Nama Ayah</td>
    <td class="value">{{ $jamaah->nama_ayah }}</td>
</tr>
<tr>
    <td class="label">Pendidikan</td>
    <td class="value">{{ $jamaah->pendidikan_terakhir ?? '-' }}</td>
</tr>
<tr>
    <td class="label">Pekerjaan</td>
    <td class="value">{{ $jamaah->pekerjaan ?? '-' }}</td>
</tr>
</table>
</div>

{{-- ================= KESEHATAN ================= --}}
<div class="section">
<div class="section-title">KESEHATAN & KEBUTUHAN KHUSUS</div>

<table>
<tr>
    <td class="label">Pernah Umrah</td>
    <td class="value">{{ $jamaah->pernah_umroh }}</td>
</tr>
<tr>
    <td class="label">Pernah Haji</td>
    <td class="value">{{ $jamaah->pernah_haji }}</td>
</tr>
<tr>
    <td class="label">Penyakit Khusus</td>
    <td class="value">
        {{ $jamaah->penyakit_khusus }}
        @if($jamaah->penyakit_khusus === 'Ya')
            ({{ $jamaah->nama_penyakit }})
        @endif
    </td>
</tr>
<tr>
    <td class="label">Kursi Roda</td>
    <td class="value">{{ $jamaah->kursi_roda }}</td>
</tr>
<tr>
    <td class="label">Butuh Pendampingan</td>
    <td class="value">{{ $jamaah->butuh_penanganan_khusus }}</td>
</tr>
</table>
</div>

{{-- ================= UMRAH ================= --}}
<div class="section">
<div class="section-title">INFORMASI UMRAH</div>

<table>
<tr>
    <td class="label">Paket</td>
    <td class="value">{{ $jamaah->nama_paket ?? $jamaah->paket }}</td>
</tr>

<tr>
    <td class="label">Agent</td>
    <td class="value">
        {{ $jamaah->agent->nama ?? '-' }}
        @if($jamaah->agent?->kode_agent)
            ({{ $jamaah->agent->kode_agent }})
        @endif
    </td>
</tr>


<tr>
    <td class="label">Keberangkatan</td>
    <td class="value">
        {{ $jamaah->keberangkatan->kode_keberangkatan ?? '-' }}
        @if($jamaah->keberangkatan)
            — {{ \Carbon\Carbon::parse($jamaah->keberangkatan->tanggal_berangkat)->format('d M Y') }}
        @endif
    </td>
</tr>

<tr>
    <td class="label">Tipe Jamaah</td>
    <td class="value">{{ ucfirst($jamaah->tipe_jamaah) }}</td>
</tr>

<tr>
    <td class="label">Tipe Kamar</td>
    <td class="value">{{ ucfirst($jamaah->tipe_kamar) }}</td>
</tr>

<tr>
    <td class="label">Sumber</td>
    <td class="value">{{ ucfirst($jamaah->source) }}</td>
</tr>
</table>
</div>

{{-- ================= FOOTER ================= --}}
<div class="footer">
    Dokumen resmi data jamaah • {{ company()->brand_name ?? company()->name }}<br>
    Dicetak {{ now()->translatedFormat('d F Y H:i') }}
</div>

</body>
</html>
