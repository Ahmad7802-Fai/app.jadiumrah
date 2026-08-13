@if($activities->count())

<div class="followup-timeline">

    @foreach($activities as $activity)
        <div class="followup-item">

            {{-- DOT --}}
            <div class="followup-dot"></div>

            {{-- CONTENT --}}
            <div class="followup-content">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div class="fw-semibold text-capitalize">
                        {{ $activity->jenis ?? 'Follow Up' }}
                    </div>

                    <span class="text-muted text-xs">
                        {{ optional($activity->created_at)->format('d M Y H:i') }}
                    </span>
                </div>

                {{-- HASIL / CATATAN (FALLBACK AMAN) --}}
                @php
                    $text =
                        $activity->hasil
                        ?? $activity->catatan
                        ?? $activity->keterangan
                        ?? null;
                @endphp

                @if($text)
                    <div class="text-sm mb-1">
                        {{ $text }}
                    </div>
                @endif

                {{-- NEXT ACTION --}}
                @if(!empty($activity->next_action))
                    <div class="text-xs text-muted">
                        Next: {{ $activity->next_action }}
                    </div>
                @endif

            </div>
        </div>
    @endforeach

</div>

@else
    <div class="empty-state">
        <h4>Belum ada follow up</h4>
        <p>Tambahkan follow up untuk mulai tracking aktivitas lead.</p>
    </div>
@endif
