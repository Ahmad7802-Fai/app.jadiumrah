@extends('layouts.app')

@section('title','Edit Departure')

@section('content')

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold">Edit Departure</h1>
        <p class="text-sm text-gray-500">
            Perbarui data keberangkatan
        </p>
    </div>

    <div class="card">
        <form action="{{ route('departures.update', $departure) }}" method="POST">
            @method('PUT')
            @include('departures._form')
        </form>
    </div>

</div>

@endsection