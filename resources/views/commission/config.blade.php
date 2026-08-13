@extends('layouts.app')

@section('title', 'Commission Configuration')

@section('content')

<div class="page-container">

    <div class="page-header">
        <div>
            <h1 class="page-title">
                Commission Configuration
            </h1>
            <p class="page-subtitle">
                Active Scheme: <strong>{{ $scheme->name }} ({{ $scheme->year }})</strong>
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">

        <table class="table">
            <thead>
                <tr>
                    <th>Branch</th>
                    <th>Company → Branch (Nominal)</th>
                    <th>Branch → Agent (%)</th>
                </tr>
            </thead>

            <tbody>
                @foreach($branches as $branch)

                    @php
                        $companyRule = $branch->companyRules->first();
                        $branchRule  = $branch->branchRules->first();
                    @endphp

                    <tr>
                        <td class="font-semibold">
                            {{ $branch->name }}
                        </td>

                        <td>
                            <form method="POST"
                                  action="{{ route('commission.config.company', $branch->id) }}">
                                @csrf

                                <div class="flex gap-2">
                                    <input type="number"
                                           name="amount_per_closing"
                                           value="{{ $companyRule->amount_per_closing ?? 0 }}"
                                           class="input w-40"
                                           min="0">

                                    <button class="btn-primary">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </td>

                        <td>
                            <form method="POST"
                                  action="{{ route('commission.config.agent', $branch->id) }}">
                                @csrf

                                <div class="flex gap-2">
                                    <input type="number"
                                           name="agent_percentage"
                                           value="{{ $branchRule->agent_percentage ?? 0 }}"
                                           class="input w-24"
                                           min="0"
                                           max="100">

                                    <button class="btn-primary">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>

                @endforeach
            </tbody>

        </table>

    </div>

</div>

@endsection