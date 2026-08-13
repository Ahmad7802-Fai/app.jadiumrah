@extends('layouts.admin')

@section('title','WA Analytics')

@section('content')
<div class="container-fluid">

    <h4 class="fw-bold mb-3">📊 WhatsApp Analytics</h4>

    {{-- FILTER --}}
    <form class="row g-2 mb-4">
        <div class="col-auto">
            <input type="date" name="from" value="{{ $from }}" class="form-control">
        </div>
        <div class="col-auto">
            <input type="date" name="to" value="{{ $to }}" class="form-control">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

    {{-- KPI --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-3">
                <div class="text-muted">Total WA</div>
                <h3>{{ $summary->total }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-success">
                <div class="text-muted">Berhasil</div>
                <h3 class="text-success">{{ $summary->success }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-danger">
                <div class="text-muted">Gagal</div>
                <h3 class="text-danger">{{ $summary->failed }}</h3>
            </div>
        </div>
    </div>

    {{-- BY TYPE --}}
    <div class="card mb-4">
        <div class="card-body">
            <strong>Distribusi WA</strong>
            <ul class="mt-2">
                <li>Approve: {{ $byType['APPROVE'] ?? 0 }}</li>
                <li>Reject: {{ $byType['REJECT'] ?? 0 }}</li>
                <li>Resend: {{ $byType['RESEND'] ?? 0 }}</li>
            </ul>
        </div>
    </div>

    {{-- DAILY --}}
    <div class="card mb-4">
        <div class="card-body">
            <strong>Trend Harian</strong>
            <table class="table table-sm mt-2">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Success</th>
                    <th>Failed</th>
                </tr>
                </thead>
                <tbody>
                @foreach($daily as $d)
                    <tr>
                        <td>{{ $d->date }}</td>
                        <td>{{ $d->total }}</td>
                        <td class="text-success">{{ $d->success }}</td>
                        <td class="text-danger">{{ $d->failed }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- FAILED LOG --}}
    <div class="card">
        <div class="card-body">
            <strong>WA Gagal Terakhir</strong>
            <table class="table table-sm mt-2">
                <thead>
                <tr>
                    <th>Waktu</th>
                    <th>No HP</th>
                    <th>Type</th>
                    <th>Error</th>
                </tr>
                </thead>
                <tbody>
                @foreach($failedList as $f)
                    <tr>
                        <td>{{ $f->created_at }}</td>
                        <td>{{ $f->phone }}</td>
                        <td>{{ $f->type }}</td>
                        <td class="text-danger">{{ $f->error }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
