<style>
.sidebar-logo-auto {
    height: 32px;
    transition: .25s ease;
    object-fit: contain;
}

/* collapsed → kecil otomatis */
.sidebar.sidebar-collapsed .sidebar-logo-auto {
    height: 26px;
    margin-left: 4px;
}

</style>
<aside id="sidebar" class="sidebar sidebar-collapsed bg-white border-end d-flex flex-column">

    {{-- LOGO / BRAND --}}
        {{-- <div class="sidebar-header text-center py-3">
            <img src="{{ asset('logo.png') }}"
                style="height:40px; object-fit:contain;">
        </div> --}}
    <div class="sidebar-header d-flex align-items-center px-3 py-3">
        <img src="{{ asset('logo.png') }}" style="height:40px; object-fit:contain;" class="sidebar-logo-auto">
    </div>



    {{-- MENU LIST --}}
    <ul class="sidebar-menu list-unstyled px-2 flex-grow-1">

        {{-- ================= DASHBOARD ================= --}}
        <li>
            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home me-2"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </li>

    {{-- ================= SUPERADMIN ================= --}}
    @if(Auth::user()->role === 'SUPERADMIN')
        <li class="sidebar-section sidebar-text">SUPERADMIN</li>

        {{-- USER --}}
        <li>
            <a href="{{ route('superadmin.users.index') }}"
            class="sidebar-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield me-2"></i>
                <span class="sidebar-text">Manajemen User</span>
            </a>
        </li>

        {{-- CABANG --}}
        <li>
            <a href="{{ route('superadmin.branch.index') }}"
            class="sidebar-link {{ request()->routeIs('superadmin.branch.*') ? 'active' : '' }}">
                <i class="fas fa-code-branch me-2"></i>
                <span class="sidebar-text">Cabang</span>
            </a>
        </li>

        {{-- AGENT --}}
        <li>
            <a href="{{ route('superadmin.agent.index') }}"
            class="sidebar-link {{ request()->routeIs('superadmin.agent.*') ? 'active' : '' }}">
                <i class="fas fa-user-tie me-2"></i>
                <span class="sidebar-text">Agent</span>
            </a>
        </li>

        {{-- ROLES --}}
        <li>
            <a href="{{ route('superadmin.roles.index') }}"
            class="sidebar-link {{ request()->routeIs('superadmin.roles.*') ? 'active' : '' }}">
                <i class="fas fa-key me-2"></i>
                <span class="sidebar-text">Roles & Akses</span>
            </a>
        </li>
    @endif


        {{-- ================= ADMIN ================= --}}
        @if(in_array(Auth::user()->role, ['SUPERADMIN','ADMIN']))
            <li class="sidebar-section sidebar-text">ADMIN</li>

            <li>
                <a href="{{ route('admin.team.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.team.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog me-2"></i>
                    <span class="sidebar-text">Team</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.partner.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.partner.*') ? 'active' : '' }}">
                    <i class="fas fa-handshake me-2"></i>
                    <span class="sidebar-text">Partner</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.gallery.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                    <i class="fas fa-images me-2"></i>
                    <span class="sidebar-text">Galeri</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.testimoni.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.testimoni.*') ? 'active' : '' }}">
                    <i class="fas fa-comment-dots me-2"></i>
                    <span class="sidebar-text">Testimoni</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.berita.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
                    <i class="fas fa-newspaper me-2"></i>
                    <span class="sidebar-text">Berita</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.paket-umrah.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.paket.*') ? 'active' : '' }}">
                    <i class="fas fa-kaaba me-2"></i>
                    <span class="sidebar-text">Paket Umrah</span>
                </a>
            </li>
        @endif


    {{-- ================= OPERATOR ================= --}}
    @if(in_array(Auth::user()->role, ['SUPERADMIN','OPERATOR']))
        <li class="sidebar-section sidebar-text">OPERATOR</li>

        <li>
            <a href="{{ route('operator.master-paket.index') }}"
            class="sidebar-link {{ request()->routeIs('operator.master-paket.*') ? 'active' : '' }}">
                <i class="fas fa-box me-2"></i>
                <span class="sidebar-text">Master Paket</span>
            </a>
        </li>

        <li>
            <a href="{{ route('operator.keberangkatan.index') }}"
            class="sidebar-link {{ request()->routeIs('operator.keberangkatan.*') ? 'active' : '' }}">
                <i class="fas fa-plane me-2"></i>
                <span class="sidebar-text">Keberangkatan</span>
            </a>
        </li>

        {{-- ================= DATA JAMAAH ================= --}}
        <li class="submenu">
            <a class="sidebar-link submenu-toggle
                {{ request()->is('operator/daftar-jamaah*') || request()->is('operator/passport*') ? 'active' : '' }}"
            data-bs-toggle="collapse"
            href="#sub-jamaah">
                <i class="fas fa-users me-2"></i>
                <span class="sidebar-text">Data Jamaah</span>
                <i class="fas fa-chevron-right float-end"></i>
            </a>

            <ul id="sub-jamaah"
                class="collapse {{ request()->is('operator/daftar-jamaah*') || request()->is('operator/passport*') ? 'show' : '' }}">

                <li>
                    <a href="{{ route('operator.daftar-jamaah.index') }}"
                    class="submenu-link {{ request()->routeIs('operator.daftar-jamaah.*') ? 'active' : '' }}">
                        <i class="far fa-circle me-2"></i>
                        <span class="sidebar-text">Daftar Jamaah</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('operator.passport.index') }}"
                    class="submenu-link {{ request()->routeIs('operator.passport.*') ? 'active' : '' }}">
                        <i class="far fa-circle me-2"></i>
                        <span class="sidebar-text">Passport Jamaah</span>
                    </a>
                </li>
            </ul>
        </li>
        {{-- ================= APPROVAL JAMAAH ================= --}}
        <li>
            <a href="{{ route('operator.jamaah-approval.index') }}"
            class="sidebar-link {{ request()->routeIs('operator.jamaah-approval.*') ? 'active' : '' }}">
                <i class="fas fa-check-circle me-2"></i>
                <span class="sidebar-text">Approval Jamaah</span>

                {{-- Badge Pending (Opsional tapi recommended) --}}
                @php
                    $pendingApproval = \App\Models\Jamaah::where('status','pending')->count();
                @endphp

                @if($pendingApproval > 0)
                    <span class="badge bg-danger ms-auto">
                        {{ $pendingApproval }}
                    </span>
                @endif
            </a>
        </li>

        {{-- ================= AKUN JAMAAH ================= --}}
        <li>
            <a href="{{ route('operator.jamaah-user.index') }}"
            class="sidebar-link {{ request()->routeIs('operator.jamaah-user.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield me-2"></i>
                <span class="sidebar-text">Akun Jamaah</span>
            </a>
        </li>

        <li>
            <a href="{{ route('operator.manifest.index') }}"
            class="sidebar-link {{ request()->routeIs('operator.manifest.*') ? 'active' : '' }}">
                <i class="fas fa-list me-2"></i>
                <span class="sidebar-text">Manifest</span>
            </a>
        </li>

        <li>
            <a href="{{ route('operator.visa.index') }}"
            class="sidebar-link {{ request()->routeIs('operator.visa.*') ? 'active' : '' }}">
                <i class="fas fa-passport me-2"></i>
                <span class="sidebar-text">Visa</span>
            </a>
        </li>
    @endif

    {{-- ================= KEUANGAN ================= --}}
    @if(in_array(Auth::user()->role, ['SUPERADMIN','KEUANGAN']))

            <li class="sidebar-section sidebar-text">KEUANGAN</li>

            {{-- MENU UTAMA KEUANGAN --}}
            <li>
                <a href="{{ route('keuangan.payments.index') }}"
                class="sidebar-link {{ request()->routeIs('keuangan.payments.*') ? 'active' : '' }}">
                    <i class="fas fa-cash-register me-2"></i>
                    <span class="sidebar-text">Pembayaran Jamaah</span>
                </a>
            </li>

            <li>
                <a href="{{ route('keuangan.invoice-jamaah.index') }}"
                class="sidebar-link {{ request()->routeIs('keuangan.invoice-jamaah.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice me-2"></i>
                    <span class="sidebar-text">Tagihan Invoice</span>
                </a>
            </li>

            <li>
                <a href="{{ route('keuangan.transaksi-layanan.index') }}"
                class="sidebar-link {{ request()->routeIs('keuangan.transaksi-layanan.*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    <span class="sidebar-text">Transaksi Layanan</span>
                </a>
            </li>

            <li>
                <a href="{{ route('keuangan.invoice-layanan.index') }}"
                class="sidebar-link {{ request()->routeIs('keuangan.invoice-layanan.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    <span class="sidebar-text">Invoice Layanan</span>
                </a>
            </li>

            <li>
                <a href="{{ route('keuangan.vendor-payments.index') }}"
                class="sidebar-link {{ request()->routeIs('keuangan.vendor-payments.*') ? 'active' : '' }}">
                    <i class="fas fa-money-check-alt me-2"></i>
                    <span class="sidebar-text">Pembayaran Vendor</span>
                </a>
            </li>
            {{-- ================= KOMISI AGENT ================= --}} 
            <li>
                <a href="{{ route('keuangan.komisi.index') }}"
                class="sidebar-link {{ request()->routeIs('keuangan.komisi.*') ? 'active' : '' }}">
                    <i class="fas fa-percentage me-2"></i>
                    <span class="sidebar-text">Komisi Agent</span>
                </a>
            </li>

            {{-- ================= PAYOUT AGENT ================= --}}
            <li>
                <a href="{{ route('keuangan.payout.index') }}"
                class="sidebar-link {{ request()->routeIs('keuangan.payout.*') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-usd me-2"></i>
                    <span class="sidebar-text">Payout Agent</span>

                    {{-- OPTIONAL BADGE (REQUESTED) --}}
                    @php
                        $payoutRequestCount = \App\Models\AgentPayoutRequest::where('status','requested')->count();
                    @endphp

                    @if($payoutRequestCount > 0)
                        <span class="badge bg-danger ms-auto">
                            {{ $payoutRequestCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('keuangan.clients.index') }}"
                class="sidebar-link {{ request()->routeIs('keuangan.clients.*') ? 'active' : '' }}">
                    <i class="fas fa-users me-2"></i>
                    <span class="sidebar-text">Clients</span>
                </a>
            </li>

            <li>
                <a href="{{ route('keuangan.layanan.index') }}"
                class="sidebar-link {{ request()->routeIs('keuangan.layanan.*') ? 'active' : '' }}">
                    <i class="fas fa-briefcase me-2"></i>
                    <span class="sidebar-text">Layanan</span>
                </a>
            </li>

            {{-- ================= TICKETING ================= --}}
            <li class="submenu">
                <a class="sidebar-link submenu-toggle
                    {{ request()->routeIs('ticketing.*') ? 'active' : '' }}"
                data-bs-toggle="collapse"
                href="#sub-ticketing">

                    <i class="fas fa-plane me-2"></i>
                    <span class="sidebar-text">Ticketing</span>
                    <i class="fas fa-chevron-right float-end"></i>
                </a>

                <ul id="sub-ticketing"
                    class="collapse {{ request()->routeIs('ticketing.*') ? 'show' : '' }}">

                    {{-- PNR --}}
                    <li>
                        <a href="{{ route('ticketing.pnr.index') }}"
                        class="submenu-link {{ request()->routeIs('ticketing.pnr.*') ? 'active' : '' }}">
                            <i class="far fa-circle me-2"></i>
                            <span class="sidebar-text">PNR Ticket</span>
                        </a>
                    </li>

                    {{-- INVOICE --}}
                    <li>
                        <a href="{{ route('ticketing.invoice.index') }}"
                        class="submenu-link {{ request()->routeIs('ticketing.invoice.*') ? 'active' : '' }}">
                            <i class="far fa-circle me-2"></i>
                            <span class="sidebar-text">Invoice Ticket</span>
                        </a>
                    </li>

                    {{-- REFUND --}}
                    <li>
                        <a href="{{ route('ticketing.refund.approval') }}"
                        class="submenu-link {{ request()->routeIs('ticketing.refund.*') ? 'active' : '' }}">
                            <i class="far fa-circle me-2"></i>
                            <span class="sidebar-text">Refund Approval</span>
                        </a>
                    </li>

                    {{-- REPORT --}}
                    <li>
                        <a href="{{ route('ticketing.report.index') }}"
                        class="submenu-link {{ request()->routeIs('ticketing.report.*') ? 'active' : '' }}">
                            <i class="far fa-circle me-2"></i>
                            <span class="sidebar-text">Report</span>
                        </a>
                    </li>

                    {{-- AUDIT LOG --}}
                    <li>
                        <a href="{{ route('ticketing.audit.index') }}"
                        class="submenu-link {{ request()->routeIs('ticketing.audit.*') ? 'active' : '' }}">
                            <i class="far fa-circle me-2"></i>
                            <span class="sidebar-text">Audit Log</span>
                        </a>
                    </li>

                    {{-- DEPOSIT & ALLOCATION --}}
                    <li>
                        <a href="{{ route('ticketing.pnr.index') }}"
                        class="submenu-link">
                            <i class="far fa-circle me-2"></i>
                            <span class="sidebar-text">Deposit & Allocation</span>
                        </a>
                    </li>

                </ul>
            </li>


            <li>
                <a href="{{ route('keuangan.operasional.index') }}"
                class="sidebar-link {{ request()->routeIs('keuangan.operasional.*') ? 'active' : '' }}">
                    <i class="fas fa-calculator me-2"></i>
                    <span class="sidebar-text">Biaya Operasional</span>
                </a>
            </li>

            <li>
                <a href="{{ route('keuangan.biaya-keberangkatan.index') }}"
                class="sidebar-link {{ request()->routeIs('keuangan.biaya-keberangkatan.*') ? 'active' : '' }}">
                    <i class="fas fa-plane-departure me-2"></i>
                    <span class="sidebar-text">Biaya Keberangkatan</span>
                </a>
            </li>

            <li>
                <a href="{{ route('keuangan.laporan.index') }}"
                class="sidebar-link {{ request()->routeIs('keuangan.laporan.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line me-2"></i>
                    <span class="sidebar-text">Laporan Keuangan</span>
                </a>
            </li>

            {{-- ================= TABUNGAN UMRAH ================= --}}
            <li class="submenu">
                <a class="sidebar-link submenu-toggle
                    {{ request()->routeIs('keuangan.tabungan.*') ? 'active' : '' }}"
                data-bs-toggle="collapse"
                href="#sub-tabungan-umrah">

                    <i class="fas fa-wallet me-2"></i>
                    <span class="sidebar-text">Tabungan Umrah</span>
                    <i class="fas fa-chevron-right float-end"></i>
                </a>

                <ul id="sub-tabungan-umrah"
                    class="collapse {{ request()->routeIs('keuangan.tabungan.*') ? 'show' : '' }}">

                    {{-- TOP UP JAMAAH --}}
                    <li>
                        <a href="{{ route('keuangan.tabungan.topup.index') }}"
                        class="submenu-link {{ request()->routeIs('keuangan.tabungan.topup.*') ? 'active' : '' }}">
                            <i class="far fa-circle me-2"></i>
                            <span class="sidebar-text">Top Up Jamaah</span>
                        </a>
                    </li>

                    {{-- REKAP TABUNGAN --}}
                    <li>
                        <a href="{{ route('keuangan.tabungan.rekap.index') }}"
                        class="submenu-link {{ request()->routeIs('keuangan.tabungan.rekap.*') ? 'active' : '' }}">
                            <i class="far fa-chart-bar me-2"></i>
                            <span class="sidebar-text">Rekap Tabungan</span>
                        </a>
                    </li>

                </ul>
            </li>

        @endif


        {{-- ================= INVENTORY ================= --}}
        @if(in_array(Auth::user()->role, ['SUPERADMIN','INVENTORY']))
            <li class="sidebar-section sidebar-text">INVENTORY</li>

            {{-- MASTER BARANG --}}
            <li>
                <a href="{{ route('inventory.items.index') }}"
                class="sidebar-link {{ request()->routeIs('inventory.items.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes me-2"></i>
                    <span class="sidebar-text">Master Barang</span>
                </a>
            </li>

            {{-- STOK BARANG --}}
            <li>
                <a href="{{ route('inventory.stok.index') }}"
                class="sidebar-link {{ request()->routeIs('inventory.stok.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group me-2"></i>
                    <span class="sidebar-text">Stok Barang</span>
                </a>
            </li>

            {{-- LOG MUTASI --}}
            <li>
                <a href="{{ route('inventory.mutasi.index') }}"
                class="sidebar-link {{ request()->routeIs('inventory.mutasi.*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt me-2"></i>
                    <span class="sidebar-text">Log Mutasi</span>
                </a>
            </li>

            {{-- DISTRIBUSI --}}
            <li>
                <a href="{{ route('inventory.distribusi.index') }}"
                class="sidebar-link {{ request()->routeIs('inventory.distribusi.*') ? 'active' : '' }}">
                    <i class="fas fa-truck me-2"></i>
                    <span class="sidebar-text">Distribusi</span>
                </a>
            </li>
        @endif
        {{-- ================= SALES ================= --}}
        @if(in_array(Auth::user()->role, ['SUPERADMIN','SALES']))

            <li class="sidebar-section sidebar-text">SALES & CRM</li>

            {{-- DASHBOARD --}}
            <li>
                <a href="{{ route('crm.dashboard.sales') }}" 
                class="sidebar-link {{ request()->routeIs('crm.dashboard.sales') ? 'active' : '' }}">
                    <i class="fas fa-chart-line me-2"></i> Dashboard Sales
                </a>
            </li>

            {{-- LEADS --}}
            <li>
                <a href="{{ route('crm.leads.index') }}"
                class="sidebar-link {{ request()->routeIs('crm.leads.*') ? 'active' : '' }}">
                    <i class="fas fa-user-plus me-2"></i>
                    <span class="sidebar-text">Leads</span>
                </a>
            </li>

            {{-- FOLLOW UP --}}
            <li>
                <a href="{{ route('crm.followup.index') }}"
                class="sidebar-link {{ request()->routeIs('crm.followup.*') ? 'active' : '' }}">
                    <i class="fas fa-phone me-2"></i>
                    <span class="sidebar-text">Follow Up</span>
                </a>
            </li>

            {{-- PIPELINE --}}
            <li>
                <a href="{{ route('crm.pipeline.index') }}"
                class="sidebar-link {{ request()->routeIs('crm.pipeline.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie me-2"></i>
                    <span class="sidebar-text">Pipeline</span>
                </a>
            </li>

            {{-- CLOSING --}}
            <li>
                <a href="{{ route('crm.closing.index') }}"
                class="sidebar-link {{ request()->routeIs('crm.closing.*') ? 'active' : '' }}">
                    <i class="fas fa-check-circle me-2"></i>
                    <span class="sidebar-text">Closing</span>
                </a>
            </li>

        @endif

    </ul>


    {{-- ================= LOGOUT BUTTON (BOTTOM) ================= --}}
    <div class="sidebar-logout px-3 pb-3 mt-auto">
        <a href="#"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="sidebar-link logout-btn">
            <i class="fas fa-sign-out-alt me-2"></i>
            <span class="sidebar-text">Logout</span>
        </a>
    </div>

</aside>
