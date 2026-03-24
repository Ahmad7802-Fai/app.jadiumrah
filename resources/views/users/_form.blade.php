@csrf

@php
    $isEdit = isset($user);

    $selectedRole = old('roles.0');
    if (!$selectedRole && $isEdit) {
        $selectedRole = optional($user->roles->first())->name;
    }

    $selectedBranch = old('branch_id');
    if (!$selectedBranch && $isEdit) {
        $selectedBranch = $user->branch_id;
    }

    $directPermissions = old('permissions');
    if (!$directPermissions && $isEdit) {
        $directPermissions = $user->permissions->pluck('name')->toArray();
    }

    $directPermissions = $directPermissions ?? [];
@endphp


<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- ================= NAME ================= --}}
    <div>
        <label class="block text-sm font-medium mb-2">
            Nama Lengkap
        </label>
        <input type="text"
               name="name"
               value="{{ old('name', $isEdit ? $user->name : '') }}"
               class="w-full border rounded-lg px-4 py-2"
               required>
    </div>

    {{-- ================= EMAIL ================= --}}
    <div>
        <label class="block text-sm font-medium mb-2">
            Email
        </label>
        <input type="email"
               name="email"
               value="{{ old('email', $isEdit ? $user->email : '') }}"
               class="w-full border rounded-lg px-4 py-2"
               required>
    </div>

    {{-- ================= PASSWORD ================= --}}
    <div>
        <label class="block text-sm font-medium mb-2">
            Password
            @if($isEdit)
                <span class="text-xs text-gray-400">(Kosongkan jika tidak diubah)</span>
            @endif
        </label>
        <input type="password"
               name="password"
               class="w-full border rounded-lg px-4 py-2"
               {{ $isEdit ? '' : 'required' }}>
    </div>

    {{-- ================= BRANCH ================= --}}
    <div>
        <label class="block text-sm font-medium mb-2">
            Cabang
        </label>
        <select name="branch_id"
                class="w-full border rounded-lg px-4 py-2">
            <option value="">-- Tanpa Cabang --</option>

            @foreach($branches as $branch)
                <option value="{{ $branch->id }}"
                    {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                </option>
            @endforeach

        </select>
    </div>

</div>



{{-- ================= ROLE ================= --}}
<div class="mt-10">

    <label class="block text-sm font-semibold mb-4">
        Role
    </label>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">

        @foreach($roles as $role)

            <label class="flex items-center gap-2 border rounded-lg p-3 hover:bg-gray-50">

                <input type="radio"
                       name="roles[]"
                       value="{{ $role->name }}"
                       {{ $selectedRole === $role->name ? 'checked' : '' }}
                       required>

                <span>{{ $role->name }}</span>

            </label>

        @endforeach

    </div>

</div>



{{-- ================= DIRECT PERMISSION ================= --}}
{{-- <div class="mt-10">

    <label class="block text-sm font-semibold mb-4">
        Direct Permission (Override Role)
    </label>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm max-h-64 overflow-y-auto border rounded-lg p-4">

        @foreach($permissions as $permission)

            <label class="flex items-center gap-2">

                <input type="checkbox"
                       name="permissions[]"
                       value="{{ $permission->name }}"
                       {{ in_array($permission->name, $directPermissions) ? 'checked' : '' }}>

                {{ $permission->name }}

            </label>

        @endforeach

    </div>

    <p class="text-xs text-gray-400 mt-2">
        Permission ini akan diberikan langsung ke user (di luar role).
    </p>

</div> --}}



{{-- ================= SUBMIT ================= --}}
<div class="mt-8 text-right">
    <button class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
        {{ $isEdit ? 'Update User' : 'Simpan User' }}
    </button>
</div>