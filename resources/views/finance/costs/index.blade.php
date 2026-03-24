@extends('layouts.app')

@section('title','Finance • Cost Management')

@section('content')

<div class="space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Cost Management
            </h1>
            <p class="text-sm text-gray-500">
                Monitoring & approval biaya operasional
            </p>
        </div>

        @can('create', \App\Models\Cost::class)
            <a href="{{ route('finance.costs.create') }}"
               class="btn btn-primary">
                + Tambah Cost
            </a>
        @endcan
    </div>

    {{-- ================= SUMMARY ================= --}}
    @php
        $total     = $costs->sum('amount');
        $approved  = $costs->where('status','approved')->sum('amount');
        $pending   = $costs->where('status','draft')->sum('amount');
    @endphp

    <div class="grid grid-cols-3 gap-6">

        <div class="card-compact">
            <div class="meta-label">Total Cost</div>
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
            <div class="meta-label">Pending Approval</div>
            <div class="text-xl font-semibold mt-2 text-yellow-600">
                Rp {{ number_format($pending,0,',','.') }}
            </div>
        </div>

    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card table-wrapper">

        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Cabang</th>
                    <th>Departure</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>

            @forelse($costs as $cost)

                <tr>

                    <td class="font-medium">
                        {{ $cost->cost_code }}
                    </td>

                    <td>
                        {{ $cost->branch->name ?? '-' }}
                    </td>

                    <td>
                        {{ $cost->departure?->departure_date
                            ? \Carbon\Carbon::parse($cost->departure->departure_date)->format('d M Y')
                            : '-' }}
                    </td>

                    <td class="capitalize">
                        {{ $cost->category }}
                    </td>

                    <td>
                        {{ $cost->description }}
                    </td>

                    <td class="font-semibold">
                        Rp {{ number_format($cost->amount,0,',','.') }}
                    </td>

                    {{-- STATUS --}}
                    <td>
                        @if($cost->status === 'approved')
                            <span class="badge-success">Approved</span>
                        @elseif($cost->status === 'draft')
                            <span class="badge-warning">Draft</span>
                        @else
                            <span class="badge-danger">Rejected</span>
                        @endif
                    </td>

                    <td>
                        {{ $cost->created_at?->format('d M Y') }}
                    </td>

                    {{-- ACTION --}}
                    <td class="text-right">
                        <div class="flex gap-2 justify-end">

                            @can('update', $cost)
                                <a href="{{ route('finance.costs.edit',$cost) }}"
                                   class="btn btn-secondary btn-xs">
                                    Edit
                                </a>
                            @endcan

                            @can('approve', $cost)
                                @if($cost->status === 'draft')
                                    <form method="POST"
                                          action="{{ route('finance.costs.approve',$cost) }}">
                                        @csrf
                                        <button class="btn btn-primary btn-xs">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                            @endcan

                            @can('delete', $cost)
                                @if($cost->status === 'draft')
                                    <form method="POST"
                                          action="{{ route('finance.costs.destroy',$cost) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-xs">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            @endcan

                        </div>
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="9"
                        class="text-center py-12 text-gray-400">
                        Belum ada data cost
                    </td>
                </tr>

            @endforelse

            </tbody>
        </table>

    </div>

    {{-- PAGINATION --}}
    <div>
        {{ $costs->links() }}
    </div>

</div>

@endsection