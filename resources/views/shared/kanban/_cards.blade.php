@foreach ($leads as $lead)

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

@endforeach
@if ($pipeline->leads_count > $pipeline->leads->count())
    <button class="btn btn-light btn-sm w-100 mt-2 load-more-btn"
        data-pipeline-id="{{ $pipeline->id }}"
        data-offset="{{ $pipeline->leads->count() }}">
        Load more
    </button>
@endif
