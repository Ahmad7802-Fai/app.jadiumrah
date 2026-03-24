@extends('layouts.app')

@section('title','Tambah Add-On')

@section('content')

<div class="max-w-3xl mx-auto card">

    <h2 class="text-lg font-semibold mb-6">
        Tambah Add-On
    </h2>

    <form action="{{ route('marketing.addons.store') }}"
          method="POST">

        @include('marketing.addons._form')

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('marketing.addons.index') }}"
               class="btn btn-secondary">
                Batal
            </a>

            <button class="btn btn-primary">
                Simpan
            </button>
        </div>

    </form>

</div>

@endsection