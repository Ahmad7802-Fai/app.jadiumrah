@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')

<h1 class="text-2xl font-bold mb-6">Edit Role</h1>

<div class="bg-white rounded-2xl shadow-sm p-6">

<form method="POST" action="{{ route('roles.update', $role) }}">
    @csrf
    @method('PUT')

    <div class="mb-5">
        <label class="block text-sm mb-2">Role Name</label>
        <input type="text" name="name"
               value="{{ $role->name }}"
               class="w-full border rounded-lg px-4 py-2"
               required>
    </div>

    <div>
        <label class="block text-sm mb-4 font-medium">Permissions</label>

        <div class="grid grid-cols-3 gap-3 text-sm">
            @foreach($permissions as $permission)
                <label class="flex items-center gap-2">
                    <input type="checkbox"
                           name="permissions[]"
                           value="{{ $permission->name }}"
                           {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                    {{ $permission->name }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="mt-6 text-right">
        <button class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            Update
        </button>
    </div>

</form>

</div>

@endsection