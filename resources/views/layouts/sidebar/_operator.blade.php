{{-- ================= OPERATOR ================= --}}
<li class="sidebar-section-title">OPERATOR</li>

@include('layouts.sidebar.components.item', [
    'route' => 'operator.master-paket.*',
    'url'   => route('operator.master-paket.index'),
    'icon'  => 'fa-box',
    'label' => 'Master Paket'
])

@include('layouts.sidebar.components.item', [
    'route' => 'operator.keberangkatan.*',
    'url'   => route('operator.keberangkatan.index'),
    'icon'  => 'fa-plane',
    'label' => 'Keberangkatan'
])

{{-- ===== DATA JAMAAH (SUBMENU) ===== --}}
@include('layouts.sidebar.components.submenu', [
    'id'    => 'operator-jamaah',
    'icon'  => 'fa-users',
    'label' => 'Data Jamaah',
    'open'  => request()->routeIs([
        'operator.daftar-jamaah.*',
        'operator.passport.*',
    ]),
    'items' => [
        [
            'route' => 'operator.daftar-jamaah.*',
            'url'   => route('operator.daftar-jamaah.index'),
            'label' => 'Daftar Jamaah',
        ],
        [
            'route' => 'operator.passport.*',
            'url'   => route('operator.passport.index'),
            'label' => 'Passport Jamaah',
        ],
    ]
])

@include('layouts.sidebar.components.item', [
    'route' => 'operator.jamaah-approval.*',
    'url'   => route('operator.jamaah-approval.index'),
    'icon'  => 'fa-check-circle',
    'label' => 'Approval Jamaah',
    'badge' => \App\Models\Jamaah::where('status','pending')->count()
])

@include('layouts.sidebar.components.item', [
    'route' => 'operator.jamaah-user.*',
    'url'   => route('operator.jamaah-user.index'),
    'icon'  => 'fa-user-shield',
    'label' => 'Akun Jamaah'
])

@include('layouts.sidebar.components.item', [
    'route' => 'operator.manifest.*',
    'url'   => route('operator.manifest.index'),
    'icon'  => 'fa-list',
    'label' => 'Manifest'
])

@include('layouts.sidebar.components.item', [
    'route' => 'operator.visa.*',
    'url'   => route('operator.visa.index'),
    'icon'  => 'fa-passport',
    'label' => 'Visa'
])
