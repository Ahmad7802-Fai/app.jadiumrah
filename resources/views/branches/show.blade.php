@extends('layouts.app')

@section('title', 'Branch Detail')

@section('content')

<h1 class="text-2xl font-bold mb-6">Branch Detail</h1>

<div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">

    <div>
        <div class="text-sm text-gray-500">Name</div>
        <div class="text-lg font-medium">{{ $branch->name }}</div>
    </div>

    <div>
        <div class="text-sm text-gray-500">Code</div>
        <div class="text-lg font-medium">{{ $branch->code }}</div>
    </div>

    <div>
        <div class="text-sm text-gray-500">City</div>
        <div class="text-lg font-medium">{{ $branch->city ?? '-' }}</div>
    </div>

    <div>
        <div class="text-sm text-gray-500">Address</div>
        <div class="text-lg font-medium">{{ $branch->address ?? '-' }}</div>
    </div>

    <div>
        <div class="text-sm text-gray-500">Phone</div>
        <div class="text-lg font-medium">{{ $branch->phone ?? '-' }}</div>
    </div>

</div>

@endsection