@extends('layouts.app')

@section('title','Buat Voucher')

@section('content')
<div class="card">
    <form method="POST"
          action="{{ route('marketing.vouchers.store') }}">
        @include('marketing.vouchers._form')
    </form>
</div>
@endsection