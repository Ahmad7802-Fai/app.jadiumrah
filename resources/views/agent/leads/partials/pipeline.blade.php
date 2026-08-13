<h3 class="text-lg font-semibold mb-2">
    Pipeline
</h3>

<div class="flex flex-wrap gap-2">

    @foreach($pipelines as $p)
        @php
            $isCurrent = $lead->pipeline_id === $p->id;
            $isPassed  = $lead->pipeline?->urutan > $p->urutan;
        @endphp

        <span
            class="
                px-3 py-1 rounded-full text-xs font-semibold
                {{ $isCurrent ? 'bg-green-600 text-white' : '' }}
                {{ $isPassed && !$isCurrent ? 'bg-gray-300 text-gray-700' : '' }}
                {{ !$isCurrent && !$isPassed ? 'bg-gray-100 text-gray-400' : '' }}
            ">
            {{ ucfirst($p->tahap) }}
        </span>
    @endforeach

</div>

<div class="mt-2 text-sm text-gray-600">
    Tahap saat ini:
    <strong>{{ ucfirst($lead->pipeline?->tahap ?? '-') }}</strong>
</div>
