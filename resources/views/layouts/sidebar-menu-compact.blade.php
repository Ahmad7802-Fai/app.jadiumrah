<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column"
        data-widget="treeview"
        role="menu"
        data-accordion="false">

        {{-- ================= DASHBOARD ================= --}}
        <li class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home nav-icon"></i>
                <p>Dashboard</p>
            </a>
        </li>


        {{-- ================= SUPERADMIN ================= --}}
        @if(Auth::user()->role === 'SUPERADMIN')
            <li class="nav-header">SUPERADMIN</li>

            <li class="nav-item">
                <a href="{{ route('superadmin.users.index') }}"
                   class="nav-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield nav-icon"></i>
                    <p>Manajemen User</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('superadmin.roles.index') }}"
                   class="nav-link {{ request()->routeIs('superadmin.roles.*') ? 'active' : '' }}">
                    <i class="fas fa-key nav-icon"></i>
                    <p>Roles & Akses</p>
                </a>
            </li>
        @endif


        {{-- ================= ADMIN ================= --}}
        @if(in_array(Auth::user()->role, ['SUPERADMIN','ADMIN']))
            <li class="nav-header">ADMIN</li>

            <li class="nav-item">
                <a href="{{ route('admin.team.index') }}"
                   class="nav-link {{ request()->routeIs('admin.team.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog nav-icon"></i>
                    <p>Team</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.partner.index') }}"
                   class="nav-link {{ request()->routeIs('admin.partner.*') ? 'active' : '' }}">
                    <i class="fas fa-handshake nav-icon"></i>
                    <p>Partner</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.gallery.index') }}"
                   class="nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                    <i class="fas fa-images nav-icon"></i>
                    <p>Galeri</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.testimoni.index') }}"
                   class="nav-link {{ request()->routeIs('admin.testimoni.*') ? 'active' : '' }}">
                    <i class="fas fa-comment-dots nav-icon"></i>
                    <p>Testimoni</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.berita.index') }}"
                   class="nav-link {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
                    <i class="fas fa-newspaper nav-icon"></i>
                    <p>Berita</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.paket.index') }}"
                   class="nav-link {{ request()->routeIs('admin.paket.*') ? 'active' : '' }}">
                    <i class="fas fa-kaaba nav-icon"></i>
                    <p>Paket Umrah</p>
                </a>
            </li>
        @endif


        {{-- ================= OPERATOR ================= --}}
        @if(in_array(Auth::user()->role, ['SUPERADMIN','OPERATOR']))
            <li class="nav-header">OPERATOR</li>

            <li class="nav-item">
                <a href="{{ route('operator.master-paket.index') }}"
                   class="nav-link {{ request()->routeIs('operator.master-paket.*') ? 'active' : '' }}">
                    <i class="fas fa-box nav-icon"></i>
                    <p>Master Paket</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('operator.keberangkatan.index') }}"
                   class="nav-link {{ request()->routeIs('operator.keberangkatan.*') ? 'active' : '' }}">
                    <i class="fas fa-plane nav-icon"></i>
                    <p>Keberangkatan</p>
                </a>
            </li>

            {{-- TREEVIEW --}}
            <li class="nav-item has-treeview {{ request()->is('operator/daftar-jamaah*') || request()->is('operator/passport*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->is('operator/daftar-jamaah*') || request()->is('operator/passport*') ? 'active' : '' }}">
                    <i class="fas fa-users nav-icon"></i>
                    <p>
                        Data Jamaah
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('operator.daftar-jamaah.index') }}"
                           class="nav-link {{ request()->routeIs('operator.daftar-jamaah.*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Daftar Jamaah</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('operator.passport.index') }}"
                           class="nav-link {{ request()->routeIs('operator.passport.*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Passport Jamaah</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('operator.manifest.index') }}"
                   class="nav-link {{ request()->routeIs('operator.manifest.*') ? 'active' : '' }}">
                    <i class="fas fa-list nav-icon"></i>
                    <p>Manifest</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('operator.visa.index') }}"
                   class="nav-link {{ request()->routeIs('operator.visa.*') ? 'active' : '' }}">
                    <i class="fas fa-passport nav-icon"></i>
                    <p>Visa</p>
                </a>
            </li>
        @endif


        {{-- ================= KEUANGAN ================= --}}
        @if(in_array(Auth::user()->role, ['SUPERADMIN','KEUANGAN']))
            <li class="nav-header">KEUANGAN</li>

            <li class="nav-item">
                <a href="{{ route('keuangan.payments.index') }}"
                   class="nav-link {{ request()->routeIs('keuangan.payments.*') ? 'active' : '' }}">
                    <i class="fas fa-cash-register nav-icon"></i>
                    <p>Pembayaran Jamaah</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('keuangan.payments.bin') }}"
                   class="nav-link {{ request()->routeIs('keuangan.payments.bin') ? 'active' : '' }}">
                    <i class="fas fa-trash nav-icon"></i>
                    <p>Recycle Bin Pembayaran</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('keuangan.invoice-jamaah.index') }}"
                   class="nav-link {{ request()->routeIs('keuangan.invoice-jamaah.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice nav-icon"></i>
                    <p>Tagihan Invoice</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('keuangan.transaksi.index') }}"
                   class="nav-link {{ request()->routeIs('keuangan.transaksi.*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave nav-icon"></i>
                    <p>Transaksi Layanan</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('keuangan.invoice.index') }}"
                   class="nav-link {{ request()->routeIs('keuangan.invoice.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar nav-icon"></i>
                    <p>Invoice Layanan</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('keuangan.clients.index') }}"
                   class="nav-link {{ request()->routeIs('keuangan.clients.*') ? 'active' : '' }}">
                    <i class="fas fa-users nav-icon"></i>
                    <p>Clients</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('keuangan.layanan.index') }}"
                   class="nav-link {{ request()->routeIs('keuangan.layanan.*') ? 'active' : '' }}">
                    <i class="fas fa-briefcase nav-icon"></i>
                    <p>Layanan</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('keuangan.operasional.index') }}"
                   class="nav-link {{ request()->routeIs('keuangan.operasional.*') ? 'active' : '' }}">
                    <i class="fas fa-calculator nav-icon"></i>
                    <p>Biaya Operasional</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('keuangan.keberangkatan.index') }}"
                   class="nav-link {{ request()->routeIs('keuangan.keberangkatan.*') ? 'active' : '' }}">
                    <i class="fas fa-plane-departure nav-icon"></i>
                    <p>Biaya Keberangkatan</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('keuangan.laporan.index') }}"
                   class="nav-link {{ request()->routeIs('keuangan.laporan.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line nav-icon"></i>
                    <p>Laporan Keuangan</p>
                </a>
            </li>
        @endif


        {{-- ================= INVENTORY ================= --}}
        @if(in_array(Auth::user()->role, ['SUPERADMIN','INVENTORY']))
            <li class="nav-header">INVENTORY</li>

            <li class="nav-item">
                <a href="{{ route('inventory.master-barang.index') }}"
                   class="nav-link {{ request()->routeIs('inventory.master-barang.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes nav-icon"></i>
                    <p>Master Barang</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('inventory.stok.index') }}"
                   class="nav-link {{ request()->routeIs('inventory.stok.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group nav-icon"></i>
                    <p>Stok Barang</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('inventory.mutasi.index') }}"
                   class="nav-link {{ request()->routeIs('inventory.mutasi.*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt nav-icon"></i>
                    <p>Log Mutasi</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('inventory.distribusi.index') }}"
                   class="nav-link {{ request()->routeIs('inventory.distribusi.*') ? 'active' : '' }}">
                    <i class="fas fa-truck nav-icon"></i>
                    <p>Distribusi</p>
                </a>
            </li>
        @endif


        {{-- ================= SALES ================= --}}
        @if(in_array(Auth::user()->role, ['SUPERADMIN','SALES']))
            <li class="nav-header">SALES</li>

            <li class="nav-item">
                <a href="{{ route('sales.leads.index') }}"
                   class="nav-link {{ request()->routeIs('sales.leads.*') ? 'active' : '' }}">
                    <i class="fas fa-user-plus nav-icon"></i>
                    <p>Leads</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('sales.followup.index') }}"
                   class="nav-link {{ request()->routeIs('sales.followup.*') ? 'active' : '' }}">
                    <i class="fas fa-phone nav-icon"></i>
                    <p>Follow Up</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('sales.pipeline.index') }}"
                   class="nav-link {{ request()->routeIs('sales.pipeline.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie nav-icon"></i>
                    <p>Pipeline</p>
                </a>
            </li>
        @endif

    </ul>
</nav>
