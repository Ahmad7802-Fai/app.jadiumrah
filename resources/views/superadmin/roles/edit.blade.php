@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
<div class="page-container">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Role</h1>
            <p class="text-muted text-sm">
                Perbarui nama dan hak akses role
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
                  action="{{ route('superadmin.roles.update', $role->id) }}"
                  class="form">
                @csrf
                @method('PUT')

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
                        💾 Update Role
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
