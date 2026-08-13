@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <form action="{{ route('admin.partner.store') }}" 
          method="POST" enctype="multipart/form-data">
        @csrf

        @include('admin.partner._form')

    </form>

</div>
@endsection
