@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <form action="{{ route('admin.team.update', $team->id) }}" 
          method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.team._form')
    </form>

</div>
@endsection
