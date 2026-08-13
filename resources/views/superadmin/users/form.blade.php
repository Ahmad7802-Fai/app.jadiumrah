{{-- =====================================================
   USER FORM (CREATE & EDIT)
===================================================== --}}

<div class="form form-grid">

    {{-- ===============================
       NAMA
    ================================ --}}
    <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text"
               name="nama"
               value="{{ old('nama', $user->nama ?? '') }}"
               class="form-control @error('nama') is-invalid @enderror"
               placeholder="Nama lengkap user"
               required>

        @error('nama')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    {{-- ===============================
       EMAIL
    ================================ --}}
    <div class="form-group">
        <label>Email</label>
        <input type="email"
               name="email"
               value="{{ old('email', $user->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror"
               placeholder="email@domain.com"
               required>

        @error('email')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    {{-- ===============================
       ROLE
    ================================ --}}
    <div class="form-group">
        <label>Role</label>
        <select name="role"
                class="form-select @error('role') is-invalid @enderror"
                required>
            <option value="">Pilih Role</option>

            @foreach($roles as $role)
                <option value="{{ $role }}"
                    @selected(old('role', $user->role ?? '') === $role)>
                    {{ $role }}
                </option>
            @endforeach
        </select>

        @error('role')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    {{-- ===============================
       PASSWORD
    ================================ --}}
    <div class="form-group">
        <label>
            Password
            @isset($user)
                <span class="text-muted text-sm">(opsional)</span>
            @endisset
        </label>

        <div class="input-group">
            <input type="password"
                   name="password"
                   id="passwordField"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Minimal 6 karakter"
                   autocomplete="new-password">

            <button type="button"
                    class="btn btn-icon btn-soft-primary"
                    onclick="togglePassword()"
                    aria-label="Toggle password">
                <i class="fas fa-eye" id="toggleIcon"></i>
            </button>
        </div>

        @error('password')
            <div class="form-error">{{ $message }}</div>
        @enderror

        @isset($user)
            <div class="form-text">
                Biarkan kosong jika tidak ingin mengganti password.
            </div>
        @endisset
    </div>

</div>

@push('scripts')
<script>
function togglePassword() {
    const field = document.getElementById('passwordField')
    const icon  = document.getElementById('toggleIcon')

    if (!field || !icon) return

    if (field.type === 'password') {
        field.type = 'text'
        icon.classList.replace('fa-eye', 'fa-eye-slash')
    } else {
        field.type = 'password'
        icon.classList.replace('fa-eye-slash', 'fa-eye')
    }
}
</script>
@endpush
