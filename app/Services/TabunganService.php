<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\TabunganUmrah;
use App\Models\TabunganTopup;
use App\Models\TabunganTransaksi;
use App\Models\JamaahNotification;
use App\Models\BuktiSetoran;
use App\Services\TabunganClosingService;
use Carbon\Carbon;
use App\Services\BuktiNumber;
use App\Jobs\SendWaToJamaahJob;
use App\Jobs\SendWaRejectTopupJob;

class TabunganService
{
    /* ==========================================================
     | APPROVE TOPUP (ADMIN / WA)
     | PROFESSIONAL MODE – AUTO PDF + WA
     ========================================================== */
public function approveTopup(
    int $topupId,
    ?int $verifiedBy = null,
    string $source = 'ADMIN'
): void {

    DB::transaction(function () use ($topupId, $verifiedBy, $source) {

        /* ======================================================
         | 1️⃣ LOCK & VALIDATE TOPUP
         ====================================================== */
        $topup = TabunganTopup::with(['tabungan', 'jamaah'])
            ->lockForUpdate()
            ->findOrFail($topupId);

        if ($topup->status !== 'PENDING') {
            throw new Exception('Top up sudah diproses.');
        }

        if (!$topup->tabungan || $topup->tabungan->status !== 'ACTIVE') {
            throw new Exception('Tabungan tidak aktif atau tidak ditemukan.');
        }

        /* ======================================================
         | 2️⃣ MONTHLY CLOSING GUARD (BANK GRADE)
         ====================================================== */
        $bulan = (int) date('m', strtotime($topup->transfer_date));
        $tahun = (int) date('Y', strtotime($topup->transfer_date));

        abort_if(
            TabunganClosingService::isLocked($bulan, $tahun),
            403,
            'Bulan transaksi sudah ditutup.'
        );

        /* ======================================================
         | 3️⃣ UPDATE STATUS TOPUP
         ====================================================== */
        $topup->update([
            'status'         => 'VALID',
            'verified_by'    => $verifiedBy,
            'verified_at'    => now(),
            'wa_verified_at' => $source === 'WA' ? now() : null,
        ]);

        /* ======================================================
         | 4️⃣ UPDATE SALDO TABUNGAN
         ====================================================== */
        $tabungan = $topup->tabungan;
        $saldoSebelum = $tabungan->saldo;

        $tabungan->increment('saldo', $topup->amount);
        $tabungan->refresh();

        /* ======================================================
         | 5️⃣ LEDGER (TABUNGAN_TRANSAKSI)
         ====================================================== */
        $transaksi = TabunganTransaksi::create([
            'tabungan_id'    => $tabungan->id,
            'jamaah_id'      => $topup->jamaah_id,
            'jenis'          => 'TOPUP',
            'amount'         => $topup->amount,
            'saldo_sebelum'  => $saldoSebelum,
            'saldo_setelah'  => $tabungan->saldo,
            'reference_type' => 'TOPUP',
            'reference_id'   => $topup->id,
            'keterangan'     => $source === 'WA'
                ? 'Top up disetujui via WhatsApp'
                : 'Topup Tabungan Umrah Alhmadulillah sudah berhasil !!',
            'created_by'     => $verifiedBy,
            'created_at'     => now(),
        ]);

        /* ======================================================
         | 6️⃣ BUKTI SETORAN (IMMUTABLE DATA)
         ====================================================== */
        $nomorBukti = BuktiNumber::generate();
        $approvedAt = now();

        $hash = hash('sha256',
            $nomorBukti .
            $tabungan->id .
            $topup->amount .
            $approvedAt
        );

        $buktiId = BuktiSetoran::create([
            'nomor_bukti'           => $nomorBukti,
            'tabungan_transaksi_id' => $transaksi->id,
            'jamaah_id'             => $topup->jamaah_id,
            'tabungan_id'           => $tabungan->id,
            'nominal'               => $topup->amount,
            'tanggal_setoran'       => $approvedAt->toDateString(),
            'approved_by'           => $verifiedBy,
            'approved_at'           => $approvedAt,
            'hash'                  => $hash,
        ])->id;

        /* ======================================================
         | 7️⃣ RELOAD DATA (ANTI PDF BUG)
         ====================================================== */
        $bukti = BuktiSetoran::with([
            'jamaah',
            'tabungan',
            'tabunganTransaksi'
        ])->findOrFail($buktiId);

        /* ======================================================
         | 8️⃣ GENERATE PDF (IMMUTABLE FILE)
         ====================================================== */
        $pdfPath = 'bukti-setoran/bukti-' . $bukti->id . '.pdf';

        if (!Storage::disk('local')->exists($pdfPath)) {
            $pdf = Pdf::loadView(
                'keuangan.tabungan.bukti-setoran-pdf',
                [
                    'bukti'    => $bukti,
                    'jamaah'   => $bukti->jamaah,
                    'tabungan' => $bukti->tabungan,
                ]
            )->setPaper('A4', 'portrait');

            Storage::disk('local')->put($pdfPath, $pdf->output());
        }

        /* ======================================================
         | 9️⃣ NOTIFIKASI INTERNAL
         ====================================================== */
        JamaahNotification::create([
            'jamaah_id' => $topup->jamaah_id,
            'title'     => 'Top Up Berhasil',
            'message'   => 'Top up sebesar Rp '
                . number_format($topup->amount, 0, ',', '.')
                . ' telah disetujui dan masuk ke saldo tabungan.',
            'is_read'   => 0,
            'created_at'=> now(),
        ]);

        /* ======================================================
         | 🔟 WA AFTER COMMIT (SAFE)
         ====================================================== */
        DB::afterCommit(function () use ($bukti) {
            SendWaToJamaahJob::dispatch($bukti->id);
        });
    });
}

    /* ==========================================================
     | REJECT TOPUP (ADMIN)
     ========================================================== */
    public function rejectTopup(
        int $topupId,
        int $adminId,
        string $adminNote
    ): void {

        DB::transaction(function () use ($topupId, $adminId, $adminNote) {

            $topup = TabunganTopup::lockForUpdate()->findOrFail($topupId);

            if ($topup->status !== 'PENDING') {
                throw new Exception('Top up sudah diproses.');
            }

            $bulan = (int) date('m', strtotime($topup->transfer_date));
            $tahun = (int) date('Y', strtotime($topup->transfer_date));

            abort_if(
                TabunganClosingService::isLocked($bulan, $tahun),
                403,
                'Bulan transaksi sudah ditutup. Tidak dapat reject top up.'
            );

            $topup->update([
                'status'      => 'REJECTED',
                'verified_by' => $adminId,
                'verified_at' => now(),
                'admin_note'  => $adminNote,
            ]);

            JamaahNotification::create([
                'jamaah_id' => $topup->jamaah_id,
                'title'     => 'Top Up Ditolak',
                'message'   => 'Top up sebesar Rp '
                    . number_format($topup->amount, 0, ',', '.')
                    . ' ditolak. Catatan admin: ' . $adminNote,
                'is_read'   => 0,
                'created_at'=> now(),
            ]);

            DB::afterCommit(function () use ($topup) {
                SendWaRejectTopupJob::dispatch($topup->id);
            });
        });
    }

    /* ==========================================================
     | READ ONLY
     ========================================================== */
    public function getSaldo(int $tabunganId): int
    {
        return TabunganUmrah::where('id', $tabunganId)
            ->value('saldo') ?? 0;
    }

    /* ==========================================================
     | RESEND WHATSAPP (ADMIN)
     ========================================================== */
    public function resendWa(int $buktiId): void
    {
        SendWaToJamaahJob::dispatch($buktiId);
    }
}
