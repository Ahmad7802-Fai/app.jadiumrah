@extends('layouts.app')

@section('title','Create Booking')

@section('content')

<h1 class="text-2xl font-bold mb-6">Create Booking</h1>

<form action="{{ route('bookings.store') }}" method="POST">
    @include('bookings._form')
</form>

@endsection