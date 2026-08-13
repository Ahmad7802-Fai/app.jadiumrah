<?php

return [

    // ===========================
    // DASHBOARD (ROLE SPESIFIK)
    // ===========================
    [
        'label' => 'Dashboard',
        'route' => [
            'SUPERADMIN' => 'superadmin.dashboard',
            'ADMIN'      => 'admin.dashboard',
            'OPERATOR'   => 'operator.dashboard',
            'KEUANGAN'   => 'keuangan.dashboard',
            'INVENTORY'  => 'inventory.dashboard',
            'SALES'      => 'sales.dashboard',
        ],
        'roles' => ['SUPERADMIN','ADMIN','OPERATOR','KEUANGAN','INVENTORY','SALES'],
    ],


    // ===========================
    // SUPERADMIN MENU
    // ===========================
    [
        'header' => 'SUPERADMIN',
        'roles'  => ['SUPERADMIN'],
    ],
    [
        'label' => 'User Management',
        'route' => 'superadmin.users.index',
        'roles' => ['SUPERADMIN'],
    ],
    [
        'label' => 'Role Settings',
        'route' => 'superadmin.roles.index',
        'roles' => ['SUPERADMIN'],
    ],


    // ===========================
    // ADMIN MENU
    // ===========================
    [
        'header' => 'ADMIN MENU',
        'roles'  => ['SUPERADMIN','ADMIN'],
    ],
    [
        'label' => 'Team',
        'route' => 'admin.team.index',
        'roles' => ['SUPERADMIN','ADMIN'],
    ],
    [
        'label' => 'Partner',
        'route' => 'admin.partner.index',
        'roles' => ['SUPERADMIN','ADMIN'],
    ],
    [
        'label' => 'Gallery',
        'route' => 'admin.gallery.index',
        'roles' => ['SUPERADMIN','ADMIN'],
    ],
    [
        'label' => 'Testimoni',
        'route' => 'admin.testimoni.index',
        'roles' => ['SUPERADMIN','ADMIN'],
    ],
    [
        'label' => 'Berita',
        'route' => 'admin.berita.index',
        'roles' => ['SUPERADMIN','ADMIN'],
    ],
    [
        'label' => 'Paket Umrah',
        'route' => 'admin.paket.index',
        'roles' => ['SUPERADMIN','ADMIN'],
    ],


    // ===========================
    // OPERATOR MENU
    // ===========================
    [
        'header' => 'OPERATOR',
        'roles'  => ['SUPERADMIN','OPERATOR'],
    ],
    [
        'label' => 'Master Paket',
        'route' => 'operator.master-paket.index',
        'roles' => ['SUPERADMIN','OPERATOR'],
    ],
    [
        'label' => 'Keberangkatan',
        'route' => 'operator.keberangkatan.index',
        'roles' => ['SUPERADMIN','OPERATOR'],
    ],
    [
        'label' => 'Daftar Jamaah',
        'route' => 'operator.daftar-jamaah.index',
        'roles' => ['SUPERADMIN','OPERATOR'],
    ],
    [
        'label' => 'Update Jamaah',
        'route' => 'operator.update-jamaah.index',
        'roles' => ['SUPERADMIN','OPERATOR'],
    ],
    [
        'label' => 'Manifest',
        'route' => 'operator.manifest.index',
        'roles' => ['SUPERADMIN','OPERATOR'],
    ],
    [
        'label' => 'Visa',
        'route' => 'operator.visa.index',
        'roles' => ['SUPERADMIN','OPERATOR'],
    ],


    // ===========================
    // KEUANGAN MENU
    // ===========================
    [
        'header' => 'KEUANGAN',
        'roles'  => ['SUPERADMIN','KEUANGAN'],
    ],
    [
        'label' => 'Pembayaran Jamaah',
        'route' => 'keuangan.pembayaran.index',
        'roles' => ['SUPERADMIN','KEUANGAN'],
    ],
    [
        'label' => 'Tagihan & Invoice',
        'route' => 'keuangan.tagihan.index',
        'roles' => ['SUPERADMIN','KEUANGAN'],
    ],
    [
        'label' => 'Transaksi Layanan',
        'route' => 'keuangan.transaksi.index',
        'roles' => ['SUPERADMIN','KEUANGAN'],
    ],
    [
        'label' => 'Invoice Layanan',
        'route' => 'keuangan.invoice.index',
        'roles' => ['SUPERADMIN','KEUANGAN'],
    ],
    [
        'label' => 'Clients',
        'route' => 'keuangan.clients.index',
        'roles' => ['SUPERADMIN','KEUANGAN'],
    ],
    [
        'label' => 'Layanan',
        'route' => 'keuangan.layanan.index',
        'roles' => ['SUPERADMIN','KEUANGAN'],
    ],
    [
        'label' => 'Biaya Operasional',
        'route' => 'keuangan.operasional.index',
        'roles' => ['SUPERADMIN','KEUANGAN'],
    ],
    [
        'label' => 'Biaya Keberangkatan',
        'route' => 'keuangan.keberangkatan.index',
        'roles' => ['SUPERADMIN','KEUANGAN'],
    ],
    [
        'label' => 'Laporan Keuangan',
        'route' => 'keuangan.laporan.index',
        'roles' => ['SUPERADMIN','KEUANGAN'],
    ],


    // ===========================
    // INVENTORY MENU
    // ===========================
    [
        'header' => 'INVENTORY',
        'roles'  => ['SUPERADMIN','INVENTORY'],
    ],
    [
        'label' => 'Master Barang',
        'route' => 'inventory.master-barang.index',
        'roles' => ['SUPERADMIN','INVENTORY'],
    ],
    [
        'label' => 'Stok Barang',
        'route' => 'inventory.stok.index',
        'roles' => ['SUPERADMIN','INVENTORY'],
    ],
    [
        'label' => 'Log Mutasi',
        'route' => 'inventory.mutasi.index',
        'roles' => ['SUPERADMIN','INVENTORY'],
    ],
    [
        'label' => 'Distribusi',
        'route' => 'inventory.distribusi.index',
        'roles' => ['SUPERADMIN','INVENTORY'],
    ],


    // ===========================
    // SALES / CRM MENU
    // ===========================
    [
        'header' => 'SALES / CRM',
        'roles'  => ['SUPERADMIN','SALES'],
    ],
    [
        'label' => 'Leads Masuk',
        'route' => 'sales.leads.index',
        'roles' => ['SUPERADMIN','SALES'],
    ],
    [
        'label' => 'Follow Up',
        'route' => 'sales.followup.index',
        'roles' => ['SUPERADMIN','SALES'],
    ],
    [
        'label' => 'Pipeline',
        'route' => 'sales.pipeline.index',
        'roles' => ['SUPERADMIN','SALES'],
    ],

];
