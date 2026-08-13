@extends('layouts.agent')

@section('title', 'Edit Jamaah')

@section('page-title','Edit Jamaah')
@section('page-subtitle','Perbarui data jamaah')

@section('content')
<form method="POST"
      action="{{ route('agent.jamaah.update', $jamaah->id) }}"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('agent.jamaah.partials._form', [
        'jamaah' => $jamaah,
        'keberangkatan' => $keberangkatan,
    ])

</form>
@endsection
