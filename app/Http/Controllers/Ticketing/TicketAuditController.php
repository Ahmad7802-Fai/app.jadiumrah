<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\TicketAuditLog;
use Illuminate\Http\Request;

class TicketAuditController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAuditLog');

        $logs = TicketAuditLog::query()
            ->when($request->entity_type, fn ($q) =>
                $q->where('entity_type', $request->entity_type)
            )
            ->when($request->entity_id, fn ($q) =>
                $q->where('entity_id', $request->entity_id)
            )
            ->when($request->action, fn ($q) =>
                $q->where('action', $request->action)
            )
            ->orderByDesc('id') // ✅ AMAN
            ->paginate(30)
            ->withQueryString();

        return view('ticketing.audit.index', compact('logs'));
    }
}
