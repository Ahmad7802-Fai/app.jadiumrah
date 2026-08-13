@extends('layouts.admin')

@section('title', 'Data Agent')

@section('content')
<div class="page-container">

    {{-- ================= PAGE HEADER ================= --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Data Agent</h1>

            <p class="page-subtitle">
                {{ $activeBranch
                    ? 'Cabang: ' . $activeBranch->nama_cabang
                    : 'Seluruh agent di semua cabang'
                }}
            </p>
        </div>

        @can('create', App\Models\Agent::class)
            <a href="{{ route('superadmin.agent.create', request()->only('branch_id')) }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Agent
            </a>
        @endcan
    </div>

    {{-- ================= FILTER ================= --}}
    <form method="GET" class="card card-soft mb-3">
        <div class="card-body">
            <div class="form-grid-3">

                <div class="form-group">
                    <label>Nama / Email</label>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control"
                           placeholder="Cari agent…">
                </div>

                <div class="form-group">
                    <label>Cabang</label>
                    <select name="branch_id" class="form-select">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}"
                                @selected(request('branch_id') == $branch->id)>
                                {{ $branch->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="1" @selected(request('status') === '1')>Aktif</option>
                        <option value="0" @selected(request('status') === '0')>Nonaktif</option>
                    </select>
                </div>

            </div>

            <div class="form-actions mt-3">
                <button class="btn btn-primary btn-sm">
                    <i class="fas fa-filter"></i> Terapkan Filter
                </button>

                @if(request()->hasAny(['q','branch_id','status']))
                    <a href="{{ route('superadmin.agent.index') }}"
                       class="btn btn-secondary btn-sm">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- ================= DESKTOP TABLE ================= --}}
    <div class="card card-hover d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-compact">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Agent</th>
                            <th>Email</th>
                            <th>Cabang</th>
                            <th class="table-center">Jamaah</th>
                            <th class="table-center">Status</th>
                            <th class="table-center">Aktif</th>
                            <th class="table-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($agents as $agent)
                        <tr>
                            <td>{{ $loop->iteration + ($agents->currentPage()-1)*$agents->perPage() }}</td>

                            {{-- AGENT (CLICKABLE) --}}
                            <td class="fw-semibold">
                                <a href="{{ route('superadmin.agent.show', $agent->id) }}"
                                   class="text-primary text-decoration-none">
                                    {{ $agent->user->nama }}
                                </a>
                            </td>

                            <td>{{ $agent->user->email }}</td>

                            <td>{{ $agent->user->branch->nama_cabang ?? '-' }}</td>

                            <td class="table-center">
                                <span class="badge badge-soft-primary">
                                    {{ $agent->jamaah_count }}
                                </span>
                            </td>

                            <td class="table-center">
                                <span class="badge {{ $agent->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                                    {{ $agent->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                </span>
                            </td>

                            <td class="table-center">
                                @can('toggle', $agent)
                                    <input type="checkbox"
                                           class="js-toggle-agent"
                                           data-id="{{ $agent->id }}"
                                           {{ $agent->is_active ? 'checked' : '' }}>
                                @endcan
                            </td>

                            <td class="table-right">
                                <a href="{{ route('superadmin.agent.show', $agent->id) }}"
                                   class="btn btn-outline-secondary btn-xs">
                                    Detail
                                </a>

                                @can('update', $agent)
                                    <a href="{{ route('superadmin.agent.edit', $agent->id) }}"
                                       class="btn btn-outline-primary btn-xs">
                                        Edit
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="table-empty">
                                👤 Tidak ada data agent
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= MOBILE CARD ================= --}}
    <div class="d-md-none card-list">
        @forelse($agents as $agent)
            <div class="card card-hover">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('superadmin.agent.show', $agent->id) }}"
                               class="fw-semibold text-primary">
                                {{ $agent->user->nama }}
                            </a>
                            <div class="text-muted text-sm">
                                {{ $agent->user->email }}
                            </div>
                        </div>

                        <span class="badge {{ $agent->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                            {{ $agent->is_active ? 'AKTIF' : 'NONAKTIF' }}
                        </span>
                    </div>

                    <div class="mt-2 text-sm">
                        <div>Cabang: {{ $agent->user->branch->nama_cabang ?? '-' }}</div>
                        <div>Jamaah: <strong>{{ $agent->jamaah_count }}</strong></div>
                    </div>

                    <div class="d-flex justify-content-end mt-3 gap-2">
                        <a href="{{ route('superadmin.agent.show', $agent->id) }}"
                           class="btn btn-outline-secondary btn-sm">
                            Detail
                        </a>

                        @can('update', $agent)
                            <a href="{{ route('superadmin.agent.edit', $agent->id) }}"
                               class="btn btn-outline-primary btn-sm">
                                Edit
                            </a>
                        @endcan
                    </div>

                </div>
            </div>
        @empty
            <div class="empty-state empty-soft">
                <div class="empty-title">Belum ada agent</div>
            </div>
        @endforelse
    </div>

    {{-- ================= PAGINATION ================= --}}
    @if ($agents->hasPages())
        <div class="pagination mt-4">
            {{ $agents->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.js-toggle-agent').forEach(el => {
    el.addEventListener('change', function () {
        fetch(`/superadmin/agent/${this.dataset.id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).catch(() => {
            alert('Gagal mengubah status agent');
            this.checked = !this.checked;
        });
    });
});
</script>
@endpush
