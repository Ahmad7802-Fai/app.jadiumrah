@extends('layouts.app')

@section('title','Edit Add-On')

@section('content')

<div class="max-w-3xl mx-auto card">

    <h2 class="text-lg font-semibold mb-6">
        Edit Add-On
    </h2>

    <form action="{{ route('marketing.addons.update',$addon) }}"
          method="POST">

        @method('PUT')

        @include('marketing.addons._form')

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('marketing.addons.index') }}"
               class="btn btn-secondary">
                Batal
            </a>

            <button class="btn btn-primary">
                Update
            </button>
        </div>

    </form>

</div>

@endsection