@extends('layouts.app')

@section('title','Data Jamaah')

@section('content')

<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Data Jamaah
            </h1>
            <p class="text-sm text-gray-500">
                Kelola data jamaah umrah
            </p>
        </div>

        @can('create', \App\Models\Jamaah::class)
        <a href="{{ route('jamaah.create') }}"
           class="btn btn-primary gap-2">
            <span>+</span>
            Tambah Jamaah
        </a>
        @endcan

    </div>


    {{-- ================= SEARCH CARD ================= --}}
    <div class="card">

        <form method="GET" class="flex gap-3">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari nama, kode, atau phone..."
                   class="input">

            <button type="submit" class="btn btn-secondary">
                Cari
            </button>

        </form>

    </div>


    {{-- ================= TABLE ================= --}}
    <div class="card p-0">

        <div class="table-wrapper">

            <table class="table">

                <thead>
                    <tr>
                        <th>KODE</th>
                        <th>NAMA</th>
                        <th>CABANG</th>
                        <th>AGENT</th>
                        <th>PHONE</th>
                        <th>STATUS</th>
                        <th class="text-right">AKSI</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($jamaahs as $jamaah)
                    <tr class="hover:bg-gray-50 transition">

                        <td class="font-medium">
                            {{ $jamaah->jamaah_code }}
                        </td>

                        <td>
                            {{ $jamaah->nama_lengkap }}
                        </td>

                        <td>
                            {{ $jamaah->branch->name ?? '-' }}
                        </td>

                        <td>
                            {{ $jamaah->agent->name ?? '-' }}
                        </td>

                        <td>
                            {{ $jamaah->phone ?? '-' }}
                        </td>

                        <td>
                            @if($jamaah->approval_status === 'approved')
                                <span class="badge badge-success">
                                    Approved
                                </span>
                            @elseif($jamaah->approval_status === 'rejected')
                                <span class="badge badge-danger">
                                    Rejected
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    Pending
                                </span>
                            @endif
                        </td>

                        <td class="text-right space-x-2">

                            <a href="{{ route('jamaah.show',$jamaah) }}"
                               class="btn btn-outline text-xs">
                                Detail
                            </a>

                            @can('update', $jamaah)
                            <a href="{{ route('jamaah.edit',$jamaah) }}"
                               class="btn btn-secondary text-xs">
                                Edit
                            </a>
                            @endcan

                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7"
                            class="text-center py-10 text-gray-400">
                            Belum ada data jamaah.
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ================= PAGINATION ================= --}}
    <div>
        {{ $jamaahs->withQueryString()->links() }}
    </div>

</div>

@endsection