@extends('layouts.app')

@section('title','Buat Paket')

@section('content')

<div class="space-y-6">

    <h1 class="text-2xl font-bold">
        Buat Paket Baru
    </h1>

    <form method="POST"
          action="{{ route('pakets.store') }}"
          enctype="multipart/form-data">

        @include('pakets._form')

    </form>

</div>

@endsection