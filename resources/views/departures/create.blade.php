@extends('layouts.app')

@section('title','Tambah Departure')

@section('content')

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold">Tambah Departure</h1>
        <p class="text-sm text-gray-500">
            Buat jadwal keberangkatan baru
        </p>
    </div>

    <div class="card">
        <form action="{{ route('departures.store') }}" method="POST">
            @include('departures._form')
        </form>
    </div>

</div>

@endsection