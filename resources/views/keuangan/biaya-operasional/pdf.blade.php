<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Biaya Operasional</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header img {
            height: 70px;
            margin-bottom: 5px;
        }

        .alamat {
            font-size: 11px;
            color: #555;
            margin-top: -8px;
            margin-bottom: 20px;
        }

        h2, h4 {
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th {
            background: #0A6847;
            color: #fff;
            padding: 8px;
            border: 1px solid #ccc;
            font-size: 12px;
        }

        td {
            padding: 7px;
            border: 1px solid #ccc;
            font-size: 12px;
        }

        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .small { font-size: 10px; color: #777; }

        .ttd-section {
            margin-top: 40px;
            width: 100%;
        }

        .ttd-box {
            width: 45%;
            text-align: center;
            display: inline-block;
        }

        .ttd-space {
            height: 55px;
        }
    </style>
</head>
<body>

    {{-- ============================
         HEADER + LOGO
    ============================= --}}
    <div class="header">
        <img src="{{ public_path('logo.png') }}" alt="Logo">
        <!-- <h2>JADIUMRAH.COM</h2> -->
        <div class="alamat">
            Jl. Cempaka Putih No. 24, Jakarta Pusat • Telp: 0812-3456-7890<br>
            Email: info@jadiumrah.com • Website: www.jadiumrah.com
        </div>

        <h3>Laporan Biaya Operasional</h3>
        <h4>Bulan {{ $namaBulan }} • Tahun {{ $tahun }}</h4>
    </div>


    {{-- ============================
           TABEL DATA OPERASIONAL
    ============================= --}}
    <table>
        <thead>
            <tr>
                <th style="width:25px;">#</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th style="width: 95px;">Tanggal</th>
                <th class="text-end" style="width:120px;">Jumlah (Rp)</th>
            </tr>
        </thead>

        <tbody>
            @php $no = 1; @endphp

            @forelse($data as $row)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $row->kategori }}</td>
                    <td>{{ $row->deskripsi ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                    <td class="text-end">{{ number_format($row->jumlah) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data pada bulan ini.</td>
                </tr>
            @endforelse

            {{-- ============================
                   TOTAL PENGELUARAN
            ============================= --}}
            @if(count($data))
            <tr>
                <td colspan="4" class="text-end fw-bold">Total Pengeluaran</td>
                <td class="text-end fw-bold">Rp {{ number_format($total) }}</td>
            </tr>
            @endif

        </tbody>
    </table>


    {{-- ============================
              TANDA TANGAN
    ============================= --}}
    <div class="ttd-section">

        <div class="ttd-box">
            <p>Mengetahui,</p>
            <p class="fw-bold">Pimpinan</p>
            <div class="ttd-space"></div>
            <p class="fw-bold" style="text-decoration: underline;">________________________</p>
        </div>

        <div class="ttd-box" style="float: right;">
            <p>Disetujui,</p>
            <p class="fw-bold">Finance</p>
            <div class="ttd-space"></div>
            <p class="fw-bold" style="text-decoration: underline;">________________________</p>
        </div>

    </div>


    <p class="small" style="margin-top: 40px;">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </p>

</body>
</html>
