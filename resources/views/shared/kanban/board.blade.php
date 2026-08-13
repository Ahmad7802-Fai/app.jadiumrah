{{-- =====================================================
 | KANBAN BOARD — FINAL PRODUCTION
 ===================================================== --}}

<div class="row kanban-board">

@foreach ($pipelines as $pipeline)
    <div class="col-md-3 mb-3">
        <div class="card kanban-column-card h-100">

            {{-- HEADER --}}
            <div class="card-header kanban-header text-center">
                {{ strtoupper($pipeline->tahap) }}
            </div>

            {{-- BODY --}}
            <div class="card-body kanban-column"
                 data-pipeline-id="{{ $pipeline->id }}">

                <div class="kanban-cards">

                    @forelse ($pipeline->leads as $lead)

                        @php
                            $locked =
                                in_array(optional($lead->pipeline)->tahap, ['closing','lost'])
                                || in_array($lead->status, ['CLOSED','DROPPED']);
                        @endphp

                        <div class="kanban-card"
                             data-lead-id="{{ $lead->id }}"
                             data-name="{{ strtolower($lead->nama) }}"
                             data-phone="{{ $lead->no_hp }}"
                             data-agent="{{ $lead->agent_id }}"
                             data-locked="{{ $locked ? 1 : 0 }}"
                             @if(!$locked) draggable="true" @endif>

                            <div class="fw-semibold">
                                {{ $lead->nama }}
                            </div>

                            <small class="text-muted">
                                {{ $lead->no_hp }}
                            </small>

                            @if($locked)
                                <span class="badge bg-secondary mt-1">
                                    LOCKED
                                </span>
                            @endif
                        </div>

                    @empty
                        <div class="kanban-empty">
                            Tidak ada lead
                        </div>
                    @endforelse

                </div>

                {{-- +X LEAD LAINNYA --}}
                @if ($pipeline->leads_count > $pipeline->leads->count())
                    <div class="kanban-more">
                        +{{ $pipeline->leads_count - $pipeline->leads->count() }}
                        lead lainnya
                    </div>
                @endif

            </div>
        </div>
    </div>
@endforeach

</div>

{{-- TOAST --}}
<div id="toast" class="toast"></div>
