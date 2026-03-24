@csrf

@php
    $user = auth()->user();
@endphp

<div class="card">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- ================= NAMA ================= --}}
        <div>
            <label class="label">Nama Lengkap *</label>
            <input type="text"
                   name="nama_lengkap"
                   value="{{ old('nama_lengkap', $jamaah->nama_lengkap ?? '') }}"
                   class="input"
                   required>
        </div>

        {{-- ================= GENDER ================= --}}
        <div>
            <label class="label">Gender</label>
            <select name="gender" class="input">
                <option value="">-- Pilih Gender --</option>
                <option value="L"
                    {{ old('gender', $jamaah->gender ?? '') == 'L' ? 'selected' : '' }}>
                    Laki-laki
                </option>
                <option value="P"
                    {{ old('gender', $jamaah->gender ?? '') == 'P' ? 'selected' : '' }}>
                    Perempuan
                </option>
            </select>
        </div>

        {{-- ================= TEMPAT LAHIR ================= --}}
        <div>
            <label class="label">Tempat Lahir</label>
            <input type="text"
                   name="tempat_lahir"
                   value="{{ old('tempat_lahir', $jamaah->tempat_lahir ?? '') }}"
                   class="input">
        </div>

        {{-- ================= TANGGAL LAHIR ================= --}}
        <div>
            <label class="label">Tanggal Lahir</label>
            <input type="date"
                   name="tanggal_lahir"
                   value="{{ old('tanggal_lahir', isset($jamaah->tanggal_lahir) ? $jamaah->tanggal_lahir->format('Y-m-d') : '') }}"
                   class="input">
        </div>

        {{-- ================= PHONE ================= --}}
        <div>
            <label class="label">No. HP</label>
            <input type="text"
                   name="phone"
                   value="{{ old('phone', $jamaah->phone ?? '') }}"
                   class="input">
        </div>

        {{-- ================= EMAIL ================= --}}
        <div>
            <label class="label">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $jamaah->email ?? '') }}"
                   class="input">
        </div>

        {{-- ================= CABANG ================= --}}
        <div>
            <label class="label">Cabang</label>

            @if($user->canChooseBranch())

                <select name="branch_id"
                        id="branchSelect"
                        class="input">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}"
                            {{ old('branch_id', $jamaah->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>

            @else

                <input type="hidden"
                       name="branch_id"
                       value="{{ $user->branch_id }}">

                <div class="input bg-gray-100 cursor-not-allowed">
                    {{ $user->branch->name ?? 'Cabang Anda' }}
                </div>

            @endif
        </div>

        {{-- ================= AGENT ================= --}}
        <div>
            <label class="label">Agent (Optional)</label>

            @if($user->canChooseAgent())

                <select name="agent_id"
                        id="agentSelect"
                        class="input">
                    <option value="">-- Tanpa Agent --</option>
                </select>

            @else

                <input type="hidden"
                       name="agent_id"
                       value="{{ $user->agent?->id }}">

                <div class="input bg-gray-100 cursor-not-allowed">
                    {{ $user->agent?->nama ?? 'Agent Anda' }}
                </div>

            @endif
        </div>

        {{-- ================= SOURCE ================= --}}
        <div>
            <label class="label">Source *</label>
            <select name="source"
                    id="sourceSelect"
                    class="input"
                    required>
                <option value="offline"
                    {{ old('source', $jamaah->source ?? 'offline') == 'offline' ? 'selected' : '' }}>
                    Offline
                </option>
                <option value="branch"
                    {{ old('source', $jamaah->source ?? '') == 'branch' ? 'selected' : '' }}>
                    Branch
                </option>
                <option value="agent"
                    {{ old('source', $jamaah->source ?? '') == 'agent' ? 'selected' : '' }}>
                    Agent
                </option>
                <option value="website"
                    {{ old('source', $jamaah->source ?? '') == 'website' ? 'selected' : '' }}>
                    Website
                </option>
            </select>
        </div>

        {{-- ================= STATUS ================= --}}
        <div class="flex items-center gap-3 mt-6">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   class="w-4 h-4"
                   {{ old('is_active', $jamaah->is_active ?? true) ? 'checked' : '' }}>
            <label class="text-sm text-gray-700">
                Aktif
            </label>
        </div>

        {{-- ================= ALAMAT ================= --}}
        <div class="md:col-span-2">
            <label class="label">Alamat</label>
            <textarea name="address"
                      rows="3"
                      class="input">{{ old('address', $jamaah->address ?? '') }}</textarea>
        </div>

    </div>

    {{-- ================= BUTTON ================= --}}
    <div class="mt-8 flex justify-end gap-3">

        <a href="{{ route('jamaah.index') }}"
           class="btn btn-secondary">
            Batal
        </a>

        <button type="submit"
                class="btn btn-primary">
            Simpan Jamaah
        </button>

    </div>

</div> 

<script>
document.addEventListener('DOMContentLoaded', function () {

    const branchSelect = document.getElementById('branchSelect');
    const agentSelect  = document.getElementById('agentSelect');
    const sourceSelect = document.getElementById('sourceSelect');

    if (!branchSelect || !agentSelect) return;

    const selectedAgent  = "{{ old('agent_id', $jamaah->agent_id ?? '') }}";
    const selectedBranch = branchSelect.value;

    function loadAgents(branchId, selected = null) {

        agentSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`/agents/by-branch/${branchId}`)
            .then(res => res.json())
            .then(data => {

                agentSelect.innerHTML = '<option value="">-- Tanpa Agent --</option>';

                data.forEach(agent => {

                    const option = document.createElement('option');
                    option.value = agent.id;
                    option.textContent = agent.nama;

                    if (selected && selected == agent.id) {
                        option.selected = true;
                    }

                    agentSelect.appendChild(option);
                });

            })
            .catch(() => {
                agentSelect.innerHTML = '<option value="">Gagal load agent</option>';
            });
    }

    branchSelect.addEventListener('change', function () {

        if (this.value) {
            loadAgents(this.value);
        } else {
            agentSelect.innerHTML = '<option value="">-- Tanpa Agent --</option>';
        }

    });

    agentSelect.addEventListener('change', function () {
        if (this.value) {
            sourceSelect.value = 'agent';
        }
    });

    if (selectedBranch) {
        loadAgents(selectedBranch, selectedAgent);
    }

});
</script>