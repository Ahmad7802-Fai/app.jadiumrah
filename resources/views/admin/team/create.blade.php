@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <form action="{{ route('admin.team.store') }}" 
          method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.team._form')
    </form>

</div>
@endsection
