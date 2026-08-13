{{-- ================= INVENTORY ================= --}}
<li class="sidebar-section-title">INVENTORY</li>

@include('layouts.sidebar.components.item', [
    'route' => 'inventory.items.*',
    'url'   => route('inventory.items.index'),
    'icon'  => 'fa-boxes',
    'label' => 'Master Barang'
])

@include('layouts.sidebar.components.item', [
    'route' => 'inventory.stok.*',
    'url'   => route('inventory.stok.index'),
    'icon'  => 'fa-layer-group',
    'label' => 'Stok Barang'
])

@include('layouts.sidebar.components.item', [
    'route' => 'inventory.mutasi.*',
    'url'   => route('inventory.mutasi.index'),
    'icon'  => 'fa-exchange-alt',
    'label' => 'Log Mutasi'
])

@include('layouts.sidebar.components.item', [
    'route' => 'inventory.distribusi.*',
    'url'   => route('inventory.distribusi.index'),
    'icon'  => 'fa-truck',
    'label' => 'Distribusi'
])
