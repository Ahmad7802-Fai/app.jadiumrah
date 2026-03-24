@extends('layouts.app')

@section('title','Tambah Banner')

@section('content')

<div class="card">
    <h2 class="text-lg font-semibold mb-6">Tambah Banner</h2>

    <form method="POST"
          action="{{ route('marketing.banners.store') }}"
          enctype="multipart/form-data">

        @include('marketing.banners._form')

    </form>
</div>

@endsection