@extends('layouts.app')

@section('title','Approval Jamaah')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold">Approval Jamaah</h1>
        <p class="text-sm text-gray-500">
            Daftar jamaah yang menunggu persetujuan
        </p>
    </div>

    {{-- TABLE --}}
    <div class="card table-wrapper">

        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Cabang</th>
                    <th>Agent</th>
                    <th>Tanggal Daftar</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            @forelse($jamaahs as $jamaah)
                <tr>
                    <td>{{ $jamaah->jamaah_code }}</td>

                    <td>
                        <div class="font-semibold">
                            {{ $jamaah->nama_lengkap }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $jamaah->phone ?? '-' }}
                        </div>
                    </td>

                    <td>{{ $jamaah->branch->name ?? '-' }}</td>
                    <td>{{ $jamaah->agent->name ?? '-' }}</td>

                    <td>
                        {{ $jamaah->created_at->format('d M Y') }}
                    </td>

                    <td class="flex gap-2">

                        {{-- APPROVE --}}
                        <form method="POST"
                              action="{{ route('jamaah.approve', $jamaah) }}">
                            @csrf
                            <button type="submit"
                                    class="btn btn-success">
                                Approve
                            </button>
                        </form>

                        {{-- REJECT --}}
                        <form method="POST"
                              action="{{ route('jamaah.reject', $jamaah) }}">
                            @csrf
                            <button type="submit"
                                    class="btn btn-danger">
                                Reject
                            </button>
                        </form>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6"
                        class="text-center py-6 text-gray-400">
                        Tidak ada jamaah pending approval
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>

    </div>

    {{ $jamaahs->links() }}

</div>

@endsection