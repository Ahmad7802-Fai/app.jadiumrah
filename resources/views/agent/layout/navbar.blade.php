<header class="agent-header">

    <div class="agent-header-left">
        <a href="{{ route('agent.dashboard') }}" class="agent-header-brand">
            <img
                src="{{ asset('assets/images/hasantour.png') }}"
                alt="HASAN Tour & Travel"
                class="agent-header-logo"
            >

            <div class="agent-header-text">
                <div class="agent-brand-title">Umrah Baik</div>
                <div class="agent-brand-tagline">Satukan Keluarga</div>
            </div>
        </a>
    </div>

    <div class="agent-header-right">
        <button class="user-avatar">
            {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
        </button>
    </div>

</header>
