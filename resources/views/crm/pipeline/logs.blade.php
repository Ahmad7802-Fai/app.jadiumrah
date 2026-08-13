@extends('layouts.admin')

@section('title', 'Pipeline Logs')

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-ju-green m-0">Riwayat Pipeline</h4>
    </div>

    {{-- CARD WRAPPER --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0">

                    <thead class="bg-ju-green text-white small text-uppercase">
                        <tr>
                            <th>#</th>
                            <th>Lead</th>
                            <th>Status Lama</th>
                            <th>Status Baru</th>
                            <th>Diubah Oleh</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($logs as $log)
                        <tr class="border-bottom small">

                            {{-- NOMOR --}}
                            <td>{{ $loop->iteration }}</td>

                            {{-- LEAD --}}
                            <td>
                                <div class="fw-semibold">{{ $log->lead->nama }}</div>
                                <div class="text-muted">{{ $log->lead->no_hp }}</div>
                            </td>

                            {{-- OLD STATUS --}}
                            <td>
                                <span class="badge bg-secondary px-2 py-1">
                                    {{ strtoupper($log->old_status) }}
                                </span>
                            </td>

                            {{-- NEW STATUS --}}
                            <td>
                                <span class="badge bg-ju-green px-2 py-1">
                                    {{ strtoupper($log->new_status) }}
                                </span>
                            </td>

                            {{-- USER --}}
                            <td>
                                {{ $log->user->name ?? 'System' }}
                            </td>

                            {{-- DATE --}}
                            <td>
                                {{ \Carbon\Carbon::parse($log->changed_at)->format('d M Y H:i') }}
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada riwayat pipeline.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>
@endsection
