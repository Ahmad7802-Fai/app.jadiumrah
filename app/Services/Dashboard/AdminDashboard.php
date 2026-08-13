<?php

namespace App\Services\Dashboard;

use App\Models\{
    Team,
    Partner,
    Gallery,
    Testimoni,
    Berita,
    PaketUmrah
};

class AdminDashboard extends AbstractDashboard
{
    /* ===============================
       VIEW
    =============================== */
    public function view(): string
    {
        return 'dashboard.admin';
    }

    /* ===============================
       TITLE
    =============================== */
    protected function title(): string
    {
        return 'Dashboard Admin';
    }

    /* ===============================
       CARDS
       (ADMIN = KONTEN & MASTER DATA)
    =============================== */
    protected function buildCards(int $month, int $year): array
    {
        return [

            $this->card(
                'Total Team',
                Team::count(),
                'fa-users-cog'
            ),

            $this->card(
                'Partner',
                Partner::count(),
                'fa-handshake'
            ),

            $this->card(
                'Galeri',
                Gallery::count(),
                'fa-images'
            ),

            $this->card(
                'Testimoni',
                Testimoni::count(),
                'fa-comment-dots'
            ),

            $this->card(
                'Berita',
                Berita::count(),
                'fa-newspaper'
            ),

            $this->card(
                'Paket Umrah',
                PaketUmrah::count(),
                'fa-kaaba'
            ),
        ];
    }

    /* ===============================
       CHART UTAMA
       (Distribusi Konten)
    =============================== */
    protected function chartLabels(): array
    {
        return [
            'Team',
            'Partner',
            'Berita',
            'Galeri',
            'Testimoni',
            'Paket Umrah',
        ];
    }

    protected function chartThisMonth(int $month, int $year): array
    {
        // Admin tidak perlu filter bulan → ALL TIME
        return [
            Team::count(),
            Partner::count(),
            Berita::count(),
            Gallery::count(),
            Testimoni::count(),
            PaketUmrah::count(),
        ];
    }

    protected function chartLastMonth(int $month, int $year): array
    {
        // Untuk konsistensi struktur (biar chart tidak error)
        return [
            0, 0, 0, 0, 0, 0
        ];
    }

    /* ===============================
       CHART COMPARISON
       (ADMIN TIDAK BUTUH → SAFE FALLBACK)
    =============================== */
    protected function chartComparisonThisMonth(): array
    {
        return [0];
    }

    protected function chartComparisonLastMonth(): array
    {
        return [0];
    }
}
