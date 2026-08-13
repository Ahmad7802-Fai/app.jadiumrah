@section('breadcrumb')
<nav class="cabang-breadcrumb">
    <a href="{{ route('cabang.dashboard') }}" class="crumb">
        <i class="fa-solid fa-house"></i>
        <span>Dashboard</span>
    </a>

    <span class="crumb-sep">/</span>

    <span class="crumb current">
        <i class="fa-solid fa-bullseye"></i>
        Leads
    </span>
</nav>
@endsection
