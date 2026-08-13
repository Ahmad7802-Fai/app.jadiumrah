<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\TabunganTopup;
use App\Services\TabunganService;
use App\Services\TabunganClosingService;
use Exception;

class TabunganTopupController extends Controller
{
    protected TabunganService $tabunganService;

    public function __construct(TabunganService $tabunganService)
    {
        $this->tabunganService = $tabunganService;
    }

    /* ==========================================================
     | LIST TOP UP
     ========================================================== */
    public function index(Request $request)
    {
        $status = strtoupper($request->get('status', 'PENDING'));

        abort_unless(
            in_array($status, ['PENDING', 'VALID', 'REJECTED']),
            400,
            'Status tidak valid.'
        );

        $topups = TabunganTopup::with([
                'jamaah',
                'tabungan',
                'transaksi.buktiSetoran',
            ])
            ->where('status', $status)
            ->orderBy('transfer_date', 'desc')
            ->orderBy('id', 'desc') // fallback kalau tanggal sama
            ->paginate(20);

        // 🔒 Inject lock flag (SINGLE SOURCE OF TRUTH)
        $topups->getCollection()->transform(function ($topup) {

            if (!$topup->transfer_date) {
                $topup->is_locked = false;
                return $topup;
            }

            $bulan = (int) date('m', strtotime($topup->transfer_date));
            $tahun = (int) date('Y', strtotime($topup->transfer_date));

            $topup->is_locked = TabunganClosingService::isLocked($bulan, $tahun);

            return $topup;
        });

        return view('keuangan.tabungan.topup.index', [
            'topups' => $topups,
            'status' => $status,
        ]);
    }

    /* ==========================================================
     | APPROVE TOP UP
     ========================================================== */
    public function approve(int $id)
    {
        try {
            $topup = TabunganTopup::findOrFail($id);

            $bulan = (int) date('m', strtotime($topup->transfer_date));
            $tahun = (int) date('Y', strtotime($topup->transfer_date));

            abort_if(
                TabunganClosingService::isLocked($bulan, $tahun),
                403,
                'Bulan transaksi sudah ditutup.'
            );

            $this->tabunganService->approveTopup(
                $id,
                auth()->id(),
                'ADMIN'
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Top up berhasil disetujui.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /* ==========================================================
     | REJECT TOP UP
     ========================================================== */
    public function reject(Request $request, int $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        try {
            $topup = TabunganTopup::findOrFail($id);

            $bulan = (int) date('m', strtotime($topup->transfer_date));
            $tahun = (int) date('Y', strtotime($topup->transfer_date));

            abort_if(
                TabunganClosingService::isLocked($bulan, $tahun),
                403,
                'Bulan transaksi sudah ditutup.'
            );

            $this->tabunganService->rejectTopup(
                $id,
                auth()->id(),
                $request->admin_note
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Top up berhasil ditolak.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /* ==========================================================
     | DOWNLOAD BUKTI TRANSFER
     ========================================================== */
    public function download(int $id)
    {
        $topup = TabunganTopup::findOrFail($id);

        abort_if(empty($topup->proof_file), 404, 'Bukti transfer tidak ditemukan.');
        abort_if(!Storage::disk('public')->exists($topup->proof_file), 404, 'File bukti tidak tersedia.');

        return Storage::disk('public')->download($topup->proof_file);
    }

    /* ==========================================================
     | RESEND WHATSAPP
     ========================================================== */
    public function resendWa(int $id)
    {
        try {
            $topup = TabunganTopup::findOrFail($id);

            if ($topup->status === 'VALID') {
                dispatch(new \App\Jobs\SendWaToJamaahJob($topup->id));
            } elseif ($topup->status === 'REJECTED') {
                dispatch(new \App\Jobs\SendWaRejectTopupJob($topup->id));
            } else {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'WA hanya bisa dikirim untuk status VALID / REJECTED'
                ], 422);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'WA berhasil dikirim ulang'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
