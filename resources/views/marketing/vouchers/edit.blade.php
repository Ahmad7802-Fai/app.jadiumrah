@extends('layouts.app')

@section('title','Edit Voucher')

@section('content')
<div class="card">
    <form method="POST"
          action="{{ route('marketing.vouchers.update',$voucher) }}">
        @method('PUT')
        @include('marketing.vouchers._form')
    </form>
</div>
@endsection