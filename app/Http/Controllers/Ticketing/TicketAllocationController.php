<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\TicketPnr;
use App\Services\Ticketing\TicketAllocationService;
use Illuminate\Http\Request;

class TicketAllocationController extends Controller
{
    public function __construct(
        protected TicketAllocationService $service
    ) {}

    public function store(Request $request, TicketPnr $pnr)
    {
        $this->authorize('update', $pnr);

        $request->validate([
            'amount' => 'required|numeric|min:1000'
        ]);

        $this->service->allocate($pnr->id, $request->amount);

        return back()->with('success', 'Dana berhasil dialokasikan');
    }

}
