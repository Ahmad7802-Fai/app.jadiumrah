@extends('layouts.app')

@section('title', 'Create Role')

@section('content')

<h1 class="text-2xl font-bold mb-6">Create Role</h1>

<div class="bg-white rounded-2xl shadow-sm p-6">

<form method="POST" action="{{ route('roles.store') }}">
    @csrf

    <div class="mb-5">
        <label class="block text-sm mb-2">Role Name</label>
        <input type="text" name="name"
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
                           value="{{ $permission->name }}">
                    {{ $permission->name }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="mt-6 text-right">
        <button class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            Save
        </button>
    </div>
</form>

</div>

@endsection