@extends('layouts.app')

@section('title','Create User')

@section('content')

<h1 class="text-2xl font-bold mb-6">Create User</h1>

<div class="bg-white rounded-2xl shadow-sm p-6">

<form method="POST" action="{{ route('users.store') }}">
    @include('users._form')
</form>

</div>

@endsection