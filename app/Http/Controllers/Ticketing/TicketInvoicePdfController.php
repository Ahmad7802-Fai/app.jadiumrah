<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\TicketInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class TicketInvoicePdfController extends Controller
{
    public function show(TicketInvoice $invoice)
    {
        $invoice->load([
            'pnr.routes',
            'items',
            'payments'
        ]);

        // ==================================================
        // BUILD ROUTE DESCRIPTION (FOR PDF)
        // ==================================================
        $routeLines = $invoice->pnr?->routes
            ? $invoice->pnr->routes
                ->sortBy('sector')
                ->map(function ($r) {

                    // Date
                    $date = Carbon::parse($r->departure_date)
                        ->format('d M Y');

                    // Time formatter (HH:MM)
                    $formatTime = fn ($t) => $t ? substr($t, 0, 5) : '';

                    $time = trim(
                        $formatTime($r->departure_time) .
                        ($r->arrival_time ? '–' . $formatTime($r->arrival_time) : '')
                    );

                    // Arrival day offset
                    $offset = $r->arrival_day_offset == 1 ? ' +1' : '';

                    // Flight number
                    $flight = $r->flight_number
                        ? strtoupper($r->flight_number) . ' '
                        : '';

                    // Route (CGK - JED => CGK → JED)
                    $route = str_replace('-', '→', $r->origin);
                    $route = preg_replace('/\s+→\s+/', ' → ', $route);
                    $route = strtoupper($route);

                    return "• {$flight}{$route} ({$date} | {$time}{$offset})";
                })
                ->implode("\n")
            : null;

        // ==================================================
        // PDF GENERATION
        // ==================================================
        $pdf = Pdf::loadView(
            'ticketing.invoice.pdf',
            [
                'invoice'    => $invoice,
                'routeLines' => $routeLines,
            ]
        )->setPaper('a4', 'portrait');

        return $pdf->stream(
            'Invoice-' . $invoice->invoice_number . '.pdf'
        );
    }
}

// namespace App\Http\Controllers\Ticketing;

// use App\Http\Controllers\Controller;
// use App\Models\TicketInvoice;
// use Barryvdh\DomPDF\Facade\Pdf;
// use Carbon\Carbon;

// class TicketInvoicePdfController extends Controller
// {
//     public function show(TicketInvoice $invoice)
//     {
//         $invoice->load([
//             'pnr.routes',
//             'items',
//             'payments'
//         ]);

//         // ===============================
//         // BUILD ROUTE DESCRIPTION
//         // ===============================
//         $routeLines = $invoice->pnr->routes
//             ->sortBy('sector')
//             ->map(function ($r) {

//                 $date = Carbon::parse($r->departure_date)
//                     ->format('d M Y');

//                 $time = trim(
//                     ($r->departure_time ?? '') .
//                     ($r->arrival_time ? '–' . $r->arrival_time : '')
//                 );

//                 $offset = $r->arrival_day_offset == 1 ? ' +1' : '';

//                 $flight = $r->flight_number
//                     ? $r->flight_number . ' '
//                     : '';

//                 // ubah "CGK - JED" => "CGK → JED"
//                 $route = str_replace('-', '→', $r->origin);
//                 $route = preg_replace('/\s+→\s+/', ' → ', $route);

//                 return "• {$flight}{$route} ({$date} | {$time}{$offset})";
//             })
//             ->implode("\n");

//         // ===============================
//         // PDF
//         // ===============================
//         $pdf = Pdf::loadView(
//             'ticketing.invoice.pdf',
//             [
//                 'invoice'    => $invoice,
//                 'routeLines' => $routeLines,
//             ]
//         )->setPaper('a4', 'portrait');

//         return $pdf->stream(
//             'Invoice-' . $invoice->invoice_number . '.pdf'
//         );
//     }
// }

// namespace App\Http\Controllers\Ticketing;

// use App\Http\Controllers\Controller;
// use App\Models\TicketInvoice;
// use Barryvdh\DomPDF\Facade\Pdf;

// class TicketInvoicePdfController extends Controller
// {
//     public function show(TicketInvoice $invoice)
//     {
//         $invoice->load([
//             'pnr.routes',
//             'items',
//             'payments'
//         ]);

//         $pdf = Pdf::loadView(
//             'ticketing.invoice.pdf',
//             compact('invoice')
//         )->setPaper('a4', 'portrait');

//         return $pdf->stream(
//             'Invoice-'.$invoice->invoice_number.'.pdf'
//         );
//     }
// }
