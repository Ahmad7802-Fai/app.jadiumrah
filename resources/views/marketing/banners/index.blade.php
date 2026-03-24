@extends('layouts.app')

@section('title','Marketing • Promo Banner')

@section('content')

<div class="space-y-8">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Promo Banner</h1>
            <p class="text-sm text-gray-500">
                Kelola banner landing page & aplikasi
            </p>
        </div>

        @can('banner.create')
        <a href="{{ route('marketing.banners.create') }}"
           class="btn btn-primary">
            + Tambah Banner
        </a>
        @endcan
    </div>

    {{-- TABLE --}}
    <div class="card table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Banner</th>
                    <th>Page</th>
                    <th>Status</th>
                    <th>Periode</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>

            @forelse($banners as $banner)
                <tr>
                    <td class="flex items-center gap-4">
                        <img src="{{ asset('storage/'.$banner->image_path) }}"
                             class="w-20 h-12 object-cover rounded">
                        <div>
                            <div class="font-semibold">
                                {{ $banner->title }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $banner->subtitle }}
                            </div>
                        </div>
                    </td>

                    <td>{{ $banner->page }}</td>

                    <td>
                        @if($banner->status === 'published')
                            <span class="badge-success">Published</span>
                        @elseif($banner->status === 'draft')
                            <span class="badge-warning">Draft</span>
                        @else
                            <span class="badge-danger">Archived</span>
                        @endif
                    </td>

                    <td class="text-sm text-gray-500">
                        {{ optional($banner->start_date)->format('d M Y') }}
                        -
                        {{ optional($banner->end_date)->format('d M Y') }}
                    </td>

                    <td class="text-right">
                        <div class="flex gap-2 justify-end">

                            @can('banner.update')
                            <a href="{{ route('marketing.banners.edit',$banner) }}"
                               class="btn btn-secondary btn-xs">
                                Edit
                            </a>
                            @endcan

                            @can('publish',$banner)
                            <form method="POST"
                                  action="{{ route('marketing.banners.publish',$banner) }}">
                                @csrf
                                <button class="btn btn-primary btn-xs">
                                    Publish
                                </button>
                            </form>
                            @endcan

                            @can('archive',$banner)
                            <form method="POST"
                                  action="{{ route('marketing.banners.archive',$banner) }}">
                                @csrf
                                <button class="btn btn-danger btn-xs">
                                    Archive
                                </button>
                            </form>
                            @endcan

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-12 text-gray-400">
                        Belum ada banner
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>

    {{ $banners->links() }}

</div>

@endsection