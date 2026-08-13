@extends('layouts.app')

@section('title','Tambah Pembayaran')

@section('content')

<div class="max-w-xl mx-auto card">

    <h2 class="text-lg font-semibold mb-6">
        Tambah Pembayaran
    </h2>

    <form action="{{ route('finance.payments.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @include('finance.payments._form')

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('bookings.show',$booking) }}"
               class="btn btn-secondary">
                Batal
            </a>

            <button type="submit"
                    class="btn btn-primary">
                Simpan Pembayaran
            </button>
        </div>

    </form>

</div>

@endsection