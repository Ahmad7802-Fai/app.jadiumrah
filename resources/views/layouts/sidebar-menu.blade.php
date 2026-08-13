<h1 class="text-2xl font-bold mb-10">JadiUmrah</h1>

<nav class="space-y-3">

    <a href="{{ route('admin.dashboard') }}"
       class="block px-4 py-2 rounded-lg hover:bg-white/10">
        Dashboard
    </a>

    <a href="/admin/berita" class="block px-4 py-2 rounded-lg hover:bg-white/10">
        Berita
    </a>

    <a href="/admin/gallery" class="block px-4 py-2 rounded-lg hover:bg-white/10">
        Galeri
    </a>

    <a href="/admin/partner" class="block px-4 py-2 rounded-lg hover:bg-white/10">
        Partner
    </a>

    <a href="/admin/paket-umrah" class="block px-4 py-2 rounded-lg hover:bg-white/10">
        Paket Umrah
    </a>

    <a href="/admin/team" class="block px-4 py-2 rounded-lg hover:bg-white/10">
        Team
    </a>

    <a href="/admin/testimoni" class="block px-4 py-2 rounded-lg hover:bg-white/10">
        Testimoni
    </a>

    <form method="POST" action="/logout">
        @csrf
        <button class="w-full text-left px-4 py-2 rounded-lg hover:bg-red-600/30 mt-10">
            Logout
        </button>
    </form>

</nav>
