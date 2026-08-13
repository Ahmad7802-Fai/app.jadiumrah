@extends('layouts.app')

@section('title', 'Role Detail')

@section('content')

<h1 class="text-2xl font-bold mb-6">Role Detail</h1>

<div class="bg-white rounded-2xl shadow-sm p-6">

    <div class="mb-6">
        <div class="text-sm text-gray-500">Role Name</div>
        <div class="text-xl font-semibold">{{ $role->name }}</div>
    </div>

    <div>
        <div class="text-sm text-gray-500 mb-3">Permissions</div>

        <div class="grid grid-cols-3 gap-2 text-sm">
            @foreach($role->permissions as $permission)
                <div class="px-3 py-1 bg-gray-100 rounded-lg">
                    {{ $permission->name }}
                </div>
            @endforeach
        </div>
    </div>

</div>

@endsection