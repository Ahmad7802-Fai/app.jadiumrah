@extends('layouts.app')

@section('title','Edit User')

@section('content')

<h1 class="text-2xl font-bold mb-6">Edit User</h1>

<div class="bg-white rounded-2xl shadow-sm p-6">

<form method="POST" action="{{ route('users.update',$user) }}">
    @method('PUT')
    @include('users._form')
</form>

</div>

@endsection