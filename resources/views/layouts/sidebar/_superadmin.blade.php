<li class="sidebar-section-title">SUPERADMIN</li>

@include('layouts.sidebar.components.item', [
    'route' => 'superadmin.users.*',
    'url'   => route('superadmin.users.index'),
    'icon'  => 'fa-user-shield',
    'label' => 'Manajemen User'
])

@include('layouts.sidebar.components.item', [
    'route' => 'superadmin.branch.*',
    'url'   => route('superadmin.branch.index'),
    'icon'  => 'fa-code-branch',
    'label' => 'Cabang'
])

@include('layouts.sidebar.components.item', [
    'route' => 'superadmin.agent.*',
    'url'   => route('superadmin.agent.index'),
    'icon'  => 'fa-user-tie',
    'label' => 'Agent'
])

@include('layouts.sidebar.components.item', [
    'route' => 'superadmin.roles.*',
    'url'   => route('superadmin.roles.index'),
    'icon'  => 'fa-key',
    'label' => 'Roles & Akses'
])

{{-- =========================
| COMPANY PROFILE
========================= --}}
@include('layouts.sidebar.components.item', [
    'route' => 'superadmin.company-profile.*',
    'url'   => route('superadmin.company-profile.index'),
    'icon'  => 'fa-building',
    'label' => 'Company Profile'
])
