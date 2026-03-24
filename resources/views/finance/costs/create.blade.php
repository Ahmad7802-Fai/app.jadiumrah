@extends('layouts.app')

@section('title','Tambah Cost')

@section('content')

<div class="max-w-3xl mx-auto card">

    <h2 class="text-lg font-semibold mb-6">
        Tambah Cost
    </h2>

    <form action="{{ route('finance.costs.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @include('finance.costs._form')

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('finance.costs.index') }}"
               class="btn btn-secondary">
                Batal
            </a>

            <button class="btn btn-primary">
                Simpan Cost
            </button>
        </div>

    </form>

</div>

@endsection