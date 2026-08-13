@extends('dashboard.layouts.base')

@section('content')

<h1 class="page-title">{{ $title }}</h1>

@include('dashboard.partials.filter')

@include('dashboard.partials.cards', [
    'cards' => $cards
])

@include('dashboard.partials.chart', [
    'chart' => $chart
])

@include('dashboard.partials.chart-comparison', [
    'chart' => $chartComparison
])

@endsection
