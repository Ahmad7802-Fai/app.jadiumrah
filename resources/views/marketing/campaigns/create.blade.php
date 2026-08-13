@extends('layouts.app')

@section('title','Buat Campaign')

@section('content')

<div class="max-w-4xl mx-auto card">

    <h2 class="text-lg font-semibold mb-6">
        Buat Campaign
    </h2>

    <form method="POST"
          action="{{ route('marketing.campaigns.store') }}">

        @include('marketing.campaigns._form')

        <div class="mt-8 flex justify-end">
            <button class="btn btn-primary">
                Simpan Campaign
            </button>
        </div>

    </form>

</div>

@endsection