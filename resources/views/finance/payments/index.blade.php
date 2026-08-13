@extends('layouts.app')

@section('title','Finance • Pembayaran')

@section('content')

<div class="space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Pembayaran
            </h1>
            <p class="text-sm text-gray-500">
                Monitoring & approval transaksi pembayaran jamaah
            </p>
        </div>

    </div>


    {{-- ================= FILTER ================= --}}
    <div class="card-compact">

        <form method="GET" class="flex flex-wrap gap-4 items-end">

            <div>
                <label class="meta-label">Search</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Kode payment..."
                       class="input w-64">
            </div>

            <div>
                <label class="meta-label">Status</label>
                <select name="status" class="input w-40">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Approved</option>
                    <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Rejected</option>
                </select>
            </div>

            <button class="btn btn-primary">
                Filter
            </button>

        </form>

    </div>


    {{-- ================= SUMMARY ================= --}}
    @php
        $total     = $payments->sum('amount');
        $approved  = $payments->where('status','paid')->sum('amount');
        $pending   = $payments->where('status','pending')->sum('amount');
        $rejected  = $payments->where('status','cancelled')->sum('amount');
    @endphp

    <div class="grid grid-cols-4 gap-6">

        <div class="card-compact">
            <div class="meta-label">Total</div>
            <div class="text-xl font-semibold mt-2">
                Rp {{ number_format($total,0,',','.') }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Approved</div>
            <div class="text-xl font-semibold mt-2 text-green-600">
                Rp {{ number_format($approved,0,',','.') }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Pending</div>
            <div class="text-xl font-semibold mt-2 text-yellow-600">
                Rp {{ number_format($pending,0,',','.') }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Rejected</div>
            <div class="text-xl font-semibold mt-2 text-red-600">
                Rp {{ number_format($rejected,0,',','.') }}
            </div>
        </div>

    </div>


    {{-- ================= TABLE ================= --}}
    <div class="card table-wrapper">

        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Booking</th>
                    <th>Branch</th>
                    <th>Metode</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>

            @forelse($payments as $payment)

                <tr>

                    <td class="font-medium">
                        {{ $payment->payment_code }}
                    </td>

                    <td>
                        {{ $payment->booking->booking_code ?? '-' }}
                    </td>

                    <td>
                        {{ $payment->branch->name ?? '-' }}
                    </td>

                    <td class="capitalize">
                        {{ $payment->method }}
                    </td>

                    <td class="capitalize">
                        {{ str_replace('_',' ',$payment->type) }}
                    </td>

                    <td class="font-semibold">
                        Rp {{ number_format($payment->amount,0,',','.') }}
                    </td>

                    {{-- ================= BUKTI ================= --}}
                    <td>
                        @if($payment->proof_file)
                            <a href="{{ asset('storage/'.$payment->proof_file) }}"
                               target="_blank"
                               class="btn btn-outline btn-xs">
                                Preview
                            </a>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>

                    {{-- ================= STATUS ================= --}}
                    <td>
                        @if($payment->status === 'paid')
                            <span class="badge-success">Approved</span>
                        @elseif($payment->status === 'pending')
                            <span class="badge-warning">Pending</span>
                        @else
                            <span class="badge-danger">Rejected</span>
                        @endif
                    </td>

                    <td>
                        {{ optional($payment->paid_at)->format('d M Y') }}
                    </td>

                    {{-- ================= ACTION ================= --}}
                    <td class="text-right">

                        <div class="flex gap-2 justify-end flex-wrap">

                            {{-- ================= APPROVAL ================= --}}
                            @if($payment->status === 'pending')

                                @can('approve', $payment)

                                    {{-- APPROVE --}}
                                    @if($payment->proof_file)
                                        <form action="{{ route('finance.payments.approve',$payment) }}"
                                            method="POST">
                                            @csrf
                                            <button class="btn btn-primary btn-xs">
                                                Approve
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-primary btn-xs opacity-50 cursor-not-allowed"
                                                disabled>
                                            Approve
                                        </button>
                                    @endif

                                    {{-- REJECT --}}
                                    <form action="{{ route('finance.payments.reject',$payment) }}"
                                        method="POST">
                                        @csrf
                                        <button class="btn btn-danger btn-xs">
                                            Reject
                                        </button>
                                    </form>

                                @endcan

                            @endif


                            {{-- ================= RECEIPT ================= --}}
                            @if($payment->status === 'paid')

                                @can('view', $payment)
                                    <a href="{{ route('finance.payments.receipt', $payment) }}"
                                    target="_blank"
                                    class="btn btn-outline btn-xs">
                                        Receipt
                                    </a>
                                @endcan

                            @endif


                            {{-- ================= INVOICE ================= --}}
                            @if($payment->booking && $payment->booking->invoice_number)

                                @can('viewInvoice', $payment->booking)
                                    <a href="{{ route('finance.payments.invoice', $payment->booking) }}"
                                    target="_blank"
                                    class="btn btn-secondary btn-xs">
                                        Invoice
                                    </a>
                                @endcan

                            @endif


                            {{-- ================= EDIT ================= --}}
                            @can('update', $payment)
                                @if($payment->status === 'pending')
                                    <a href="{{ route('finance.payments.edit',$payment) }}"
                                    class="btn btn-warning btn-xs">
                                        Edit
                                    </a>
                                @endif
                            @endcan

                        </div>

                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="10"
                        class="text-center py-12 text-gray-400">
                        Belum ada transaksi pembayaran
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    <div>
        {{ $payments->withQueryString()->links() }}
    </div>

</div>

@endsection