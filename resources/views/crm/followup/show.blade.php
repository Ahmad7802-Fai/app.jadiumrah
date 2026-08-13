@extends('layouts.admin')

@section('title', 'Follow Up Schedule')

@section('content')

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Jadwal Follow Up</h1>
        <div class="page-subtitle">
            Daftar aktivitas follow up yang telah dijadwalkan
        </div>
    </div>
</div>

{{-- ======================================================
| TABLE
====================================================== --}}
<div class="card card-hover">
    <div class="table-responsive">

        <table class="table table-compact">

            <thead>
                <tr>
                    <th>Lead</th>
                    <th>Aktivitas</th>
                    <th>Jadwal</th>
                    <th>PIC</th>
                    <th>Next Action</th>
                    <th class="table-right col-actions">Aksi</th>
                </tr>
            </thead>

            <tbody>
            @forelse ($list as $row)
                @php
                    $date = \Carbon\Carbon::parse($row->followup_date);

                    if ($date->isToday()) {
                        $label = 'Hari Ini';
                        $badge = 'badge-soft-warning';
                    } elseif ($date->isTomorrow()) {
                        $label = 'Besok';
                        $badge = 'badge-soft-info';
                    } elseif ($date->isPast()) {
                        $label = 'Terlambat';
                        $badge = 'badge-soft-danger';
                    } else {
                        $label = 'Mendatang';
                        $badge = 'badge-soft-secondary';
                    }
                @endphp

                <tr>

                    {{-- LEAD --}}
                    <td>
                        <div class="fw-semibold">
                            {{ $row->lead->nama }}
                        </div>
                        <div class="text-muted text-sm">
                            {{ $row->lead->no_hp }}
                        </div>
                    </td>

                    {{-- AKTIVITAS --}}
                    <td>
                        <span class="badge badge-soft-secondary">
                            {{ ucfirst($row->aktivitas) }}
                        </span>
                    </td>

                    {{-- JADWAL --}}
                    <td>
                        <span class="badge {{ $badge }}">
                            {{ $label }}
                        </span>
                        <div class="text-muted text-sm mt-1">
                            {{ $date->format('d M Y H:i') }}
                        </div>
                    </td>

                    {{-- PIC --}}
                    <td>
                        {{ $row->user->name ?? '—' }}
                    </td>

                    {{-- NEXT ACTION --}}
                    <td class="text-sm text-muted">
                        {{ $row->next_action ?: '—' }}
                    </td>

                    {{-- ACTION --}}
                    <td class="table-right col-actions">
                        <div class="table-actions">
                            <a href="{{ route('crm.leads.show', $row->lead_id) }}"
                               class="btn btn-outline-primary btn-xs"
                               title="Lihat Lead">
                                👁
                            </a>
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6" class="table-empty">
                        Belum ada jadwal follow up
                    </td>
                </tr>
            @endforelse
            </tbody>

        </table>

    </div>
</div>

{{-- ======================================================
| PAGINATION
====================================================== --}}
<div class="mt-4">
    {{ $list->links() }}
</div>

@endsection
