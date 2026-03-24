@extends('layouts.app')

@section('title','Edit Booking')

@section('content')

<h1 class="text-2xl font-bold mb-6">Edit Booking</h1>

<form action="{{ route('bookings.update', $booking) }}" method="POST">
    @method('PUT')
    @include('bookings._form')
</form>

@endsection