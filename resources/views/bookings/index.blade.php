@extends('layouts.app')

@section('title','Data Booking')

@section('content')

<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold">
                Data Booking
            </h1>
            <p class="text-sm text-gray-500">
                Kelola semua transaksi booking
            </p>
        </div>

        @can('booking.create')
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">
            + Buat Booking
        </a>
        @endcan

    </div>


    {{-- ================= FILTER ================= --}}
    <div class="card">

        <form method="GET" class="grid md:grid-cols-3 gap-4">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari nama jamaah..."
                   class="input">

            <select name="status" class="input">
                <option value="">-- Semua Status --</option>
                <option value="draft"
                    {{ request('status')=='draft'?'selected':'' }}>
                    Draft
                </option>
                <option value="confirmed"
                    {{ request('status')=='confirmed'?'selected':'' }}>
                    Confirmed
                </option>
                <option value="cancelled"
                    {{ request('status')=='cancelled'?'selected':'' }}>
                    Cancelled
                </option>
            </select>

            <button class="btn btn-secondary">
                Filter
            </button>

        </form>

    </div>


    {{-- ================= TABLE ================= --}}
    <div class="card">

        @if($bookings->count())

        <div class="overflow-x-auto">

            <table class="table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jamaah</th>
                        <th>Paket</th>
                        <th>Departure</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th width="220">Action</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($bookings as $booking)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        {{-- ================= MULTI JAMAAH ================= --}}
                        <td>
                            @if($booking->jamaahs->count())
                                <div class="flex flex-col text-sm">
                                    @foreach($booking->jamaahs as $j)
                                        <span>{{ $j->nama_lengkap }}</span>
                                    @endforeach
                                </div>
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            {{ $booking->paket?->name ?? '-' }}
                        </td>

                        <td>
                            {{ optional($booking->departure)->departure_date 
                                ? \Carbon\Carbon::parse($booking->departure->departure_date)->format('d M Y')
                                : '-' }}
                        </td>

                        <td>
                            Rp {{ number_format($booking->total_amount,0,',','.') }}
                        </td>

                        <td>
                            Rp {{ number_format($booking->paid_amount,0,',','.') }}
                        </td>

                       {{-- ================= STATUS BADGE ================= --}}
                        <td>

                            @if($booking->status === 'draft')

                                <span class="badge badge-secondary">
                                    Draft
                                </span>

                            @elseif($booking->status === 'waiting_payment')

                                <span class="badge badge-warning">
                                    Waiting Payment
                                </span>

                            @elseif($booking->status === 'partial_paid')

                                <span class="badge badge-info">
                                    Partial Paid
                                </span>

                            @elseif($booking->status === 'confirmed')

                                <span class="badge badge-success">
                                    Confirmed
                                </span>

                            @elseif($booking->status === 'cancelled')

                                <span class="badge badge-danger">
                                    Cancelled
                                </span>

                            @endif

                        </td>

                        {{-- ================= ACTION ================= --}}
                        <td class="flex flex-wrap gap-2">

                            <a href="{{ route('bookings.show',$booking) }}"
                               class="btn btn-outline btn-sm">
                                View
                            </a>

                            @can('booking.update')
                            <a href="{{ route('bookings.edit',$booking) }}"
                               class="btn btn-secondary btn-sm">
                                Edit
                            </a>
                            @endcan

                            {{-- Confirm --}}
                            @if($booking->status === 'draft')
                                @can('booking.approve')
                                <form action="{{ route('bookings.confirm',$booking) }}"
                                      method="POST">
                                    @csrf
                                    <button class="btn btn-primary btn-sm">
                                        Confirm
                                    </button>
                                </form>
                                @endcan
                            @endif

                            {{-- Cancel --}}
                            @if($booking->status !== 'cancelled')
                                @can('booking.cancel')
                                <form action="{{ route('bookings.cancel',$booking) }}"
                                      method="POST">
                                    @csrf
                                    <button class="btn btn-danger btn-sm">
                                        Cancel
                                    </button>
                                </form>
                                @endcan
                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

        <div class="mt-4">
            {{ $bookings->withQueryString()->links() }}
        </div>

        @else

            <div class="text-center py-12 text-gray-400">
                Belum ada booking.
            </div>

        @endif

    </div>

</div>

@endsection