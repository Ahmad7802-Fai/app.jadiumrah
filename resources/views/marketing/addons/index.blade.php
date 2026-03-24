@extends('layouts.app')

@section('title','Marketing • Produk Add-On')

@section('content')

<div class="space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Produk Add-On
            </h1>
            <p class="text-sm text-gray-500">
                Kelola produk tambahan untuk booking
            </p>
        </div>

        @can('addon.create')
        <a href="{{ route('marketing.addons.create') }}"
           class="btn btn-primary">
            + Tambah Add-On
        </a>
        @endcan
    </div>

    {{-- ================= FILTER ================= --}}
    <div class="card-compact">
        <form method="GET" class="flex gap-4 items-end flex-wrap">

            <div>
                <label class="meta-label">Search</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Nama add-on..."
                       class="input w-64">
            </div>

            <div>
                <label class="meta-label">Status</label>
                <select name="status" class="input w-40">
                    <option value="">Semua</option>
                    <option value="1" {{ request('status')==='1'?'selected':'' }}>
                        Active
                    </option>
                    <option value="0" {{ request('status')==='0'?'selected':'' }}>
                        Inactive
                    </option>
                </select>
            </div>

            <button class="btn btn-secondary">
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
                    <th>Harga Jual</th>
                    <th>Cost</th>
                    <th>Status</th>
                    <th width="180" class="text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
            @forelse($addons as $addon)
                <tr>
                    <td class="font-medium">
                        {{ $addon->code }}
                    </td>

                    <td>
                        {{ $addon->name }}
                    </td>

                    <td>
                        Rp {{ number_format($addon->selling_price,0,',','.') }}
                    </td>

                    <td>
                        Rp {{ number_format($addon->cost_price,0,',','.') }}
                    </td>

                    <td>
                        @if($addon->is_active)
                            <span class="badge-success">Active</span>
                        @else
                            <span class="badge-danger">Inactive</span>
                        @endif
                    </td>

                    <td class="text-right">
                        <div class="flex gap-2 justify-end">

                            @can('addon.update')
                            <a href="{{ route('marketing.addons.edit',$addon) }}"
                               class="btn btn-secondary btn-xs">
                                Edit
                            </a>
                            @endcan

                            @can('addon.delete')
                            <form action="{{ route('marketing.addons.destroy',$addon) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-xs">
                                    Hapus
                                </button>
                            </form>
                            @endcan

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6"
                        class="text-center py-12 text-gray-400">
                        Belum ada data add-on
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

    </div>

    {{-- PAGINATION --}}
    <div>
        {{ $addons->withQueryString()->links() }}
    </div>

</div>

@endsection