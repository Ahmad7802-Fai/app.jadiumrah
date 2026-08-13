@extends('layouts.agent')

@section('page-title','Konfirmasi Pencairan')
@section('page-subtitle','Pastikan data sebelum melanjutkan')

@section('content')

<div class="card max-w-xl mx-auto">

    <div class="card-header">
        <h3 class="card-title">Ajukan Pencairan Komisi</h3>
    </div>

    <div class="card-body text-sm space-y-3">

        <div class="flex justify-between">
            <span>Jumlah Dicairkan</span>
            <strong>Rp {{ number_format($total) }}</strong>
        </div>

        <div class="flex justify-between">
            <span>Total Item</span>
            <strong>{{ $totalItem }} komisi</strong>
        </div>

        <hr>

        <div class="flex justify-between">
            <span>Nama Rekening</span>
            <strong>{{ $agent->bank_account_name }}</strong>
        </div>

        <div class="flex justify-between">
            <span>Bank</span>
            <strong>{{ $agent->bank_name }}</strong>
        </div>

        <div class="flex justify-between">
            <span>No. Rekening</span>
            <strong>{{ $agent->bank_account_number }}</strong>
        </div>

        <div class="text-xs text-gray-500 mt-2">
            ⚠ Proses pencairan akan diverifikasi oleh admin.
        </div>
    </div>

    <div class="card-footer flex gap-2 justify-end">
        <a href="{{ route('agent.komisi.index') }}"
           class="btn btn-secondary btn-sm">
            Batal
        </a>

        <form method="POST"
              action="{{ route('agent.payout.request') }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">
                Ya, Ajukan
            </button>
        </form>
    </div>

</div>

@endsection
