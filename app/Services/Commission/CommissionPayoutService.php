<?php

namespace App\Services\Commission;

use App\Models\CommissionLog;
use App\Models\CommissionPayout;
use App\Models\CommissionPayoutItem;
use Illuminate\Support\Facades\DB;
use App\Services\CodeGeneratorService;

class CommissionPayoutService
{
    public function __construct(
        protected CodeGeneratorService $codeService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | REQUEST PAYOUT (Agent / Branch)
    |--------------------------------------------------------------------------
    */
    public function request(array $data): CommissionPayout
    {
        return DB::transaction(function () use ($data) {

            $user = auth()->user();

            $logs = CommissionLog::whereIn('id', $data['log_ids'])
                ->whereDoesntHave('payoutItems') // 🔥 prevent double payout
                ->lockForUpdate()
                ->get();

            if ($logs->isEmpty()) {
                throw new \Exception('Tidak ada komisi valid untuk dipayout.');
            }

            $total = $logs->sum('agent_amount');

            $code = $this->codeService->generate(
                prefix: 'CPO',
                entity: 'commission_payout',
                pad: 5,
                yearly: true
            );

            $payout = CommissionPayout::create([
                'payout_code' => $code,
                'agent_id'    => $logs->first()->agent_id,
                'branch_id'   => $logs->first()->branch_id,
                'total_amount'=> $total,
                'status'      => 'request',
                'created_by'  => $user->id,
            ]);

            foreach ($logs as $log) {
                CommissionPayoutItem::create([
                    'commission_payout_id' => $payout->id,
                    'commission_log_id'    => $log->id,
                    'amount'               => $log->agent_amount,
                ]);
            }

            return $payout;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE PAYOUT (Finance)
    |--------------------------------------------------------------------------
    */
    public function approve(CommissionPayout $payout): CommissionPayout
    {
        return DB::transaction(function () use ($payout) {

            if ($payout->status !== 'request') {
                return $payout;
            }

            $payout->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return $payout;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MARK AS PAID
    |--------------------------------------------------------------------------
    */
    public function markAsPaid(CommissionPayout $payout): CommissionPayout
    {
        return DB::transaction(function () use ($payout) {

            if ($payout->status !== 'approved') {
                return $payout;
            }

            $payout->update([
                'status'  => 'paid',
                'paid_by' => auth()->id(),
                'paid_at' => now(),
            ]);

            return $payout;
        });
    }
}