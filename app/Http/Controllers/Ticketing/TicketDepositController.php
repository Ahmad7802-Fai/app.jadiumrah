<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\TicketDeposit;
use App\Services\Ticketing\TicketDepositService;
use Illuminate\Http\Request;

class TicketDepositController extends Controller
{
    public function __construct(
        protected TicketDepositService $service
    ) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'pnr_id'    => 'required|exists:ticket_pnrs,id',
            'amount'    => 'required|numeric|min:1000',
        ]);

        $this->service->create($data);

        return back()->with('success', 'Deposit berhasil dicatat');
    }

    public function approve(TicketDeposit $deposit)
    {
        $this->authorize('update', $deposit);

        $this->service->approve($deposit->id);

        return back()->with('success', 'Deposit disetujui');
    }
}
