@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <form action="{{ route('admin.partner.update', $partner->id) }}" 
          method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.partner._form')

    </form>

</div>
@endsection
