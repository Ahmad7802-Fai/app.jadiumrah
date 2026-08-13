<?php

namespace App\Jobs;

use App\Models\TabunganTopup;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendWaToJamaahJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $topupId;

    /**
     * Retry policy
     */
    public int $tries = 5;
    public int $timeout = 15;

    public function __construct(int $topupId)
    {
        $this->topupId = $topupId;
    }

    /**
     * Exponential backoff (seconds)
     */
    public function backoff(): array
    {
        return [30, 120, 300, 900, 1800];
    }

    /**
     * HANDLE JOB
     */
    public function handle(WhatsAppService $wa): void
    {
        $topup = TabunganTopup::with(['jamaah', 'tabungan'])
            ->findOrFail($this->topupId);

        /* ======================================================
         | SAFETY GUARDS
         ====================================================== */

        // hanya VALID yang boleh kirim WA approve
        if ($topup->status !== 'VALID') {
            Log::warning('WA SKIPPED — TOPUP NOT VALID', [
                'topup_id' => $topup->id,
                'status'   => $topup->status,
            ]);
            return;
        }

        // mute duplicate
        if ($topup->wa_verified_at) {
            Log::info('WA SKIPPED — ALREADY SENT', [
                'topup_id' => $topup->id,
            ]);
            return;
        }

        $jamaah = $topup->jamaah;

        if (!$jamaah || !$jamaah->no_hp) {
            Log::warning('WA SKIPPED — NO PHONE', [
                'topup_id' => $topup->id,
            ]);
            return;
        }

        /* ======================================================
         | NORMALIZE PHONE
         ====================================================== */
        $phone = preg_replace('/[^0-9]/', '', $jamaah->no_hp);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            Log::warning('WA SKIPPED — INVALID PHONE', [
                'phone' => $jamaah->no_hp,
            ]);
            return;
        }

        /* ======================================================
         | MESSAGE
         ====================================================== */
        $message =
            "✅ *TOP UP BERHASIL*\n\n" .
            "Assalamu’alaikum {$jamaah->nama_lengkap},\n\n" .
            "Top up tabungan umrah Anda telah *BERHASIL* diverifikasi.\n\n" .
            "💰 Nominal: Rp " . number_format($topup->amount, 0, ',', '.') . "\n" .
            "💳 Saldo Sekarang: Rp " . number_format($topup->tabungan->saldo, 0, ',', '.') . "\n\n" .
            "Terima kasih telah menabung untuk umrah 🤲";

        /* ======================================================
         | SEND + ATOMIC LOGGING
         ====================================================== */
        DB::transaction(function () use ($wa, $topup, $phone, $message) {

            // kirim WA (kalau gateway error → throw → retry)
            $wa->send($phone, $message);

            // tandai WA terkirim (single source of truth)
            $topup->update([
                'wa_verified_at' => now(),
            ]);

            // audit log
            DB::table('wa_logs')->insert([
                'topup_id'   => $topup->id,
                'jamaah_id'  => $topup->jamaah_id,
                'phone'      => $phone,
                'type'       => 'APPROVE',
                'status'     => 'SUCCESS',
                'message'    => $message,
                'created_at' => now(),
            ]);
        });

        Log::info('WA APPROVE SENT', [
            'topup_id' => $topup->id,
            'to'       => $phone,
        ]);
    }

    /**
     * CALLED AFTER ALL RETRIES FAILED
     */
    public function failed(Throwable $e): void
    {
        Log::critical('WA APPROVE FAILED AFTER RETRY', [
            'topup_id' => $this->topupId,
            'error'    => $e->getMessage(),
        ]);

        DB::table('wa_logs')->insert([
            'topup_id'   => $this->topupId,
            'jamaah_id'  => optional(
                \App\Models\TabunganTopup::find($this->topupId)
            )->jamaah_id,
            'type'       => 'APPROVE',
            'status'     => 'FAILED',
            'error'      => $e->getMessage(),
            'created_at' => now(),
        ]);
    }
}
