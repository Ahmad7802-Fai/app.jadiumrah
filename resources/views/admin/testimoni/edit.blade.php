@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h4 class="fw-bold mb-4">Edit Testimoni</h4>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">

            <form action="{{ route('admin.testimoni.update', $item->id) }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('admin.testimoni.form')

                <button class="btn btn-primary rounded-pill px-4 mt-3">Update</button>
            </form>

        </div>
    </div>

</div>
@endsection
