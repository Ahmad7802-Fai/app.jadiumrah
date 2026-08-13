@extends('layouts.admin')

@section('title', 'Marketing Expenses')

@section('content')

{{-- ================= HEADER ================= --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Marketing Expenses</h4>
        <small class="text-muted">Monitoring biaya, CPL & ROI marketing</small>
    </div>

    <a href="{{ route('marketing.expenses.create') }}" class="btn btn-primary">
        <i class="fa fa-plus me-1"></i> Tambah Biaya
    </a>
</div>

{{-- ================= SUMMARY ================= --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-start border-primary border-3">
            <div class="card-body">
                <div class="text-muted small">Total Biaya</div>
                <h4>Rp {{ number_format($summary['total_biaya']) }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-start border-success border-3">
            <div class="card-body">
                <div class="text-muted small">Total Campaign</div>
                <h4>{{ $summary['total_campaign'] }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-start border-warning border-3">
            <div class="card-body">
                <div class="text-muted small">Platform Terbesar</div>
                <h4 class="text-uppercase">{{ $summary['top_platform'] ?? '-' }}</h4>
            </div>
        </div>
    </div>
</div>

{{-- ================= COST PER LEAD ================= --}}
<div class="card mb-4">
    <div class="card-header"><strong>Cost Per Lead (CPL)</strong></div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Sumber</th>
                <th>Biaya</th>
                <th>Total Lead</th>
                <th>CPL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cplData as $row)
                <tr>
                    <td>{{ $row['sumber'] }}</td>
                    <td>Rp {{ number_format($row['total_biaya']) }}</td>
                    <td>{{ $row['total_lead'] }}</td>
                    <td class="fw-bold text-primary">
                        Rp {{ number_format($row['cpl']) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ================= ROI ================= --}}
<div class="card mb-4">
    <div class="card-header"><strong>ROI Marketing</strong></div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Sumber</th>
                <th>Biaya</th>
                <th>Revenue</th>
                <th>Profit</th>
                <th>ROI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roiData as $row)
                <tr>
                    <td>{{ $row['sumber'] }}</td>
                    <td>Rp {{ number_format($row['total_biaya']) }}</td>
                    <td>Rp {{ number_format($row['total_revenue']) }}</td>
                    <td class="{{ $row['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($row['profit']) }}
                    </td>
                    <td>
                        <span class="badge {{ $row['roi'] >= 100 ? 'bg-success' : 'bg-warning' }}">
                            {{ $row['roi'] }}%
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ================= EXPENSE TABLE ================= --}}
<div class="card">
    <div class="card-header"><strong>Daftar Marketing Expense</strong></div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Campaign</th>
                <th>Sumber</th>
                <th>Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $e)
                <tr>
                    <td>{{ $e->tanggal_indo }}</td>
                    <td>{{ $e->nama_campaign ?? '-' }}</td>
                    <td>{{ $e->sumber->nama_sumber ?? '-' }}</td>
                    <td class="text-end">Rp {{ number_format($e->biaya) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="card-footer">
        {{ $expenses->links() }}
    </div>
</div>

@endsection
