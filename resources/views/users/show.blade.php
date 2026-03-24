@extends('layouts.app')

@section('title', 'User Detail')

@section('content')

<h1 class="text-2xl font-bold mb-6">User Detail</h1>

<div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">

    <div>
        <div class="text-sm text-gray-500">Name</div>
        <div class="text-lg font-medium">{{ $user->name }}</div>
    </div>

    <div>
        <div class="text-sm text-gray-500">Email</div>
        <div class="text-lg font-medium">{{ $user->email }}</div>
    </div>

    <div>
        <div class="text-sm text-gray-500">Role</div>
        <div class="text-lg font-medium">
            {{ $user->getRoleNames()->implode(', ') }}
        </div>
    </div>

</div>

@endsection