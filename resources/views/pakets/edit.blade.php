@extends('layouts.app')

@section('title','Edit Paket')

@section('content')

<div class="space-y-6">

    <h1 class="text-2xl font-bold">
        Edit Paket
    </h1>

    <form method="POST"
          action="{{ route('pakets.update',$paket) }}"
          enctype="multipart/form-data">

        @method('PUT')

        @include('pakets._form')

    </form>

</div>

@endsection