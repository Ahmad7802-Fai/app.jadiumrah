@extends('layouts.agent')

@section('page-title','Tambah Jamaah')

@section('content')
<form method="POST"
      action="{{ route('agent.jamaah.store') }}">
    @csrf

    @include('agent.jamaah.partials._form')
</form>
@endsection
