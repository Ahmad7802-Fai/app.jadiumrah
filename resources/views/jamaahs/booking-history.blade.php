@extends('layouts.app')

@section('title','Riwayat Booking')

@section('content')

<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <div>
        <h1 class="text-2xl font-bold">Riwayat Booking</h1>
        <p class="text-sm text-gray-500">
            Semua riwayat booking jamaah
        </p>
    </div>


    {{-- ================= FILTER ================= --}}
    <div class="card">
        <form method="GET" class="grid md:grid-cols-3 gap-4">

            <input type="text"
                   name="search"
                   placeholder="Cari nama / kode jamaah..."
                   value="{{ request('search') }}"
                   class="input">

            <select name="status" class="input">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>Confirmed</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
            </select>

            <button class="btn btn-primary">
                Filter
            </button>

        </form>
    </div>


    {{-- ================= TABLE ================= --}}
    <div class="card table-wrapper">

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jamaah</th>
                    <th>Paket</th>
                    <th>Keberangkatan</th>
                    <th>Seat</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

            @forelse($bookings as $booking)

                <tr>

                    {{-- BOOKING ID --}}
                    <td class="font-semibold">
                        #{{ $booking->id }}
                    </td>


                    {{-- JAMAAH (MULTI) --}}
                    <td>
                        @forelse($booking->jamaahs as $jamaah)
                            <div class="mb-1">
                                <div class="font-medium">
                                    {{ $jamaah->nama_lengkap }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $jamaah->jamaah_code }}
                                </div>
                            </div>
                        @empty
                            <span class="text-gray-400">-</span>
                        @endforelse
                    </td>


                    {{-- PAKET --}}
                    <td>
                        <div class="font-medium">
                            {{ $booking->paket->name ?? '-' }}
                        </div>
                    </td>


                    {{-- DEPARTURE --}}
                    <td>
                        @if($booking->departure)
                            {{ \Carbon\Carbon::parse($booking->departure->departure_date)->format('d M Y') }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>


                    {{-- SEAT COUNT --}}
                    <td>
                        <span class="badge badge-secondary">
                            {{ $booking->jamaahs->count() }} Seat
                        </span>
                    </td>


                    {{-- STATUS --}}
                    <td>
                        @if($booking->status === 'confirmed')
                            <span class="badge badge-success">Confirmed</span>
                        @elseif($booking->status === 'draft')
                            <span class="badge badge-warning">Draft</span>
                        @elseif($booking->status === 'cancelled')
                            <span class="badge badge-danger">Cancelled</span>
                        @else
                            <span class="badge badge-primary">
                                {{ ucfirst($booking->status) }}
                            </span>
                        @endif
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-400">
                        Belum ada data booking
                    </td>
                </tr>

            @endforelse

            </tbody>
        </table>

    </div>


    {{-- ================= PAGINATION ================= --}}
    <div>
        {{ $bookings->links() }}
    </div>

</div>

@endsection