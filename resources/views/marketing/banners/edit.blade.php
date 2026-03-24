@extends('layouts.app')

@section('title','Edit Banner')

@section('content')

<div class="card">
    <h2 class="text-lg font-semibold mb-6">Edit Banner</h2>

    <form method="POST"
          action="{{ route('marketing.banners.update',$banner) }}"
          enctype="multipart/form-data">

        @method('PUT')

        @include('marketing.banners._form')

    </form>
</div>

@endsection