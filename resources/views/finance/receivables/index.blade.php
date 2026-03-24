@extends('layouts.app')

@section('title','Finance • Piutang')

@section('content')

<div class="space-y-8">

    {{-- ================= HEADER ================= --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Piutang Booking
        </h1>
        <p class="text-sm text-gray-500">
            Monitoring sisa pembayaran jamaah
        </p>
    </div>

    {{-- ================= SUMMARY ================= --}}
    @php
        $totalPiutang = $bookings->sum('receivable');
        $totalBooking = $bookings->count();
    @endphp

    <div class="grid grid-cols-2 gap-6">

        <div class="card-compact">
            <div class="meta-label">Total Piutang</div>
            <div class="text-2xl font-bold text-red-600 mt-2">
                Rp {{ number_format($totalPiutang,0,',','.') }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Total Booking Belum Lunas</div>
            <div class="text-2xl font-bold mt-2">
                {{ $totalBooking }} Booking
            </div>
        </div>

    </div>


    {{-- ================= TABLE ================= --}}
    <div class="card table-wrapper">

        <table class="table">
            <thead>
                <tr>
                    <th>Booking</th>
                    <th>Nama Jamaah</th>
                    <th>Cabang</th>
                    <th>Paket</th>
                    <th>Total</th>
                    <th>Sudah Bayar</th>
                    <th>Piutang</th>
                </tr>
            </thead>

            <tbody>

            @forelse($bookings as $booking)

                @php
                    $piutang = $booking->receivable;
                @endphp

                <tr class="hover:bg-gray-50 transition">

                    {{-- BOOKING --}}
                    <td class="font-medium">
                        <a href="{{ route('bookings.show',$booking) }}"
                           class="text-blue-600 hover:underline">
                            {{ $booking->booking_code ?? '#'.$booking->id }}
                        </a>
                    </td>

                    {{-- NAMA JAMAAH --}}
                    <td>
                        @if($booking->jamaahs->count())
                            <div class="flex flex-col text-sm">
                                <span>
                                    {{ $booking->jamaahs->first()->nama_lengkap }}
                                </span>

                                @if($booking->jamaahs->count() > 1)
                                    <span class="text-xs text-gray-500">
                                        +{{ $booking->jamaahs->count() - 1 }} jamaah lainnya
                                    </span>
                                @endif
                            </div>
                        @else
                            -
                        @endif
                    </td>

                    {{-- CABANG --}}
                    <td>
                        {{ $booking->branch->name ?? '-' }}
                    </td>

                    {{-- PAKET --}}
                    <td>
                        {{ $booking->paket->name ?? '-' }}
                    </td>

                    {{-- TOTAL --}}
                    <td>
                        Rp {{ number_format($booking->total_amount,0,',','.') }}
                    </td>

                    {{-- SUDAH BAYAR --}}
                    <td class="text-green-600 font-medium">
                        Rp {{ number_format($booking->paid_amount,0,',','.') }}
                    </td>

                    {{-- PIUTANG --}}
                    <td class="font-semibold
                        @if($piutang > 10000000)
                            text-red-700
                        @else
                            text-red-600
                        @endif
                    ">
                        Rp {{ number_format($piutang,0,',','.') }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7"
                        class="text-center py-12 text-gray-400">
                        Tidak ada piutang
                    </td>
                </tr>

            @endforelse

            </tbody>
        </table>

    </div>

</div>

@endsection