<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Kwitansi Pembayaran</title>

<style>

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:12px;
    color:#333;
}

.container{
    width:100%;
}

.header{
    width:100%;
    margin-bottom:12px;
}

.logo{
    float:left;
}

.logo img{
    height:36px;
}

.title{
    float:right;
    font-size:18px;
    font-weight:bold;
    letter-spacing:0.5px;
}

.clear{
    clear:both;
}

.meta{
    margin-top:8px;
    margin-bottom:10px;
}

.meta table{
    width:100%;
}

.meta td{
    padding:2px 0;
}

.section{
    margin-top:6px;
}

.section table{
    width:100%;
}

.section td{
    padding:2px 0;
}

.label{
    width:90px;
    color:#666;
}

.divider{
    border-top:1px solid #e5e5e5;
    margin:10px 0;
}

.amount-box{
    text-align:center;
    margin-top:6px;
}

.amount-title{
    font-size:11px;
    color:#777;
}

.amount{
    font-size:20px;
    font-weight:bold;
    color:#1f8f3a;
    margin-top:3px;
}

.footer{
    text-align:center;
    font-size:10px;
    color:#888;
    margin-top:10px;
}

</style>
</head>

<body>

<div class="container">

    {{-- HEADER --}}
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('logo.png') }}">
        </div>

        <div class="title">
            KWITANSI PEMBAYARAN
        </div>

        <div class="clear"></div>
    </div>


    {{-- META --}}
    <div class="meta">
        <table>

            <tr>
                <td class="label">No Kwitansi</td>
                <td>: {{ $payment->receipt_number }}</td>
            </tr>

            <tr>
                <td class="label">Tanggal</td>
                <td>: {{ $payment->paid_at->format('d M Y') }}</td>
            </tr>

        </table>
    </div>


    <div class="divider"></div>


    {{-- BOOKING --}}
    <div class="section">
        <table>

            <tr>
                <td class="label">Booking</td>
                <td>: {{ $payment->booking->booking_code }}</td>
            </tr>

            <tr>
                <td class="label">Paket</td>
                <td>: {{ $payment->booking->paket->name ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">Jamaah</td>
                <td>: {{ $payment->booking->jamaahs->first()->nama_lengkap ?? '-' }}</td>
            </tr>

        </table>
    </div>


    <div class="divider"></div>


    {{-- PAYMENT INFO --}}
    <div class="section">
        <table>

            <tr>
                <td class="label">Metode</td>
                <td>: {{ ucfirst($payment->method) }}</td>
            </tr>

            <tr>
                <td class="label">Tipe</td>
                <td>: {{ ucfirst($payment->type) }}</td>
            </tr>

        </table>
    </div>


    <div class="divider"></div>


    {{-- AMOUNT --}}
    <div class="amount-box">

        <div class="amount-title">
            Jumlah Dibayar
        </div>

        <div class="amount">
            Rp {{ number_format($payment->amount,0,',','.') }}
        </div>

    </div>


    <div class="divider"></div>


    <div class="footer">
        Terima kasih atas pembayaran Anda
    </div>

</div>

</body>
</html>