@extends('layouts.admin')

@section('content')
<div class="dashboard-container">

    {{-- HEADER --}}
    @include('dashboard.partials.header')

    {{-- CARDS --}}
    @include('dashboard.partials.cards')

    {{-- CHART --}}
    @includeWhen(isset($chart), 'dashboard.partials.chart')

    {{-- COMPARISON --}}
    @includeWhen(isset($compare), 'dashboard.partials.compare')

</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
