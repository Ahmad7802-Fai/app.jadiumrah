@extends('layouts.app')

@section('title','Detail Booking')

@section('content')

<div class="space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Booking {{ $booking->booking_code ?? '#'.$booking->id }}
            </h1>
            <p class="text-sm text-gray-500">
                Detail informasi dan pembayaran
            </p>
        </div>

        <a href="{{ route('bookings.index') }}"
           class="btn btn-secondary">
            ← Kembali
        </a>

    </div>


    {{-- ================= BOOKING INFO ================= --}}
    <div class="card grid md:grid-cols-4 gap-6">

        <div>
            <div class="meta-label">Paket</div>
            <div class="meta-value">
                {{ $booking->paket?->name ?? '-' }}
            </div>
        </div>

        <div>
            <div class="meta-label">Departure</div>
            <div class="meta-value">
                {{ optional($booking->paketDeparture?->departure_date)->format('d M Y') }}
            </div>
        </div>

        <div>
            <div class="meta-label">Branch</div>
            <div class="meta-value">
                {{ $booking->branch?->name ?? '-' }}
            </div>
        </div>

        <div>
            <div class="meta-label">Agent</div>
            <div class="meta-value">
                {{ $booking->agent?->nama ?? '-' }}
            </div>
        </div>

    </div>


    {{-- ================= PAYMENT SUMMARY ================= --}}
    @php
        $total = $booking->total_amount;
        $paid  = $booking->payments->where('status','paid')->sum('amount');
        $remain = $total - $paid;

        if ($paid >= $total && $total > 0) {
            $paymentStatus = 'lunas';
        } elseif ($paid > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'belum';
        }
    @endphp

    <div class="grid md:grid-cols-4 gap-6">

        <div class="card-compact">
            <div class="meta-label">Total Booking</div>
            <div class="text-xl font-semibold mt-2">
                Rp {{ number_format($total,0,',','.') }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Total Dibayar</div>
            <div class="text-xl font-semibold mt-2 text-green-600">
                Rp {{ number_format($paid,0,',','.') }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Sisa Pembayaran</div>
            <div class="text-xl font-semibold mt-2 text-red-600">
                Rp {{ number_format($remain,0,',','.') }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Status Pembayaran</div>
            <div class="mt-3">

                @if($paymentStatus === 'lunas')
                    <span class="badge-success">Lunas</span>
                @elseif($paymentStatus === 'partial')
                    <span class="badge-warning">Partial</span>
                @else
                    <span class="badge-danger">Belum Bayar</span>
                @endif

            </div>
        </div>

    </div>

    {{-- ================= PAYMENT ACTION ================= --}}
    @can('create', App\Models\Payment::class)
    <div class="flex justify-end">
        <a href="{{ route('finance.payments.create', $booking) }}"
        class="btn btn-primary">
            + Tambah Pembayaran
        </a>
    </div>
    @endcan


    {{-- ================= PAYMENT HISTORY ================= --}}
    <div class="card">

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">
                Riwayat Pembayaran
            </h2>
        </div>

        @if($booking->payments->count())

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Metode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                @foreach($booking->payments as $payment)

                    <tr>
                        <td>{{ $payment->payment_code }}</td>

                        <td class="capitalize">
                            {{ $payment->method }}
                        </td>

                        <td class="font-semibold">
                            Rp {{ number_format($payment->amount,0,',','.') }}
                        </td>

                        <td>
                            @if($payment->status === 'paid')
                                <span class="badge-success">Paid</span>
                            @elseif($payment->status === 'pending')
                                <span class="badge-warning">Pending</span>
                            @else
                                <span class="badge-danger">Cancelled</span>
                            @endif
                        </td>

                        <td>
                            {{ optional($payment->paid_at)->format('d M Y') }}
                        </td>

                        <td class="flex gap-2">

                            @can('payment.update')
                            <a href="{{ route('finance.payments.edit',$payment) }}"
                               class="btn btn-secondary btn-xs">
                                Edit
                            </a>
                            @endcan

                            @can('payment.delete')
                            <form action="{{ route('finance.payments.destroy',$payment) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-xs">
                                    Hapus
                                </button>
                            </form>
                            @endcan

                        </td>
                    </tr>

                @endforeach
                </tbody>
            </table>
        </div>

        @else

            <div class="text-gray-400 text-sm">
                Belum ada pembayaran.
            </div>

        @endif

    </div>


    {{-- ================= JAMAAH LIST ================= --}}
    <div class="card">

        <h2 class="text-lg font-semibold mb-4">
            Jamaah Dalam Booking
        </h2>

        @if($booking->jamaahs->count())

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Room Type</th>
                        <th>Harga</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($booking->jamaahs as $j)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $j->nama_lengkap }}</td>
                        <td>{{ ucfirst($j->pivot->room_type ?? '-') }}</td>
                        <td>
                            Rp {{ number_format($j->pivot->price ?? 0,0,',','.') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @else
            <div class="text-gray-400">
                Tidak ada jamaah.
            </div>
        @endif

    </div>
{{-- ================= ADD-ON SECTION ================= --}}
<div class="card">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">
            Add-On Produk
        </h2>
    </div>

    {{-- FORM TAMBAH ADDON --}}
    @can('booking.update')
    <form method="POST"
          action="{{ route('bookings.addons.store',$booking) }}"
          class="flex gap-3 items-end mb-6 flex-wrap">
        @csrf

        <div class="flex-1 min-w-[250px]">
            <label class="meta-label">Pilih Add-On</label>
            <select name="marketing_addon_id" class="input">
                @foreach(\App\Models\MarketingAddon::where('is_active',1)->get() as $addon)
                    <option value="{{ $addon->id }}">
                        {{ $addon->name }}
                        (Rp {{ number_format($addon->selling_price,0,',','.') }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="meta-label">Qty</label>
            <input type="number"
                   name="qty"
                   value="1"
                   min="1"
                   class="input w-24">
        </div>

        <button class="btn btn-primary">
            Tambah
        </button>
    </form>
    @endcan


    {{-- LIST ADDON --}}
    @if($booking->addons->count())

    <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                @foreach($booking->addons as $addon)
                    <tr>
                        <td>{{ $addon->addon->name }}</td>

                        <td>
                            @can('booking.update')
                            <form method="POST"
                                action="{{ route('bookings.addons.update',[$booking,$addon]) }}"
                                class="flex gap-2">
                                @csrf
                                @method('PUT')

                                <input type="number"
                                    name="qty"
                                    value="{{ $addon->qty }}"
                                    min="0"
                                    class="input w-20">

                                <button class="btn btn-secondary btn-xs">
                                    Update
                                </button>
                            </form>
                            @else
                                {{ $addon->qty }}
                            @endcan
                        </td>

                        <td>
                            Rp {{ number_format($addon->price,0,',','.') }}
                        </td>

                        <td class="font-semibold">
                            Rp {{ number_format($addon->total,0,',','.') }}
                        </td>

                        <td>
                            @can('booking.update')
                            <form method="POST"
                                action="{{ route('bookings.addons.destroy',[$booking,$addon]) }}">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-xs">
                                    Hapus
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @else

            <div class="text-gray-400 text-sm">
                Belum ada add-on.
            </div>

        @endif

    </div>

</div>

@endsection