@extends('layouts.app')

@section('title','Finance • Refund')

@section('content')

<div class="space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Refund
            </h1>
            <p class="text-sm text-gray-500">
                Monitoring & approval pengembalian dana
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
                       placeholder="Kode refund..."
                       class="input w-64">
            </div>

            <div>
                <label class="meta-label">Status</label>
                <select name="status" class="input w-40">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                    <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
                </select>
            </div>

            <button class="btn btn-primary">
                Filter
            </button>

        </form>
    </div>


    {{-- ================= SUMMARY ================= --}}
    @php
        $total     = $refunds->sum('amount');
        $approved  = $refunds->where('status','approved')->sum('amount');
        $pending   = $refunds->where('status','pending')->sum('amount');
        $rejected  = $refunds->where('status','rejected')->sum('amount');
    @endphp

    <div class="grid grid-cols-4 gap-6">

        <div class="card-compact">
            <div class="meta-label">Total Refund</div>
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
                    <th>Payment</th>
                    <th>Booking</th>
                    <th>Branch</th>
                    <th>Jumlah</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Approved By</th>
                    <th>Tanggal</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>

            @forelse($refunds as $refund)

                <tr>

                    <td class="font-medium">
                        {{ $refund->refund_code }}
                    </td>

                    <td>
                        {{ $refund->payment->payment_code ?? '-' }}
                    </td>

                    <td>
                        {{ $refund->booking->booking_code ?? '-' }}
                    </td>

                    <td>
                        {{ $refund->booking->branch->name ?? '-' }}
                    </td>

                    <td class="font-semibold">
                        Rp {{ number_format($refund->amount,0,',','.') }}
                    </td>

                    {{-- REASON --}}
                    <td class="text-sm text-gray-600">
                        {{ $refund->reason ?? '-' }}
                    </td>

                    {{-- STATUS --}}
                    <td>
                        @if($refund->status === 'approved')
                            <span class="badge-success">Approved</span>
                        @elseif($refund->status === 'pending')
                            <span class="badge-warning">Pending</span>
                        @else
                            <span class="badge-danger">Rejected</span>
                        @endif
                    </td>

                    {{-- APPROVER --}}
                    <td class="text-sm">
                        {{ $refund->approver->name ?? '-' }}
                    </td>

                    <td>
                        {{ $refund->created_at->format('d M Y') }}
                    </td>

                    {{-- ACTION --}}
                    <td class="text-right">

                        <div class="flex gap-2 justify-end">

                            {{-- APPROVAL --}}
                            @if($refund->status === 'pending')

                                @can('approve', $refund)

                                    <form method="POST"
                                          action="{{ route('finance.refunds.approve',$refund) }}">
                                        @csrf
                                        <button class="btn btn-primary btn-xs">
                                            Approve
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('finance.refunds.reject',$refund) }}">
                                        @csrf
                                        <button class="btn btn-danger btn-xs">
                                            Reject
                                        </button>
                                    </form>

                                @endcan

                            @endif

                            {{-- RECEIPT --}}
                            @if($refund->status === 'approved')
                                <a href="{{ route('finance.refunds.receipt',$refund) }}"
                                   target="_blank"
                                   class="btn btn-outline btn-xs">
                                    Receipt
                                </a>
                            @endif

                        </div>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="10"
                        class="text-center py-12 text-gray-400">
                        Belum ada data refund
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div>
        {{ $refunds->withQueryString()->links() }}
    </div>

</div>

@endsection