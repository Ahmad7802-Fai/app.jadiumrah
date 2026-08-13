<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">

        <h6 class="fw-bold mb-3">Riwayat Follow Up</h6>

        @forelse ($lead->activities as $act)
            <div class="border-bottom py-2">

                <div class="d-flex justify-content-between">
                    <div>
                        <strong class="text-capitalize">{{ $act->aktivitas }}</strong>
                        <span class="text-muted small">
                            oleh {{ $act->user->name ?? 'User' }}
                        </span>
                    </div>

                    <div class="small text-muted">
                        {{ \Carbon\Carbon::parse($act->created_at)->format('d M Y H:i') }}
                    </div>
                </div>

                <div class="mt-1">
                    <span class="fw-semibold">Hasil:</span> {{ $act->hasil }}
                </div>

                @if ($act->next_action)
                    <div class="text-muted small">
                        <i class="fas fa-arrow-right me-1"></i>
                        Next: {{ $act->next_action }}
                    </div>
                @endif

                @if ($act->followup_date)
                    <div class="text-primary small">
                        <i class="fas fa-clock me-1"></i>
                        Jadwal berikutnya:
                        {{ \Carbon\Carbon::parse($act->followup_date)->format('d M Y H:i') }}
                    </div>
                @endif

            </div>
        @empty
            <p class="text-muted">Belum ada aktivitas follow up.</p>
        @endforelse

    </div>
</div>
