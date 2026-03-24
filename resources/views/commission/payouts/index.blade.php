@extends('layouts.app')

@section('title','Finance • Commission Payout')

@section('content')

<div class="space-y-8">

    {{-- ================= HEADER ================= --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Commission Payout
        </h1>
        <p class="text-sm text-gray-500">
            Monitoring & approval pembayaran komisi agent
        </p>
    </div>


    {{-- ================= SUMMARY ================= --}}
    @php
        $total      = $payouts->sum('total_amount');
        $requested  = $payouts->where('status','requested')->sum('total_amount');
        $approved   = $payouts->where('status','approved')->sum('total_amount');
        $paid       = $payouts->where('status','paid')->sum('total_amount');
    @endphp

    <div class="grid grid-cols-4 gap-6">

        <div class="card-compact">
            <div class="meta-label">Total Payout</div>
            <div class="text-xl font-semibold mt-2">
                Rp {{ number_format($total,0,',','.') }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Requested</div>
            <div class="text-xl font-semibold mt-2 text-yellow-600">
                Rp {{ number_format($requested,0,',','.') }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Approved</div>
            <div class="text-xl font-semibold mt-2 text-blue-600">
                Rp {{ number_format($approved,0,',','.') }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Paid</div>
            <div class="text-xl font-semibold mt-2 text-green-600">
                Rp {{ number_format($paid,0,',','.') }}
            </div>
        </div>

    </div>


    {{-- ================= TABLE ================= --}}
    <div class="card table-wrapper">

        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Agent</th>
                    <th>Cabang</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal Request</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>

            @forelse($payouts as $payout)

                <tr>

                    <td class="font-medium">
                        {{ $payout->payout_code ?? '#'.$payout->id }}
                    </td>

                    <td>
                        {{ $payout->agent->nama ?? '-' }}
                    </td>

                    <td>
                        {{ $payout->branch->name ?? '-' }}
                    </td>

                    <td class="font-semibold">
                        Rp {{ number_format($payout->total_amount,0,',','.') }}
                    </td>

                    {{-- STATUS --}}
                    <td>
                        @if($payout->status === 'requested')
                            <span class="badge-warning">Requested</span>
                        @elseif($payout->status === 'approved')
                            <span class="badge-primary">Approved</span>
                        @else
                            <span class="badge-success">Paid</span>
                        @endif
                    </td>

                    <td>
                        {{ $payout->created_at->format('d M Y') }}
                    </td>

                    {{-- ACTION --}}
                    <td class="text-right">

                        <div class="flex gap-2 justify-end">

                            {{-- APPROVE --}}
                            @if($payout->status === 'requested')

                                @can('approve', $payout)
                                <form method="POST"
                                      action="{{ route('commission.payouts.approve',$payout) }}">
                                    @csrf
                                    <button class="btn btn-primary btn-xs">
                                        Approve
                                    </button>
                                </form>
                                @endcan

                            @endif

                            {{-- MARK AS PAID --}}
                            @if($payout->status === 'approved')

                                @can('markAsPaid', $payout)
                                <form method="POST"
                                      action="{{ route('commission.payouts.paid',$payout) }}">
                                    @csrf
                                    <button class="btn btn-success btn-xs">
                                        Mark as Paid
                                    </button>
                                </form>
                                @endcan

                            @endif

                        </div>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7"
                        class="text-center py-12 text-gray-400">
                        Belum ada payout komisi
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    <div>
        {{ $payouts->withQueryString()->links() }}
    </div>

</div>

@endsection