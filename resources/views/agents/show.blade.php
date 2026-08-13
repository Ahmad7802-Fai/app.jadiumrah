@extends('layouts.app')

@section('title', 'Agent Detail')

@section('content')

<h1 class="text-2xl font-bold mb-6">Agent Detail</h1>

<div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">

    <div>
        <div class="text-sm text-gray-500">Name</div>
        <div class="text-lg font-medium">{{ $agent->nama }}</div>
    </div>

    <div>
        <div class="text-sm text-gray-500">Email</div>
        <div class="text-lg font-medium">{{ $agent->user->email }}</div>
    </div>

    <div>
        <div class="text-sm text-gray-500">Branch</div>
        <div class="text-lg font-medium">{{ $agent->branch->name }}</div>
    </div>

    <div>
        <div class="text-sm text-gray-500">Phone</div>
        <div class="text-lg font-medium">{{ $agent->phone ?? '-' }}</div>
    </div>

</div>

@endsection