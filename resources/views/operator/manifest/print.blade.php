<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manifest Jamaah Umrah</title>

    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 13px;
            margin: 25px;
        }

        h2, h3, h4 {
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .title {
            margin-top: 10px;
            font-size: 20px;
            font-weight: bold;
        }

        .sub-title {
            text-align: center;
            margin-top: 3px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 13px;
        }

        th {
            background: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .info {
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 11px;
            color: #555;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <h2 class="title">MANIFEST JAMAAH UMRAH</h2>
    <div class="sub-title">
        Keberangkatan: <strong>{{ $keberangkatan->kode_keberangkatan }}</strong><br>
        Paket: <strong>{{ $keberangkatan->paket->nama_paket ?? '-' }}</strong><br>
        Tanggal Berangkat: 
        <strong>{{ \Carbon\Carbon::parse($keberangkatan->tanggal_berangkat)->format('d M Y') }}</strong><br>
        Durasi: <strong>{{ $durasi }} Hari</strong>
    </div>

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama Jamaah</th>
                <th style="width: 90px;">Tipe Kamar</th>
                <th style="width: 100px;">Nomor Kamar</th>
                <th style="width: 100px;">Kode Keberangkatan</th>
                <th style="width: 110px;">Tanggal</th>
            </tr>
        </thead>

        <tbody>
            @foreach($manifests as $m)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $m->jamaah->nama_lengkap }}</td>

                <td class="text-center">
                    {{ ucfirst($m->tipe_kamar) }}
                </td>

                <td class="text-center">
                    {{ $m->nomor_kamar }}
                </td>

                <td class="text-center">
                    {{ $keberangkatan->kode_keberangkatan }}
                </td>

                <td class="text-center">
                    {{ \Carbon\Carbon::parse($keberangkatan->tanggal_berangkat)->format('d M Y') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        Dicetak pada {{ date('d M Y H:i') }} <br>
        © {{ date('Y') }} JadiUmrah.com — All Rights Reserved
    </div>

</body>
</html>
