<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;

class DevReset extends Command
{
    /**
     * Command name
     */
    protected $signature = 'dev:reset {--force : Paksa jalan di production}';

    /**
     * Command description
     */
    protected $description = 'Reset database DEV + seed data resmi (SuperAdmin + Branch)';

    public function handle(): int
    {
        // ===============================
        // SAFETY CHECK
        // ===============================
        if (App::environment('production') && ! $this->option('force')) {
            $this->error('⛔ Command ini tidak boleh dijalankan di PRODUCTION!');
            $this->line('Gunakan --force jika benar-benar yakin.');
            return self::FAILURE;
        }

        $this->warn('⚠️ DATABASE AKAN DIRESET TOTAL!');
        if (! $this->confirm('Lanjutkan?')) {
            $this->info('❌ Dibatalkan.');
            return self::SUCCESS;
        }

        // ===============================
        // RESET DB
        // ===============================
        $this->info('🧨 Reset database...');
        Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true,
        ]);
        $this->line(Artisan::output());

        $this->info('✅ Dev reset selesai.');
        return self::SUCCESS;
    }
}
