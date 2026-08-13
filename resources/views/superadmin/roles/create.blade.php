@extends('layouts.admin')

@section('title', 'Tambah Role')

@section('content')
<div class="page-container">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Role</h1>
            <p class="text-muted text-sm">
                Buat role baru dan tentukan hak akses sistem
            </p>
        </div>

        <a href="{{ route('superadmin.roles.index') }}"
           class="btn btn-outline-primary btn-sm">
            ← Kembali
        </a>
    </div>

    {{-- ===============================
       FORM CARD
    ================================ --}}
    <div class="card card-hover">

        <div class="card-header">
            <div class="card-title">
                Informasi Role
            </div>
        </div>

        <div class="card-body">

            <form method="POST"
                  action="{{ route('superadmin.roles.store') }}"
                  class="form">
                @csrf

                @php
                    $permissions = $permissions
                        ?? \App\Models\Permission::orderBy('perm_name')->get();
                @endphp

                {{-- ===============================
                   ROLE FORM (REUSE)
                ================================ --}}
                @include('superadmin.roles._form')

                {{-- ===============================
                   ACTIONS
                ================================ --}}
                <div class="form-actions">
                    <a href="{{ route('superadmin.roles.index') }}"
                       class="btn btn-secondary">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        💾 Simpan Role
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
