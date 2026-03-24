@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <h1 class="text-2xl font-bold">
        Dashboard
    </h1>

    {{-- PUSAT / SUPERADMIN --}}
    @if(isset($stats['total_branches']))
        @include('dashboard.partials.pusat', ['stats' => $stats])
    @endif

    {{-- CABANG --}}
    @if(isset($stats['branch_commission_received']))
        @include('dashboard.partials.cabang', ['stats' => $stats])
    @endif

    {{-- AGENT --}}
    @if(isset($stats['my_commission']))
        @include('dashboard.partials.agent', ['stats' => $stats])
    @endif

    {{-- JAMAAH --}}
    @if(isset($stats['my_bookings']) && count($stats) === 1)
        @include('dashboard.partials.jamaah', ['stats' => $stats])
    @endif

</div>

@endsection