@extends('layouts.app')

@section('title', 'Branches')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Branches</h1>

    @can('branch.create')
        <a href="{{ route('branches.create') }}"
           class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            + Create Branch
        </a>
    @endcan
</div>

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-4 text-left">Name</th>
                <th class="px-6 py-4 text-left">Code</th>
                <th class="px-6 py-4 text-left">City</th>
                <th class="px-6 py-4 text-left">Status</th>
                <th class="px-6 py-4 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($branches as $branch)
                <tr>
                    <td class="px-6 py-4 font-medium">
                        {{ $branch->name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $branch->code }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $branch->city ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($branch->is_active)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">
                                Active
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">

                        @can('branch.update')
                            <a href="{{ route('branches.edit', $branch) }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>
                        @endcan

                        @can('branch.delete')
                            <form action="{{ route('branches.destroy', $branch) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete branch?')"
                                        class="text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>
                        @endcan

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-6 text-center text-gray-500">
                        No branches found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection