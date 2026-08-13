@extends('layouts.admin')

@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="page-container page-wide">

    {{-- ===============================
    | HEADER + FILTER
    =============================== --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

        <div>
            <h4 class="fw-bold mb-0">Laporan Laba Rugi</h4>
            <small class="text-muted">
                Periode: {{ $bulanNama }} {{ $tahun }}
            </small>
        </div>

        <form method="GET" class="d-flex flex-wrap gap-2 mt-3 mt-md-0">

            {{-- BACK --}}
            <a href="{{ route('keuangan.laporan.index') }}"
               class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                <span class="btn-text">Kembali</span>
            </a>

            {{-- BULAN --}}
            <select name="bulan" class="form-select form-select-sm">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == $bulan ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endforeach
            </select>

            {{-- TAHUN --}}
            <select name="tahun" class="form-select form-select-sm">
                @foreach(range(date('Y')-5, date('Y')+1) as $y)
                    <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i>
                <span class="btn-text">Tampilkan</span>
            </button>

            <a href="{{ route('keuangan.laporan.pnl.excel', request()->query()) }}"
               class="btn btn-outline-primary btn-sm">
                <i class="fas fa-file-excel"></i>
                <span class="btn-text">Excel</span>
            </a>

            <a href="{{ route('keuangan.laporan.pnl.pdf', request()->query()) }}"
               class="btn btn-outline-danger btn-sm">
                <i class="fas fa-file-pdf"></i>
                <span class="btn-text">PDF</span>
            </a>

        </form>
    </div>

    {{-- ===============================
    | SUMMARY CARDS
    =============================== --}}
    <div class="row g-3 mb-4">

        {{-- TOTAL REVENUE --}}
        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 shadow-sm h-100">
                <div class="small text-muted">Total Pendapatan</div>
                <h4 class="fw-bold text-success mb-0">
                    Rp {{ number_format($totalRevenue,0,',','.') }}
                </h4>
                <small class="text-muted">Jamaah + Layanan</small>
            </div>
        </div>

        {{-- HPP --}}
        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 shadow-sm h-100">
                <div class="small text-muted">HPP</div>
                <h4 class="fw-bold text-danger mb-0">
                    Rp {{ number_format($hpp,0,',','.') }}
                </h4>
                <small class="text-muted">Trip + Vendor</small>
            </div>
        </div>

        {{-- OPEX --}}
        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 shadow-sm h-100">
                <div class="small text-muted">Beban Operasional</div>
                <h4 class="fw-bold text-danger mb-0">
                    Rp {{ number_format($operational + $marketing,0,',','.') }}
                </h4>
                <small class="text-muted">Operasional + Marketing</small>
            </div>
        </div>

        {{-- NET PROFIT --}}
        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 shadow-sm h-100">
                <div class="small text-muted">Laba Bersih</div>
                <h4 class="fw-bold {{ $netProfit >= 0 ? 'text-success':'text-danger' }} mb-0">
                    Rp {{ number_format($netProfit,0,',','.') }}
                </h4>
                <small class="text-muted">Setelah semua beban</small>
            </div>
        </div>

    </div>

    {{-- ===============================
    | DETAIL PERHITUNGAN
    =============================== --}}
    <div class="card rounded-4 shadow-sm p-4 mb-5">
        <h5 class="fw-bold mb-3">Rincian Laba Rugi</h5>

        <div class="table-wrap">
            <table class="table table-compact mb-0">
                <tbody>

                    {{-- REVENUE --}}
                    <tr>
                        <td><strong>Total Pendapatan</strong></td>
                        <td class="text-end fw-bold">
                            Rp {{ number_format($totalRevenue,0,',','.') }}
                        </td>
                    </tr>

                    <tr>
                        <td class="ps-4 text-muted">Pendapatan Jamaah</td>
                        <td class="text-end text-muted">
                            Rp {{ number_format($revenueJamaah,0,',','.') }}
                        </td>
                    </tr>

                    <tr>
                        <td class="ps-4 text-muted">Pendapatan Layanan</td>
                        <td class="text-end text-muted">
                            Rp {{ number_format($revenueLayanan,0,',','.') }}
                        </td>
                    </tr>

                    <tr><td colspan="2"><hr></td></tr>

                    {{-- HPP --}}
                    <tr>
                        <td><strong>Harga Pokok Penjualan (HPP)</strong></td>
                        <td class="text-end fw-bold">
                            – Rp {{ number_format($hpp,0,',','.') }}
                        </td>
                    </tr>

                    <tr>
                        <td class="ps-4 text-muted">Biaya Trip</td>
                        <td class="text-end text-muted">
                            – Rp {{ number_format($tripExpenses,0,',','.') }}
                        </td>
                    </tr>

                    <tr>
                        <td class="ps-4 text-muted">Biaya Vendor</td>
                        <td class="text-end text-muted">
                            – Rp {{ number_format($vendorExpenses,0,',','.') }}
                        </td>
                    </tr>

                    <tr><td colspan="2"><hr></td></tr>

                    {{-- GROSS PROFIT --}}
                    <tr>
                        <td><strong>Laba Kotor</strong></td>
                        <td class="text-end fw-bold {{ $grossProfit >= 0 ? 'text-success':'text-danger' }}">
                            Rp {{ number_format($grossProfit,0,',','.') }}
                        </td>
                    </tr>

                    <tr><td colspan="2"><hr></td></tr>

                    {{-- OPEX --}}
                    <tr>
                        <td>Beban Operasional</td>
                        <td class="text-end">
                            – Rp {{ number_format($operational,0,',','.') }}
                        </td>
                    </tr>

                    <tr>
                        <td>Beban Marketing</td>
                        <td class="text-end">
                            – Rp {{ number_format($marketing,0,',','.') }}
                        </td>
                    </tr>

                    <tr><td colspan="2"><hr></td></tr>

                    {{-- NET PROFIT --}}
                    <tr>
                        <td><strong>Laba Bersih</strong></td>
                        <td class="text-end fw-bold {{ $netProfit >= 0 ? 'text-success':'text-danger' }}">
                            Rp {{ number_format($netProfit,0,',','.') }}
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
