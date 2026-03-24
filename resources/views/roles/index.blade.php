@extends('layouts.app')

@section('title', 'Roles')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Roles</h1>

    @can('role.create')
        <a href="{{ route('roles.create') }}"
           class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            + Create Role
        </a>
    @endcan
</div>

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-4 text-left">Role Name</th>
                <th class="px-6 py-4 text-left">Permissions</th>
                <th class="px-6 py-4 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($roles as $role)
                <tr>
                    <td class="px-6 py-4 font-medium">
                        {{ $role->name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $role->permissions_count }}
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">

                        @can('role.update')
                            <a href="{{ route('roles.edit', $role) }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>
                        @endcan

                        @can('role.delete')
                            @if($role->name !== 'SUPERADMIN')
                                <form action="{{ route('roles.destroy', $role) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete role?')"
                                            class="text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        @endcan

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection