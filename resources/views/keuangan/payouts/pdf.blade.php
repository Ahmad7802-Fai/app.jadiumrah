<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Payout #{{ $payout->id }}</title>

    <style>
        @page {
            margin: 18mm 16mm 22mm 16mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }

        /* ================== HEADER ================== */
        .header {
            border-bottom: 2px solid #111;
            margin-bottom: 14px;
            padding-bottom: 8px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            letter-spacing: .5px;
        }
        .header .sub {
            font-size: 10px;
            color: #6b7280;
        }

        /* ================== SECTION ================== */
        .section {
            margin-bottom: 16px;
        }
        .section h4 {
            margin: 0 0 6px 0;
            font-size: 13px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 4px;
        }

        .label {
            font-size: 10px;
            color: #6b7280;
        }
        .value {
            font-weight: 700;
            font-size: 11px;
        }

        /* ================== TABLE ================== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px;
            font-size: 10px;
        }
        th {
            background: #f3f4f6;
            text-align: left;
            font-weight: 700;
        }
        .right { text-align: right; }
        .center { text-align: center; }

        /* ================== STATUS ================== */
        .status {
            font-weight: 700;
            letter-spacing: .3px;
        }
        .status-paid { color: #047857; }
        .status-requested { color: #1d4ed8; }
        .status-approved { color: #92400e; }
        .status-rejected { color: #b91c1c; }

        /* ================== FOOTER ================== */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
            padding-top: 6px;
        }

        /* ================== WATERMARK ================== */
        .watermark {
            position: fixed;
            top: 45%;
            left: 15%;
            font-size: 64px;
            color: rgba(0,0,0,.06);
            transform: rotate(-25deg);
            z-index: -1;
            letter-spacing: 6px;
        }
    </style>
</head>

<body>

@if($payout->status === 'paid')
    <div class="watermark">PAID</div>
@endif

{{-- ================= HEADER ================= --}}
<div class="header">
    <h2>PAYOUT KOMISI AGENT</h2>
    <div class="sub">
        Dokumen Resmi · Sistem {{ config('app.name') }}
    </div>
</div>

{{-- ================= SUMMARY ================= --}}
<div class="section">
    <table>
        <tr>
            <td>
                <div class="label">Payout ID</div>
                <div class="value">#{{ $payout->id }}</div>
            </td>
            <td>
                <div class="label">Status</div>
                <div class="value status status-{{ $payout->status }}">
                    {{ strtoupper($payout->status) }}
                </div>
            </td>
            <td>
                <div class="label">Total Komisi</div>
                <div class="value">
                    Rp {{ number_format($payout->total_komisi,0,',','.') }}
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Agent</div>
                <div class="value">
                    {{ $payout->agent->user->nama ?? '-' }}
                    ({{ $payout->agent->kode_agent ?? '-' }})
                </div>
            </td>
            <td>
                <div class="label">Branch</div>
                <div class="value">
                    {{ $payout->branch->nama_cabang ?? '-' }}
                </div>
            </td>
            <td>
                <div class="label">Requested At</div>
                <div class="value">
                    {{ optional($payout->requested_at)->format('d M Y H:i') }}
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- ================= TRANSFER SNAPSHOT ================= --}}
@if($payout->transfer)
<div class="section">
    <h4>Informasi Transfer (Snapshot)</h4>
    <table>
        <tr>
            <th>Bank</th>
            <th>No Rekening</th>
            <th>Atas Nama</th>
            <th class="right">Jumlah</th>
            <th class="center">Dibayar</th>
        </tr>
        <tr>
            <td>{{ $payout->transfer->bank_name }}</td>
            <td>{{ $payout->transfer->bank_account_number }}</td>
            <td>{{ $payout->transfer->bank_account_name }}</td>
            <td class="right">
                Rp {{ number_format($payout->transfer->amount,0,',','.') }}
            </td>
            <td class="center">
                {{ optional($payout->transfer->paid_at)->format('d M Y H:i') }}
            </td>
        </tr>
    </table>
</div>
@endif

{{-- ================= DETAIL KOMISI ================= --}}
<div class="section">
    <h4>Detail Komisi</h4>
    <table>
        <thead>
            <tr>
                <th width="40">#</th>
                <th>Jamaah</th>
                <th>Invoice</th>
                <th class="right">%</th>
                <th class="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
        @foreach($payout->komisi as $k)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>
                    {{ $k->jamaah->nama_lengkap ?? '-' }}<br>
                    <small>{{ $k->jamaah->no_id ?? '-' }}</small>
                </td>
                <td>
                    {{ $k->payment && $k->payment->invoice
                        ? $k->payment->invoice->nomor_invoice
                        : '-' }}
                </td>
                <td class="right">
                    {{ number_format($k->komisi_persen,2) }}%
                </td>
                <td class="right">
                    Rp {{ number_format($k->komisi_nominal,0,',','.') }}
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="right">TOTAL</th>
                <th class="right">
                    Rp {{ number_format($payout->komisi->sum('komisi_nominal'),0,',','.') }}
                </th>
            </tr>
        </tfoot>
    </table>
</div>

{{-- ================= FOOTER (LEGAL) ================= --}}
<div class="footer">
    Dicetak pada {{ now()->format('d M Y H:i') }} ·
    Oleh {{ auth()->user()->nama ?? 'SYSTEM' }} ·
    Dokumen ini dihasilkan otomatis oleh sistem dan sah tanpa tanda tangan.
</div>

</body>
</html>
