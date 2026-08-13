@extends('layouts.app')

@section('page-title','Edit Flight')

@section('content')

<div class="bg-white p-6 rounded-xl shadow">

    <form method="POST"
          action="{{ route('ticketing.flights.update', $flight->id) }}"
          class="space-y-6">

        @csrf
        @method('PUT')

        @include('ticketing.flights._form', ['flight' => $flight])

        <div class="flex justify-end">
            <button class="px-6 py-2 bg-primary-600 text-white rounded-lg">
                Update Flight
            </button>
        </div>

    </form>

</div>

@endsection