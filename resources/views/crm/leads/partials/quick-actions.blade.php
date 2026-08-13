@php
    $phoneRaw = preg_replace('/[^0-9]/', '', $lead->no_hp ?? '');
    $phoneWa  = preg_replace('/^0/', '62', $phoneRaw);

    $waText = urlencode(
        "Assalamu’alaikum, Bapak/Ibu {$lead->nama}. ".
        "Kami dari Jadiumrah menindaklanjuti permintaan informasi umroh."
    );
@endphp

<div class="card-premium mb-4">

    <div class="card-title-premium mb-3">
        Quick Action
    </div>

    <div class="quick-action-grid">

        {{-- WHATSAPP --}}
        <a
            href="https://wa.me/{{ $phoneWa }}?text={{ $waText }}"
            target="_blank"
            class="quick-action-item wa">
            <span class="icon">📱</span>
            <span class="label">WhatsApp</span>
        </a>

        {{-- CALL --}}
        <a
            href="tel:{{ $phoneRaw }}"
            class="quick-action-item call">
            <span class="icon">📞</span>
            <span class="label">Call</span>
        </a>

        {{-- REMINDER / FOLLOW UP --}}
        @can('createFollowUp', $lead)
            <button
                type="button"
                class="quick-action-item reminder"
                data-bs-toggle="modal"
                data-bs-target="#followupModal">
                <span class="icon">⏰</span>
                <span class="label">Reminder</span>
            </button>
        @endcan

    </div>

</div>
