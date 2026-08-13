@extends('layouts.app')

@section('title', 'Commission Schemes')

@section('content')

<div class="page-container">

    {{-- ================= HEADER ================= --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Commission Schemes</h1>
            <p class="page-subtitle">
                Manage company commission configuration per year
            </p>
        </div>

        @can('commission.create')
            <a href="{{ route('commission.schemes.create') }}"
               class="btn-primary">
                + Create Scheme
            </a>
        @endcan
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card">

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($schemes as $scheme)
                        <tr>
                            <td class="font-semibold">
                                {{ $scheme->name }}
                            </td>

                            <td>
                                {{ $scheme->year }}
                            </td>

                            <td>
                                @if($scheme->is_active)
                                    <span class="badge-success">
                                        Active
                                    </span>
                                @else
                                    <span class="badge-gray">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="text-right space-x-3">

                                @can('commission.update')
                                    <a href="{{ route('commission.schemes.edit', $scheme) }}"
                                       class="text-blue-500 hover:underline">
                                        Edit
                                    </a>
                                @endcan

                                @can('commission.delete')
                                    <form method="POST"
                                          action="{{ route('commission.schemes.destroy', $scheme) }}"
                                          class="inline-block"
                                          onsubmit="return confirm('Delete this scheme?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-500 hover:underline">
                                            Delete
                                        </button>
                                    </form>
                                @endcan

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-6">
                                No commission scheme found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= PAGINATION ================= --}}
        <div class="mt-6">
            {{ $schemes->links() }}
        </div>

    </div>

</div>

@endsection