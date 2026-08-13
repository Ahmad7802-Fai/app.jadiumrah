{{-- =====================================================
   ROLE FORM (CREATE & EDIT)
===================================================== --}}
@php
    $editing  = isset($role) && $role->id;
    $selected = old(
        'permissions',
        $editing ? $role->permissions->pluck('id')->toArray() : []
    );
@endphp

<div class="form">

    {{-- ===============================
       ROLE INFO
    ================================ --}}
    <div class="form-grid">

        <div class="form-group">
            <label>Nama Role</label>
            <input type="text"
                   name="role_name"
                   value="{{ old('role_name', $role->role_name ?? '') }}"
                   class="form-control @error('role_name') is-invalid @enderror"
                   placeholder="Contoh: ADMIN, INVENTORY"
                   required>

            @error('role_name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Keterangan (Opsional)</label>
            <input type="text"
                   name="description"
                   value="{{ old('description', $role->description ?? '') }}"
                   class="form-control @error('description') is-invalid @enderror"
                   placeholder="Contoh: Mengelola user, akses penuh">

            @error('description')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

    </div>

    {{-- ===============================
       PERMISSION HEADER
    ================================ --}}
    <div class="card card-soft mt-2">

        <div class="card-header">
            <div>
                <div class="card-title">Hak Akses (Permissions)</div>
                <div class="card-subtitle">
                    Pilih permission yang dimiliki role ini
                </div>
            </div>

            <div class="d-flex gap-2">
                <input id="perm-search"
                       type="search"
                       class="form-control input-sm"
                       placeholder="Cari permission…">

                <label class="form-check">
                    <input id="perm-select-all" type="checkbox">
                    Pilih Semua
                </label>
            </div>
        </div>

        {{-- ===============================
           PERMISSION LIST
        ================================ --}}
        <div class="card-body">

            <div class="card-list" id="permissions-list">

                @foreach($permissions as $perm)
                    <label class="card card-hover permission-item"
                           data-name="{{ strtolower($perm->perm_name) }}">

                        <div class="form-check">
                            <input type="checkbox"
                                   class="perm-checkbox"
                                   name="permissions[]"
                                   value="{{ $perm->id }}"
                                   {{ in_array($perm->id, (array)$selected) ? 'checked' : '' }}>

                            <div>
                                <div class="fw-semibold text-sm">
                                    {{ $perm->perm_key }}
                                </div>
                                <div class="text-muted text-xs">
                                    {{ $perm->perm_name }}
                                </div>
                            </div>
                        </div>

                    </label>
                @endforeach

            </div>

        </div>

    </div>

    {{-- ===============================
       ACTIONS
    ================================ --}}
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            {{ $editing ? '💾 Simpan Perubahan' : '💾 Buat Role' }}
        </button>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // Select All
    const selectAll = document.getElementById('perm-select-all');
    const boxes     = document.querySelectorAll('.perm-checkbox');

    selectAll?.addEventListener('change', () => {
        boxes.forEach(cb => cb.checked = selectAll.checked);
    });

    // Search
    const search = document.getElementById('perm-search');
    const items  = document.querySelectorAll('.permission-item');

    search?.addEventListener('input', () => {
        const q = search.value.toLowerCase();
        items.forEach(it => {
            it.style.display = it.dataset.name.includes(q) ? '' : 'none';
        });
    });

});
</script>
@endpush
