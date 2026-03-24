@extends('layouts.app')

@section('title', 'Create Agent')

@section('content')

<h1 class="text-2xl font-bold mb-6">Create Agent</h1>

<div class="bg-white rounded-2xl shadow-sm p-6">

    <form method="POST" action="{{ route('agents.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm mb-2">Name</label>
            <input type="text" name="nama"
                   class="w-full border rounded-lg px-4 py-2"
                   required>
        </div>

        <div>
            <label class="block text-sm mb-2">Email</label>
            <input type="email" name="email"
                   class="w-full border rounded-lg px-4 py-2"
                   required>
        </div>

        <div>
            <label class="block text-sm mb-2">Password</label>
            <input type="password" name="password"
                   class="w-full border rounded-lg px-4 py-2">
        </div>

        <div>
            <label class="block text-sm mb-2">Branch</label>
            <select name="branch_id"
                    class="w-full border rounded-lg px-4 py-2"
                    required>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm mb-2">Phone</label>
            <input type="text" name="phone"
                   class="w-full border rounded-lg px-4 py-2">
        </div>

        <div class="flex justify-end">
            <button class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Save
            </button>
        </div>
    </form>

</div>

@endsection