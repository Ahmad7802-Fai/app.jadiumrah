@extends('layouts.app')

@section('title','Buat Flash Sale')

@section('content')
<div class="card">
    <form method="POST"
          action="{{ route('marketing.flash-sales.store') }}">
        @include('marketing.flash_sales._form')
    </form>
</div>
@endsection