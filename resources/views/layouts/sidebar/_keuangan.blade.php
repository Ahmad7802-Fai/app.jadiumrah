{{-- ================= KEUANGAN ================= --}}
<li class="sidebar-section-title">KEUANGAN</li>

@include('layouts.sidebar.components.item', [
    'route' => 'keuangan.payments.*',
    'url'   => route('keuangan.payments.index'),
    'icon'  => 'fa-cash-register',
    'label' => 'Pembayaran Jamaah'
])

@include('layouts.sidebar.components.item', [
    'route' => 'keuangan.invoice-jamaah.*',
    'url'   => route('keuangan.invoice-jamaah.index'),
    'icon'  => 'fa-file-invoice',
    'label' => 'Tagihan Invoice'
])

@include('layouts.sidebar.components.item', [
    'route' => 'keuangan.transaksi-layanan.*',
    'url'   => route('keuangan.transaksi-layanan.index'),
    'icon'  => 'fa-money-bill-wave',
    'label' => 'Transaksi Layanan'
])

@include('layouts.sidebar.components.item', [
    'route' => 'keuangan.invoice-layanan.*',
    'url'   => route('keuangan.invoice-layanan.index'),
    'icon'  => 'fa-file-invoice-dollar',
    'label' => 'Invoice Layanan'
])

@include('layouts.sidebar.components.item', [
    'route' => 'keuangan.vendor-payments.*',
    'url'   => route('keuangan.vendor-payments.index'),
    'icon'  => 'fa-money-check-alt',
    'label' => 'Pembayaran Vendor'
])

{{-- ===== KOMISI & PAYOUT (SUBMENU) ===== --}}
@include('layouts.sidebar.components.submenu', [
    'id'    => 'keuangan-komisi',
    'icon'  => 'fa-percentage',
    'label' => 'Komisi & Payout',
    'open'  => request()->routeIs([
        'keuangan.komisi.*',
        'keuangan.payout.*',
    ]),
    'items' => [
        [
            'route' => 'keuangan.komisi.*',
            'url'   => route('keuangan.komisi.index'),
            'label' => 'Komisi Agent',
        ],
        [
            'route' => 'keuangan.payout.*',
            'url'   => route('keuangan.payout.index'),
            'label' => 'Payout Agent',
            'badge' => \App\Models\AgentPayoutRequest::where('status','requested')->count(),
        ],
    ]
])

@include('layouts.sidebar.components.item', [
    'route' => 'keuangan.clients.*',
    'url'   => route('keuangan.clients.index'),
    'icon'  => 'fa-users',
    'label' => 'Clients'
])

@include('layouts.sidebar.components.item', [
    'route' => 'keuangan.layanan.*',
    'url'   => route('keuangan.layanan.index'),
    'icon'  => 'fa-briefcase',
    'label' => 'Layanan'
])

{{-- ===== TICKETING ===== --}}
@include('layouts.sidebar.components.submenu', [
    'id'    => 'keuangan-ticketing',
    'icon'  => 'fa-plane',
    'label' => 'Ticketing',
    'open'  => request()->routeIs('ticketing.*'),
    'items' => [
        [
            'route' => 'ticketing.pnr.*',
            'url'   => route('ticketing.pnr.index'),
            'label' => 'PNR Ticket',
        ],
        [
            'route' => 'ticketing.invoice.*',
            'url'   => route('ticketing.invoice.index'),
            'label' => 'Invoice Ticket',
        ],
        [
            'route' => 'ticketing.refund.*',
            'url'   => route('ticketing.refund.approval'),
            'label' => 'Refund Approval',
        ],
        [
            'route' => 'ticketing.report.*',
            'url'   => route('ticketing.report.index'),
            'label' => 'Report',
        ],
        [
            'route' => 'ticketing.audit.*',
            'url'   => route('ticketing.audit.index'),
            'label' => 'Audit Log',
        ],
    ]
])

@include('layouts.sidebar.components.item', [
    'route' => 'keuangan.operasional.*',
    'url'   => route('keuangan.operasional.index'),
    'icon'  => 'fa-calculator',
    'label' => 'Biaya Operasional'
])

{{-- ===== MARKETING ===== --}}
@include('layouts.sidebar.components.item', [
    'route' => 'marketing.expenses.*',
    'url'   => route('marketing.expenses.index'),
    'icon'  => 'fa-bullhorn',
    'label' => 'Biaya Marketing'
])

@include('layouts.sidebar.components.item', [
    'route' => 'keuangan.biaya-keberangkatan.*',
    'url'   => route('keuangan.biaya-keberangkatan.index'),
    'icon'  => 'fa-plane-departure',
    'label' => 'Biaya Keberangkatan'
])


{{-- ===== TABUNGAN UMRAH ===== --}}
@include('layouts.sidebar.components.submenu', [
    'id'    => 'keuangan-tabungan',
    'icon'  => 'fa-wallet',
    'label' => 'Tabungan Umrah',
    'open'  => request()->routeIs('keuangan.tabungan.*'),
    'items' => [
        [
            'route' => 'keuangan.tabungan.topup.*',
            'url'   => route('keuangan.tabungan.topup.index'),
            'label' => 'Top Up Jamaah',
        ],
        [
            'route' => 'keuangan.tabungan.rekap.*',
            'url'   => route('keuangan.tabungan.rekap.index'),
            'label' => 'Rekap Tabungan',
        ],
    ]
])

@include('layouts.sidebar.components.item', [
    'route' => 'keuangan.laporan.*',
    'url'   => route('keuangan.laporan.index'),
    'icon'  => 'fa-chart-line',
    'label' => 'Laporan Keuangan'
])
