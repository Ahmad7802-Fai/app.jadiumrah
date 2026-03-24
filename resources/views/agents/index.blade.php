@extends('layouts.app')

@section('title', 'Agents')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Agents</h1>

    @can('agent.create')
        <a href="{{ route('agents.create') }}"
           class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
            + Create Agent
        </a>
    @endcan
</div>

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-4 text-left">Name</th>
                <th class="px-6 py-4 text-left">Branch</th>
                <th class="px-6 py-4 text-left">Email</th>
                <th class="px-6 py-4 text-left">Phone</th>
                <th class="px-6 py-4 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($agents as $agent)
                <tr>
                    <td class="px-6 py-4 font-medium">
                        {{ $agent->nama }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $agent->branch->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $agent->user->email }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $agent->phone ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">

                        @can('agent.update')
                            <a href="{{ route('agents.edit', $agent) }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>
                        @endcan

                        @can('agent.delete')
                            <form action="{{ route('agents.destroy', $agent) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete agent?')"
                                        class="text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>
                        @endcan

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection