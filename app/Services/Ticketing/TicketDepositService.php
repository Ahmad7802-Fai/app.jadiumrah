<?php

namespace App\Services\Ticketing;

use App\Models\TicketDeposit;
use Illuminate\Support\Facades\DB;
use Exception;

class TicketDepositService
{
    public function create(array $data): TicketDeposit
    {
        return TicketDeposit::create([
            'pnr_id'         => $data['pnr_id'],
            'agent_id'       => $data['agent_id'] ?? null,
            'branch_id'      => $data['branch_id'] ?? null,
            'amount'         => $data['amount'],
            'bank_recipient' => $data['bank_recipient'] ?? null,
            'sender'         => $data['sender'] ?? null,
            'transfer_date'  => $data['transfer_date'] ?? null,
            'receipt_file'   => $data['receipt_file'] ?? null,
            'status'         => 'PENDING',
            'source'         => $data['source'] ?? 'TOPUP',
        ]);
    }

    public function approve(int $depositId): void
    {
        DB::transaction(function () use ($depositId) {

            $deposit = TicketDeposit::lockForUpdate()
                ->findOrFail($depositId);

            if ($deposit->status !== 'PENDING') {
                throw new Exception('Deposit sudah diproses');
            }

            $deposit->update([
                'status' => 'APPROVED'
            ]);
        });
    }

    public function getAvailableBalance(int $agentId): int
    {
        $deposit = TicketDeposit::where('agent_id', $agentId)
            ->where('status', 'APPROVED')
            ->sum('amount');

        $allocated = DB::table('ticket_allocations')
            ->join('ticket_pnrs', 'ticket_pnrs.id', '=', 'ticket_allocations.pnr_id')
            ->where('ticket_pnrs.agent_id', $agentId)
            ->sum('allocated_amount');

        return max(0, $deposit - $allocated);
    }
}
