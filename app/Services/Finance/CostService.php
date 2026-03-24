<?php

namespace App\Services\Finance;

use App\Models\Cost;
use App\Models\Booking;
use App\Models\PaketDeparture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CostService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE COST (PENDING)
    |--------------------------------------------------------------------------
    */
    public function create(array $data): Cost
    {
        return DB::transaction(function () use ($data) {

            $user = auth()->user();

            $proofPath = null;

            if (!empty($data['proof_file'])) {
                $proofPath = $data['proof_file']
                    ->store('cost-proofs', 'public');
            }

            return Cost::create([
                'cost_category_id'  => $data['cost_category_id'],
                'booking_id'        => $data['booking_id'] ?? null,
                'paket_departure_id'=> $data['paket_departure_id'] ?? null,
                'branch_id'         => $user->branch_id,
                'amount'            => $data['amount'],
                'description'       => $data['description'] ?? null,
                'proof_file'        => $proofPath,
                'cost_date'         => $data['cost_date'],
                'status'            => 'pending',
                'created_by'        => $user->id,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE COST (Hanya Pending)
    |--------------------------------------------------------------------------
    */
    public function update(Cost $cost, array $data): Cost
    {
        return DB::transaction(function () use ($cost, $data) {

            if ($cost->status !== 'pending') {
                return $cost;
            }

            $updateData = [
                'cost_category_id' => $data['cost_category_id'],
                'amount'           => $data['amount'],
                'description'      => $data['description'] ?? null,
                'cost_date'        => $data['cost_date'],
            ];

            /*
            |--------------------------------------------------------------------------
            | HANDLE PROOF REUPLOAD
            |--------------------------------------------------------------------------
            */
            if (!empty($data['proof_file'])) {

                if ($cost->proof_file &&
                    Storage::disk('public')->exists($cost->proof_file)
                ) {
                    Storage::disk('public')->delete($cost->proof_file);
                }

                $path = $data['proof_file']
                    ->store('cost-proofs', 'public');

                $updateData['proof_file'] = $path;
            }

            $cost->update($updateData);

            return $cost;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE COST
    |--------------------------------------------------------------------------
    */
    public function approve(Cost $cost): Cost
    {
        return DB::transaction(function () use ($cost) {

            if ($cost->status !== 'pending') {
                return $cost;
            }

            $cost->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $this->syncProfit($cost);

            return $cost;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT COST
    |--------------------------------------------------------------------------
    */
    public function reject(Cost $cost): Cost
    {
        $cost->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return $cost;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE COST
    |--------------------------------------------------------------------------
    */
    public function delete(Cost $cost): void
    {
        DB::transaction(function () use ($cost) {

            if ($cost->proof_file &&
                Storage::disk('public')->exists($cost->proof_file)
            ) {
                Storage::disk('public')->delete($cost->proof_file);
            }

            $cost->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 SYNC PROFIT (Departure Based)
    |--------------------------------------------------------------------------
    */
    protected function syncProfit(Cost $cost): void
    {
        if (!$cost->paket_departure_id) {
            return;
        }

        $departure = PaketDeparture::find($cost->paket_departure_id);

        if (!$departure) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL REVENUE
        |--------------------------------------------------------------------------
        */
        $totalRevenue = Booking::where('paket_departure_id', $departure->id)
            ->sum('total_amount');

        /*
        |--------------------------------------------------------------------------
        | TOTAL APPROVED COST
        |--------------------------------------------------------------------------
        */
        $totalCost = Cost::where('paket_departure_id', $departure->id)
            ->where('status', 'approved')
            ->sum('amount');

        $profit = $totalRevenue - $totalCost;

        /*
        |--------------------------------------------------------------------------
        | OPTIONAL: Simpan ke departure
        |--------------------------------------------------------------------------
        */
        $departure->update([
            'total_revenue' => $totalRevenue,
            'total_cost'    => $totalCost,
            'profit'        => $profit,
        ]);
    }
}