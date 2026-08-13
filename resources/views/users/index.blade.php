@extends('layouts.app')

@section('title', 'Users')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Users</h1>

    @can('user.create')
        <a href="{{ route('users.create') }}"
           class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            + Create User
        </a>
    @endcan
</div>

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-4 text-left">Name</th>
                <th class="px-6 py-4 text-left">Email</th>
                <th class="px-6 py-4 text-left">Role</th>
                <th class="px-6 py-4 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        {{ $user->getRoleNames()->implode(', ') }}
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">

                        @can('user.update')
                            <a href="{{ route('users.edit', $user) }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>
                        @endcan

                        @can('user.delete')
                            <form action="{{ route('users.destroy', $user) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete user?')"
                                        class="text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>
                        @endcan

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                        No users found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection