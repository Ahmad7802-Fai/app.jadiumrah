@extends('layouts.app')

@section('title','Receipt')

@section('content')

<div class="max-w-2xl mx-auto bg-white p-8 shadow rounded space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold">RECEIPT</h2>
            <p class="text-sm text-gray-500">
                {{ $payment->receipt_number }}
            </p>
        </div>

        <div class="text-right text-sm">
            <p>{{ now()->format('d M Y') }}</p>
        </div>
    </div>

    <hr>

    <div class="grid grid-cols-2 gap-6 text-sm">

        <div>
            <p class="text-gray-500">Booking</p>
            <p class="font-semibold">
                {{ $payment->booking->booking_code }}
            </p>
        </div>

        <div>
            <p class="text-gray-500">Branch</p>
            <p class="font-semibold">
                {{ $payment->branch->name ?? '-' }}
            </p>
        </div>

        <div>
            <p class="text-gray-500">Metode</p>
            <p class="font-semibold">
                {{ ucfirst($payment->method) }}
            </p>
        </div>

        <div>
            <p class="text-gray-500">Tipe</p>
            <p class="font-semibold">
                {{ str_replace('_',' ',$payment->type) }}
            </p>
        </div>

    </div>

    <hr>

    <div class="text-center">
        <p class="text-gray-500 text-sm">Jumlah Dibayar</p>
        <p class="text-2xl font-bold text-green-600 mt-2">
            Rp {{ number_format($payment->amount,0,',','.') }}
        </p>
    </div>

</div>

@endsection