{{-- ================= ADMIN ================= --}}
<li class="sidebar-section-title">ADMIN</li>

@include('layouts.sidebar.components.item', [
    'route' => 'admin.team.*',
    'url'   => route('admin.team.index'),
    'icon'  => 'fa-users-cog',
    'label' => 'Team'
])

@include('layouts.sidebar.components.item', [
    'route' => 'admin.partner.*',
    'url'   => route('admin.partner.index'),
    'icon'  => 'fa-handshake',
    'label' => 'Partner'
])

@include('layouts.sidebar.components.item', [
    'route' => 'admin.gallery.*',
    'url'   => route('admin.gallery.index'),
    'icon'  => 'fa-images',
    'label' => 'Galeri'
])

@include('layouts.sidebar.components.item', [
    'route' => 'admin.testimoni.*',
    'url'   => route('admin.testimoni.index'),
    'icon'  => 'fa-comment-dots',
    'label' => 'Testimoni'
])

@include('layouts.sidebar.components.item', [
    'route' => 'admin.berita.*',
    'url'   => route('admin.berita.index'),
    'icon'  => 'fa-newspaper',
    'label' => 'Berita'
])

@include('layouts.sidebar.components.item', [
    'route' => 'admin.paket.*',
    'url'   => route('admin.paket-umrah.index'),
    'icon'  => 'fa-kaaba',
    'label' => 'Paket Umrah'
])
