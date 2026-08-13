<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daftar Jamaah</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f2f2f2; }
        h3 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>

<h3>Daftar Jamaah</h3>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Jamaah</th>
            <th>NIK</th>
            <th>Keberangkatan</th>
            <th>Paket</th>
            <th>Kamar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($jamaahs as $j)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $j->nama_lengkap }}</td>
            <td>{{ $j->nik }}</td>
            <td>{{ $j->keberangkatan->kode_keberangkatan ?? '-' }}</td>
            <td>{{ $j->paket }}</td>
            <td>{{ strtoupper($j->tipe_kamar) }}</td>
        </tr>
        @endforeach
        </tbody>

</table>

</body>
</html>
