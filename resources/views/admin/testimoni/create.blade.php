@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h4 class="fw-bold mb-4">Tambah Testimoni</h4>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">

            <form action="{{ route('admin.testimoni.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.testimoni.form')

                <button class="btn btn-primary rounded-pill px-4 mt-3">Simpan</button>
            </form>

        </div>
    </div>

</div>
@endsection
