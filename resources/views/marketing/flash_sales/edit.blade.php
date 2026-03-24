@extends('layouts.app')

@section('title','Edit Flash Sale')

@section('content')
<div class="card">
    <form method="POST"
          action="{{ route('marketing.flash-sales.update',$flashSale) }}">
        @method('PUT')
        @include('marketing.flash_sales._form')
    </form>
</div>
@endsection