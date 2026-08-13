@extends('layouts.cabang')

@section('title', 'Edit Jamaah')

@section('content')
<form method="POST"
      action="{{ route('cabang.jamaah.update', $jamaah->id) }}"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('cabang.jamaah._form')
</form>
@endsection
