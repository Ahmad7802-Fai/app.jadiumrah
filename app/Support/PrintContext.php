<?php

namespace App\Support;

class PrintContext
{
    /* =====================================================
     | FLAGS (APA YANG BOLEH DILIHAT)
     ===================================================== */
    public bool $canViewHarga       = false;
    public bool $canViewTabungan    = false;
    public bool $canViewPembayaran  = false;
    public bool $canViewAgent       = false;

    /* =====================================================
     | METADATA
     ===================================================== */
    public string $roleLabel = '';
    public string $watermark = '';

    /* =====================================================
     | FACTORY
     ===================================================== */
    public static function fromRole(string $role): self
    {
        $role = strtoupper($role);
        $ctx  = new self();

        match ($role) {

            /* ===============================
             | PUSAT
             | SUPERADMIN / OPERATOR
             =============================== */
            'SUPERADMIN', 'OPERATOR'
                => self::pusat($ctx),

            /* ===============================
             | CABANG
             | ADMIN
             =============================== */
            'ADMIN'
                => self::cabang($ctx),

            /* ===============================
             | AGENT
             | SALES
             =============================== */
            'SALES'
                => self::agent($ctx),

            /* ===============================
             | DEFAULT (AMAN)
             =============================== */
            default
                => self::restricted($ctx),
        };

        return $ctx;
    }

    /* =====================================================
     | ROLE PRESET
     ===================================================== */

    // 🔵 PUSAT
    protected static function pusat(self $ctx): void
    {
        $ctx->canViewHarga      = true;
        $ctx->canViewTabungan   = true;
        $ctx->canViewPembayaran = true;
        $ctx->canViewAgent      = true;

        $ctx->roleLabel = 'PUSAT';
        $ctx->watermark = 'PUSAT';
    }

    // 🟢 CABANG
    protected static function cabang(self $ctx): void
    {
        $ctx->canViewHarga      = true;
        $ctx->canViewTabungan   = true;
        $ctx->canViewPembayaran = true;
        $ctx->canViewAgent      = true;

        $ctx->roleLabel = 'CABANG';
        $ctx->watermark = 'CABANG';
    }

    // 🟠 AGENT
    protected static function agent(self $ctx): void
    {
        $ctx->canViewHarga      = false;
        $ctx->canViewTabungan   = false;
        $ctx->canViewPembayaran = false;
        $ctx->canViewAgent      = false;

        $ctx->roleLabel = 'AGENT';
        $ctx->watermark = 'AGENT';
    }

    // 🔒 DEFAULT
    protected static function restricted(self $ctx): void
    {
        $ctx->roleLabel = 'RESTRICTED';
        $ctx->watermark = 'RESTRICTED';
    }
}
