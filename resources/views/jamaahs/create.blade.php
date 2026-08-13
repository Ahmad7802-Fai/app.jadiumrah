@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-xl shadow">
    <h1 class="text-xl font-bold mb-6">Tambah Jamaah</h1>

    <form method="POST" action="{{ route('jamaah.store') }}">
        @include('jamaahs.form')
    </form>
</div>
@endsection