@extends('layouts.admin')

@section('title', 'Akun Jamaah')

@section('content')
<div class="page-jamaah-user">

    {{-- =====================================================
       PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Akun Jamaah</h1>
            <p class="text-muted text-sm">
                Kelola akun login jamaah (email / no HP)
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('operator.jamaah-user.create') }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i>
                Buat Akun Jamaah
            </a>
        </div>
    </div>

    {{-- =====================================================
       FLASH MESSAGE
    ====================================================== --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('new_password'))
        <div class="alert alert-warning">
            <strong>Password Baru:</strong>
            <code class="ms-1">{{ session('new_password') }}</code>
            <div class="text-muted text-sm mt-1">
                ⚠️ Simpan sekarang. Password ini hanya ditampilkan sekali.
            </div>
        </div>
    @endif

    {{-- =====================================================
       TABLE (DESKTOP + MOBILE AUTO)
    ====================================================== --}}
    <div class="card card-hover">
        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-compact">

                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Jamaah</th>
                            <th>Akun Login</th>
                            <th class="table-center">Status</th>
                            <th>Update Terakhir</th>
                            <th class="table-right col-actions">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($users as $user)
                        <tr>

                            {{-- NO --}}
                            <td data-label="#">
                                {{ $loop->iteration }}
                            </td>

                            {{-- JAMAAH --}}
                            <td data-label="Jamaah">
                                <div class="fw-semibold">
                                    {{ $user->jamaah->nama_lengkap ?? '-' }}
                                </div>
                                <div class="text-muted text-sm">
                                    ID Jamaah: {{ $user->jamaah_id }}
                                </div>
                            </td>

                            {{-- AKUN --}}
                            <td data-label="Akun Login">
                                <div>{{ $user->email ?? '-' }}</div>
                                <div class="text-muted text-sm">
                                    {{ $user->phone ?? '-' }}
                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td data-label="Status" class="table-center">
                                <span class="badge {{ $user->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                    {{ $user->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                </span>
                            </td>

                            {{-- UPDATED --}}
                            <td data-label="Update Terakhir">
                                <span class="text-muted text-sm">
                                    {{ $user->updated_at?->format('d M Y H:i') ?? '-' }}
                                </span>
                            </td>

                            {{-- ACTION --}}
                            <td class="table-right col-actions">
                                <div class="table-actions">

                                    {{-- RESET PASSWORD --}}
                                    <form method="POST"
                                          action="{{ route('operator.jamaah-user.reset-password', $user->id) }}"
                                          onsubmit="return confirm('Reset password akun ini?')">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-outline-warning btn-xs"
                                                title="Reset Password">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </form>

                                    {{-- TOGGLE STATUS --}}
                                    <form method="POST"
                                          action="{{ route('operator.jamaah-user.toggle', $user->id) }}"
                                          onsubmit="return confirm('Ubah status akun ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-outline-secondary btn-xs"
                                                title="Aktif / Nonaktif">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty">
                                👤 Belum ada akun jamaah
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>

            </div>
        </div>
    </div>

    {{-- =====================================================
       PAGINATION
    ====================================================== --}}
    @if($users->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection
