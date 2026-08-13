@extends('layouts.app')

@section('page-title','Create Flight')

@section('content')

<div class="bg-white p-6 rounded-xl shadow">

    <form method="POST"
          action="{{ route('ticketing.flights.store') }}"
          class="space-y-6">

        @csrf

        @include('ticketing.flights._form')

        <div class="flex justify-end">
            <button class="px-6 py-2 bg-primary-600 text-white rounded-lg">
                Save Flight
            </button>
        </div>

    </form>

</div>

@endsection