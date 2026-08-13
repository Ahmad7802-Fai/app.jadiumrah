@extends('layouts.app')

@section('title','Edit Paket')

@section('content')

<div class="space-y-6">

    <h1 class="text-2xl font-bold">
        Edit Paket
    </h1>

    {{-- ERROR VALIDATION --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded">
            <ul class="text-sm list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('pakets.update',$paket) }}"
          enctype="multipart/form-data"
          class="space-y-6">

        @csrf
        @method('PUT')

        @include('pakets._form', [
            'paket' => $paket,
            'destinations' => $destinations ?? []
        ])

    </form>

</div>

@endsection