<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Name Tag</title>

<style>
    @page {
        size: A4 portrait;
        margin: 20px;
    }

    body {
        font-family: sans-serif;
        margin: 0;
        padding: 0;
    }

    .grid {
        width: 100%;
    }

    .card {
        width: 30%;
        height: 300px;
        border: 0.5px solid #1f4b3f;
        display: inline-block;
        margin-bottom: 15px;
        margin-right: 2%;
        padding: 10px;
        box-sizing: border-box;
        text-align: center;
        vertical-align: top;
    }

    .card:nth-child(3n) {
        margin-right: 0;
    }

    .paket {
        font-size: 13px;
        font-weight: bold;
        margin-bottom: 3px;
    }

    .flight {
        font-size: 11px;
        margin-bottom: 3px;
    }

    .seat {
        font-size: 13px;
        font-weight: bold;
        color: #1f4b3f;
        margin-bottom: 10px;
    }

    .name {
        font-size: 18px;
        font-weight: bold;
        margin: 10px 0;
    }

    .branch {
        font-size: 12px;
        margin-bottom: 10px;
    }

    .footer {
        font-size: 10px;
        color: #555;
    }
</style>

</head>
<body>

<div class="grid">

@foreach($jamaahs as $jamaah)

    <div class="card">

        <div class="paket">
            {{ $departure->paket->name }}
        </div>

        <div class="flight">
            Flight: {{ $departure->flight_number ?? '-' }}
        </div>

        <div class="seat">
            Seat: {{ $jamaah->seat_number ?? '-' }}
        </div>

        <div class="name">
            {{ strtoupper($jamaah->nama_lengkap) }}
        </div>

        <div class="branch">
            {{ $jamaah->branch->name ?? 'Head Office' }}
        </div>

        <div class="footer">
            {{ $departure->departure_date->format('d M Y') }}
        </div>

    </div>

@endforeach

</div>

</body>
</html>