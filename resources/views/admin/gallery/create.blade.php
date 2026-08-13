@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h4 class="fw-bold mb-3">Tambah Foto Gallery</h4>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">

            <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.gallery._form')
            </form>

        </div>
    </div>

</div>
@endsection
