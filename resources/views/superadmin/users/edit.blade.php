@extends('layouts.admin')

@section('title','Edit User')

@section('content')
<div class="page-container">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit User</h1>
            <p class="text-muted text-sm">
                Perbarui informasi akun pengguna
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
                  action="{{ route('superadmin.users.update', $user->id) }}"
                  class="form form-grid">
                @csrf
                @method('PUT')

                {{-- NAMA --}}
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text"
                           name="nama"
                           value="{{ old('nama', $user->nama) }}"
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
                           value="{{ old('email', $user->email) }}"
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
                        @foreach($roles as $role)
                            <option value="{{ $role }}"
                                @selected(old('role', $user->role) === $role)>
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
                    <label>Password (Opsional)</label>
                    <input type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Biarkan kosong jika tidak diubah">

                    <div class="form-text">
                        Isi hanya jika ingin mengganti password
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
                        Update User
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
