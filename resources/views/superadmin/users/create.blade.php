@extends('layouts.admin')

@section('title','Tambah User')

@section('content')
<div class="page-container">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah User</h1>
            <p class="text-muted text-sm">
                Buat akun user baru untuk sistem
            </p>
        </div>

        <a href="{{ route('superadmin.users.index') }}"
           class="btn btn-outline-primary btn-sm">
            ← Kembali
        </a>
    </div>

    {{-- ===============================
       FORM CARD
    ================================ --}}
    <div class="card card-hover" style="max-width:720px">
        <div class="card-header">
            <span class="card-title">Informasi User</span>
        </div>

        <div class="card-body">

            <form method="POST"
                  action="{{ route('superadmin.users.store') }}"
                  class="form form-grid">
                @csrf

                {{-- NAMA --}}
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text"
                           name="nama"
                           value="{{ old('nama') }}"
                           class="form-control @error('nama') is-invalid @enderror"
                           placeholder="Nama lengkap user">

                    @error('nama')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div class="form-group">
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="email@domain.com">

                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ROLE --}}
                <div class="form-group">
                    <label>Role</label>
                    <select name="role"
                            class="form-select @error('role') is-invalid @enderror"
                            required>
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}"
                                @selected(old('role') === $role)>
                                {{ $role }}
                            </option>
                        @endforeach
                    </select>

                    @error('role')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- PASSWORD --}}
                <div class="form-group">
                    <label>Password</label>
                    <input type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimal 6 karakter">

                    <div class="form-text">
                        Wajib diisi saat pembuatan user
                    </div>

                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ACTION --}}
                <div class="form-actions">
                    <a href="{{ route('superadmin.users.index') }}"
                       class="btn btn-secondary">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan User
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
