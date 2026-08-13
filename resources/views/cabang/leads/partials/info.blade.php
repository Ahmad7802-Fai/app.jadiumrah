<div class="card-premium">

    <div class="card-title-premium mb-3">
        Informasi Lead
    </div>

    <div class="row g-3 text-sm">

        {{-- NAMA --}}
        <div class="col-md-6">
            <div class="text-muted mb-1">Nama</div>
            <div class="fw-semibold">
                {{ $lead->nama }}
            </div>
        </div>

        {{-- NO HP --}}
        <div class="col-md-6">
            <div class="text-muted mb-1">No. HP</div>
            <div>
                {{ $lead->no_hp }}
            </div>
        </div>

        {{-- SUMBER --}}
        <div class="col-md-6">
            <div class="text-muted mb-1">Sumber</div>
            <div>
                {{ optional($lead->sumber)->nama_sumber ?? '-' }}
            </div>
        </div>

        {{-- CHANNEL --}}
        <div class="col-md-6">
            <div class="text-muted mb-1">Channel</div>
            <div class="text-capitalize">
                {{ $lead->channel }}
            </div>
        </div>

        {{-- AGENT --}}
        <div class="col-md-6">
            <div class="text-muted mb-1">Agent</div>
            <div>
                {{ optional($lead->agent)->nama ?? '-' }}
            </div>
        </div>

        {{-- STATUS --}}
        <div class="col-md-6">
            <div class="text-muted mb-1">Status</div>

            @php
                $statusBadge = match(strtoupper($lead->status)) {
                    'NEW'       => 'badge-info-soft',
                    'ACTIVE'    => 'badge-info',
                    'PROSPECT'  => 'badge-warning-soft',
                    'KOMIT'     => 'badge-warning',
                    'CLOSED'    => 'badge-success',
                    'LOST'      => 'badge-danger-soft',
                    default     => 'badge-gray-soft',
                };
            @endphp

            <span class="{{ $statusBadge }}">
                {{ strtoupper($lead->status) }}
            </span>
        </div>

    </div>

</div>
