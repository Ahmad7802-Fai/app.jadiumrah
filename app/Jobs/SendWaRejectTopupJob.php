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

class SendWaRejectTopupJob implements ShouldQueue
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
        $topup = TabunganTopup::with('jamaah')
            ->findOrFail($this->topupId);

        /* ======================================================
         | SAFETY GUARDS
         ====================================================== */

        if ($topup->status !== 'REJECTED') {
            Log::warning('WA REJECT SKIPPED — STATUS NOT REJECTED', [
                'topup_id' => $topup->id,
                'status'   => $topup->status,
            ]);
            return;
        }

        // mute duplicate
        if ($topup->wa_rejected_at) {
            Log::info('WA REJECT SKIPPED — ALREADY SENT', [
                'topup_id' => $topup->id,
            ]);
            return;
        }

        $jamaah = $topup->jamaah;

        if (!$jamaah || !$jamaah->no_hp) {
            Log::warning('WA REJECT SKIPPED — NO PHONE', [
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
            Log::warning('WA REJECT SKIPPED — INVALID PHONE', [
                'phone' => $jamaah->no_hp,
            ]);
            return;
        }

        $reason = $topup->admin_note ?: 'Tidak ada keterangan dari admin.';

        /* ======================================================
         | MESSAGE
         ====================================================== */
        $message =
            "❌ *TOP UP DITOLAK*\n\n" .
            "Assalamu’alaikum {$jamaah->nama_lengkap},\n\n" .
            "Mohon maaf, top up tabungan umrah Anda *DITOLAK*.\n\n" .
            "💰 Nominal: Rp " . number_format($topup->amount, 0, ',', '.') . "\n" .
            "📝 Alasan:\n{$reason}\n\n" .
            "Silakan lakukan top up ulang sesuai ketentuan.\n" .
            "Terima kasih 🙏";

        /* ======================================================
         | SEND + ATOMIC LOGGING
         ====================================================== */
        DB::transaction(function () use ($wa, $topup, $phone, $message) {

            // kirim WA (exception → retry)
            $wa->send($phone, $message);

            // tandai WA terkirim
            $topup->update([
                'wa_rejected_at' => now(),
            ]);

            // audit log
            DB::table('wa_logs')->insert([
                'topup_id'   => $topup->id,
                'jamaah_id'  => $topup->jamaah_id,
                'phone'      => $phone,
                'type'       => 'REJECT',
                'status'     => 'SUCCESS',
                'message'    => $message,
                'created_at' => now(),
            ]);
        });

        Log::info('WA REJECT SENT', [
            'topup_id' => $topup->id,
            'to'       => $phone,
        ]);
    }

    /**
     * CALLED AFTER ALL RETRIES FAILED
     */
    public function failed(Throwable $e): void
    {
        Log::critical('WA REJECT FAILED AFTER RETRY', [
            'topup_id' => $this->topupId,
            'error'    => $e->getMessage(),
        ]);

        DB::table('wa_logs')->insert([
            'topup_id'   => $this->topupId,
            'jamaah_id'  => optional(
                \App\Models\TabunganTopup::find($this->topupId)
            )->jamaah_id,
            'type'       => 'REJECT',
            'status'     => 'FAILED',
            'error'      => $e->getMessage(),
            'created_at' => now(),
        ]);
    }
}
