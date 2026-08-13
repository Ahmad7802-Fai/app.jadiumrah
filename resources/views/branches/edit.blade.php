@extends('layouts.app')

@section('title', 'Edit Branch')

@section('content')

<h1 class="text-2xl font-bold mb-6">Edit Branch</h1>

<div class="bg-white rounded-2xl shadow-sm p-6">

    <form method="POST" action="{{ route('branches.update', $branch) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm mb-2">Branch Name</label>
            <input type="text" name="name"
                   value="{{ $branch->name }}"
                   class="w-full border rounded-lg px-4 py-2"
                   required>
        </div>

        <div>
            <label class="block text-sm mb-2">Code</label>
            <input type="text" name="code"
                   value="{{ $branch->code }}"
                   class="w-full border rounded-lg px-4 py-2"
                   required>
        </div>

        <div>
            <label class="block text-sm mb-2">City</label>
            <input type="text" name="city"
                   value="{{ $branch->city }}"
                   class="w-full border rounded-lg px-4 py-2">
        </div>

        <div>
            <label class="block text-sm mb-2">Address</label>
            <textarea name="address"
                      class="w-full border rounded-lg px-4 py-2">{{ $branch->address }}</textarea>
        </div>

        <div>
            <label class="block text-sm mb-2">Phone</label>
            <input type="text" name="phone"
                   value="{{ $branch->phone }}"
                   class="w-full border rounded-lg px-4 py-2">
        </div>

        <div class="flex justify-end">
            <button class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Update
            </button>
        </div>
    </form>

</div>

@endsection