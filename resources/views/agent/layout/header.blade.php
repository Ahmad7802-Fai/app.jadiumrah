<header class="header">

    {{-- LEFT --}}
    <div class="header-left">
        <a href="{{ route('agent.dashboard') }}" class="header-brand">
            <img
                src="{{ asset('assets/images/hasantour.png') }}"
                alt="HASAN Tour & Travel"
                class="header-logo"
            >
            <div class="header-text">
                <span class="brand-title">Umrah Baik</span>
                <span class="brand-tagline">Satukan Keluarga</span>
            </div>
        </a>
    </div>

    {{-- RIGHT --}}
<div class="header-right">

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
        </button>
    </form>

    <div class="user-avatar">
        {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
    </div>

</div>


</header>
