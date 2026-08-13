<div class="c-navbar">

    {{-- ===============================
       LEFT
    =============================== --}}
    <div class="c-navbar__left">
        <img
            src="{{ asset('hasantour.png') }}"
            class="c-navbar__logo"
            alt="Logo Cabang">

        <span class="c-navbar__title">
                <div class="agent-brand-title">Umrah Baik</div>
                <div class="agent-brand-tagline">Satukan Keluarga</div>
        </span>
    </div>

    {{-- ===============================
       RIGHT
    =============================== --}}
    <div class="c-navbar__right">

        {{-- NOTIFICATION --}}
        <button class="c-icon-btn" type="button">
            <i class="fas fa-bell"></i>
        </button>

        {{-- USER --}}
        <div class="c-user">
            <i class="fas fa-user-circle"></i>
            <span>{{ auth()->user()->nama }}</span>
        </div>

        {{-- LOGOUT --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="c-icon-btn" type="submit">
                <i class="fas fa-arrow-right-from-bracket"></i>
            </button>
        </form>

    </div>

</div>
