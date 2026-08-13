<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\TabunganTopup;
use App\Services\TabunganService;
use Exception;

class TopupApprovalController extends Controller
{
    protected TabunganService $tabunganService;

    public function __construct(TabunganService $tabunganService)
    {
        $this->tabunganService = $tabunganService;
    }

    /**
     * ==========================================================
     * APPROVE TOP UP VIA WHATSAPP
     * ==========================================================
     */
    public function approve(string $token)
    {
        try {
            $topup = TabunganTopup::where('wa_token', $token)
                ->where('status', 'PENDING')
                ->firstOrFail();

            // 🔥 SATU-SATUNYA JALUR APPROVE
            $this->tabunganService->approveTopup(
                $topup->id,
                null,   // system
                'WA'
            );

            return view('wa.success');

        } catch (Exception $e) {
            return view('wa.failed', [
                'message' => $e->getMessage()
            ]);
        }
    }
}
