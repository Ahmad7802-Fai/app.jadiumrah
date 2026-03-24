<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manifest</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        h1 { margin-bottom: 5px; }
        .info { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th { background: #f2f2f2; }
    </style>
</head>
<body>

<h1>MANIFEST JAMAAH</h1>

<div class="info">
    <strong>Paket:</strong> {{ $departure->paket->name }} <br>
    <strong>Departure Code:</strong> {{ $departure->departure_code ?? '-' }} <br>
    <strong>Tanggal Berangkat:</strong> {{ $departure->departure_date->format('d M Y') }} <br>
    <strong>Flight:</strong> {{ $departure->flight_number ?? '-' }} <br>
    <strong>Meeting Point:</strong> {{ $departure->meeting_point ?? '-' }} <br>
    <strong>Total Jamaah:</strong> {{ $jamaahs->count() }}
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama</th>
            <th>Gender</th>
            <th>Passport</th>
            <th>Cabang</th>
            <th>Agent</th>
        </tr>
    </thead>
    <tbody>
        @foreach($jamaahs as $jamaah)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $jamaah->jamaah_code }}</td>
            <td>{{ $jamaah->nama_lengkap }}</td>
            <td>{{ $jamaah->gender }}</td>
            <td>{{ $jamaah->passport_number ?? '-' }}</td>
            <td>{{ $jamaah->branch->name ?? '-' }}</td>
            <td>{{ $jamaah->agent->name ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>