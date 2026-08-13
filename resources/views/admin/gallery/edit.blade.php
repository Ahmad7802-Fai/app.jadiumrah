@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h4 class="fw-bold mb-3">Edit Foto Gallery</h4>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">

            <form action="{{ route('admin.gallery.update', $item->id) }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('admin.gallery._form', ['item' => $item])
            </form>

        </div>
    </div>

</div>
@endsection
