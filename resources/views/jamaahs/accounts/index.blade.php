@extends('layouts.app')

@section('title','Akun Jamaah')

@section('content')

<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Akun Jamaah</h1>
            <p class="text-sm text-gray-500">
                Kelola akun login jamaah
            </p>
        </div>

        {{-- Bulk Create --}}
        <form action="{{ route('jamaah.account.bulk-create') }}" method="POST">
            @csrf
            <button class="btn btn-success">
                Bulk Create Akun
            </button>
        </form>
    </div>

    {{-- ================= FILTER ================= --}}
    <div class="card p-4">
        <form method="GET" class="flex gap-3 items-center">

            <select name="status" class="form-select w-48">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                    Sudah Punya Akun
                </option>
                <option value="no-account" {{ request('status') == 'no-account' ? 'selected' : '' }}>
                    Belum Ada Akun
                </option>
            </select>

            <button class="btn btn-primary btn-sm">
                Filter
            </button>

        </form>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Status Akun</th>
                    <th width="280">Action</th>
                </tr>
            </thead>
            <tbody>

            @forelse($jamaahs as $jamaah)
                <tr>
                    <td>{{ $jamaah->jamaah_code }}</td>
                    <td>{{ $jamaah->nama_lengkap }}</td>
                    <td>{{ $jamaah->email ?? '-' }}</td>

                    <td>
                        @if($jamaah->user)

                            @if($jamaah->user->is_active ?? true)
                                <span class="badge-success">Aktif</span>
                            @else
                                <span class="badge-warning">Nonaktif</span>
                            @endif

                        @else
                            <span class="badge-danger">Belum Ada Akun</span>
                        @endif
                    </td>

                    <td>
                        <div class="flex flex-wrap gap-2">

                            {{-- CREATE --}}
                            @if(!$jamaah->user)
                                <form action="{{ route('jamaah.account.create',$jamaah) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-primary btn-sm">
                                        Buat Akun
                                    </button>
                                </form>
                            @else

                                {{-- RESET --}}
                                <form action="{{ route('jamaah.account.reset',$jamaah) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-warning btn-sm">
                                        Reset
                                    </button>
                                </form>

                                {{-- DEACTIVATE --}}
                                @if($jamaah->user->is_active ?? true)
                                    <form action="{{ route('jamaah.account.deactivate',$jamaah) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-danger btn-sm">
                                            Nonaktifkan
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('jamaah.account.activate',$jamaah) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-success btn-sm">
                                            Aktifkan
                                        </button>
                                    </form>
                                @endif

                                {{-- KIRIM WA --}}
                                <form action="{{ route('jamaah.account.send-wa',$jamaah) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-secondary btn-sm">
                                        Kirim WA
                                    </button>
                                </form>

                            @endif

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-400">
                        Tidak ada data
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>

    {{ $jamaahs->links() }}

</div>

@endsection