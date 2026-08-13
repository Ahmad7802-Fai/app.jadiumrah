@php
    $currentUrutan = $lead->pipeline?->urutan ?? 0;
@endphp

<div class="card-premium">

    <div class="card-title-premium mb-3">
        Pipeline
    </div>

    {{-- STATUS BADGE (STATUS SAJA) --}}
    <div class="mb-3">
        <span class="badge-success">
            {{ strtoupper($lead->status) }}
        </span>
    </div>

    {{-- PIPELINE STEPPER (DINAMIS) --}}
    <div class="pipeline-stepper">

        @foreach($pipelines as $pipe)

            @php
                $isDone   = $pipe->urutan < $currentUrutan;
                $isActive = $pipe->urutan === $currentUrutan;
            @endphp

            <div class="pipeline-step
                {{ $isDone ? 'done' : '' }}
                {{ $isActive ? 'active' : '' }}">

                <div class="step-dot"></div>

                <div class="step-label text-capitalize">
                    {{ $pipe->tahap }}
                </div>
            </div>

        @endforeach

    </div>

    {{-- FOOTER --}}
    <div class="mt-3 text-sm">
        Tahap saat ini:
        <strong class="text-capitalize">
            {{ $lead->pipeline?->tahap ?? '-' }}
        </strong>

        @if($lead->status === 'CLOSED')
            <div class="text-success mt-1">
                ✔ Lead sudah closing & dikunci
            </div>
        @endif
    </div>

</div>
