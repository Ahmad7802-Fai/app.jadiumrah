@extends('layouts.admin')

@section('title', 'Edit Jamaah')

@section('content')
<div class="page-jamaah">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Jamaah</h1>
            <p class="text-muted text-sm">
                Perbarui data jamaah
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('operator.daftar-jamaah.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- ===============================
       FORM CARD
    ================================ --}}
    <div class="card card-hover">
        <div class="card-body">

            <form method="POST"
                  action="{{ route('operator.daftar-jamaah.update', $jamaah->id) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('operator.daftar-jamaah._form', [
                    'jamaah' => $jamaah
                ])

            </form>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/jamaah-form.js') }}"></script>
@endpush
