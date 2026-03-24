@extends('layouts.app')

@section('title','Edit Campaign')

@section('content')

<div class="max-w-4xl mx-auto card">

    <h2 class="text-lg font-semibold mb-6">
        Edit Campaign
    </h2>

    <form method="POST"
          action="{{ route('marketing.campaigns.update',$campaign) }}">
        @method('PUT')

        @include('marketing.campaigns._form')

        <div class="mt-8 flex justify-end">
            <button class="btn btn-primary">
                Update Campaign
            </button>
        </div>

    </form>

</div>

@endsection