<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Detail Jamaah</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
        }

        h1 {
            font-size: 16px;
            margin-bottom: 6px;
        }

        .muted {
            color: #666;
            font-size: 10px;
        }

        .section {
            margin-top: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>Detail Jamaah Umrah</h1>
<p class="muted">
    Dicetak: {{ tanggal_indo(now(), true) }}
</p>

<div class="section">
    <strong>Data Jamaah</strong>
    <table>
        <tr><th>No ID</th><td>{{ $jamaah->no_id }}</td></tr>
        <tr><th>Nama</th><td>{{ $jamaah->nama_lengkap }}</td></tr>
        <tr><th>No HP</th><td>{{ $jamaah->no_hp }}</td></tr>
        <tr><th>TTL</th>
            <td>{{ $jamaah->tempat_lahir }}, {{ tanggal_indo($jamaah->tanggal_lahir) }}</td>
        </tr>
        <tr><th>Jenis Kelamin</th>
            <td>{{ $jamaah->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <strong>Paket Umrah</strong>
    <table>
        <tr>
            <th>Nama Paket</th>
            <td>{{ $jamaah->paketMaster->nama_paket ?? '-' }}</td>
        </tr>
        <tr>
            <th>Harga</th>
            <td>Rp {{ number_format($jamaah->harga_akhir) }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <strong>Tabungan</strong>
    <table>
        <tr>
            <th>Nomor Tabungan</th>
            <td>{{ $jamaah->tabungan->nomor_tabungan ?? '-' }}</td>
        </tr>
        <tr>
            <th>Saldo</th>
            <td>Rp {{ number_format($jamaah->tabungan->saldo ?? 0) }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <strong>Riwayat Pembayaran</strong>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nominal</th>
                <th>Metode</th>
            </tr>
        </thead>
        <tbody>
        @forelse($jamaah->payments as $p)
            <tr>
                <td>{{ tanggal_indo($p->tanggal_bayar) }}</td>
                <td>Rp {{ number_format($p->jumlah) }}</td>
                <td>{{ $p->metode }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="text-align:center">
                    Tidak ada pembayaran
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

</body>
</html>
