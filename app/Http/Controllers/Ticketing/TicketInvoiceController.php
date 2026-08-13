<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\TicketInvoice;
use App\Models\Client;
use App\Models\TicketPnr;
use App\Services\Ticketing\TicketInvoiceService;
use Illuminate\Http\Request;

class TicketInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::orderBy('nama')->get();

        $invoices = TicketInvoice::with('pnr.client')

            /* =========================
            | FILTER: CLIENT
            ========================= */
            ->when($request->client_id, function ($q) use ($request) {
                $q->whereHas('pnr', function ($qq) use ($request) {
                    $qq->where('client_id', $request->client_id);
                });
            })

            /* =========================
            | FILTER: STATUS
            ========================= */
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })

            /* =========================
            | FILTER: SEARCH
            | invoice number / pnr code
            ========================= */
            ->when($request->q, function ($q) use ($request) {
                $q->where(function ($qq) use ($request) {
                    $qq->where('invoice_number', 'like', "%{$request->q}%")
                    ->orWhereHas('pnr', function ($qp) use ($request) {
                        $qp->where('pnr_code', 'like', "%{$request->q}%");
                    });
                });
            })

            /* =========================
            | ORDERING
            ========================= */
            ->orderByDesc('created_at')

            ->paginate(20)
            ->withQueryString();

        return view(
            'ticketing.invoice.index',
            compact('invoices', 'clients')
        );
    }

public function show(TicketInvoice $invoice)
{
    $invoice->load([
        'pnr.client',
        'pnr.routes',
        'items',
        'payments',
        'refunds',
    ]);

    // ===============================
    // BUILD ROUTE LINES (SAMA DENGAN PDF)
    // ===============================
    $routeLines = $invoice->pnr?->routes
        ? $invoice->pnr->routes
            ->sortBy('sector')
            ->map(function ($r) {

                $date = \Carbon\Carbon::parse($r->departure_date)
                    ->format('d M Y');

                $formatTime = fn ($t) => $t ? substr($t, 0, 5) : '';

                $time = trim(
                    $formatTime($r->departure_time) .
                    ($r->arrival_time ? '–' . $formatTime($r->arrival_time) : '')
                );

                $offset = $r->arrival_day_offset == 1 ? ' +1' : '';

                $flight = $r->flight_number
                    ? strtoupper($r->flight_number) . ' '
                    : '';

                $route = str_replace('-', '→', $r->origin);
                $route = preg_replace('/\s+→\s+/', ' → ', $route);
                $route = strtoupper($route);

                return "{$flight}{$route} ({$date} | {$time}{$offset})";
            })
            ->implode("\n")
        : null;

    return view('ticketing.invoice.show', [
        'invoice'    => $invoice,
        'routeLines' => $routeLines,
    ]);
}


    public function storeFromPnr(
        TicketPnr $pnr,
        TicketInvoiceService $service
    ) {
        $invoice = $service->createFromPnr($pnr->id);

        return redirect()
            ->route('ticketing.invoice.show', $invoice)
            ->with('success', 'Invoice berhasil dibuat');
    }

}

